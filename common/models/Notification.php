<?php

namespace common\models;

use Codeception\Util\ReflectionHelper;
use common\components\Sms;
use common\helpers\StringHelper;
use DateInterval;
use DateTime;
use Yii;
use \common\models\base\Notification as BaseNotification;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "notification".
 */
class Notification extends BaseNotification
{
    public $added = false;

    const CREATE_NEW_USER = 'userCreate';
    const CREATE_NEW_CUSTOMER = 'customerCreate';
    const USER_NEW_TASK = 'userTaskAdd';
    const USER_ADDED_TO_EVENT = 'userEventAdd';
    const USER_REMOVED_FROM_EVENT = 'userEventRemove';
    const EVENT_GEAR_CHANGE = 'eventGearChange';
    const EVENT_SCHEDULE_CHANGE = 'eventScheduleChange';
    const READY_TO_INVOICE = 'readyToInvoice';
    const COSTS_ADDED = 'costsAdded';
    public $userIds;

    const PUSH_TYPE_NOTIFICATION = 1;
    const PUSH_TYPE_CHAT = 2;
    const PUSH_TYPE_CHECKLIST = 3;
    const PUSH_TYPE_TASKS = 4;
    const PUSH_TYPE_EVENTS = 5;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'userIds',
            ],
            'relations' => [
                'users',
            ],
            'modelClasses'=>[
                'common\models\User',
            ],
        ];

        return $behaviors;

    }
    public function rules()
    {
        $rules = [
            [['userIds'], 'each', 'rule'=>['integer']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    /**
     * @param $name
     * @return static Notification
     */
    public static function getByName($name)
    {
        return static::loadByParams(['name'=>$name]);
    }

    public function getRecipients()
    {
        $query = $this->getUsers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function sendMail($email, $title='', $data=[])
    {
        $params = Yii::$app->params;
        if ($this->sendEmailEnabled() == true)
        {
            $subject = '[New Event Management] '.Yii::t('app', 'Wiadomość automatyczna.');

            $users = User::findAll(['email'=>$email]);

            if ($this->added == false)
            {
                $this->addUserNotification($users, ['mail'=>1], $data['obj']);
            }
            $body = StringHelper::parseText($this->content, $data);



            $sent = Yii::$app->mailer->compose('message', [
                'title'=>$title,
                'body'=>StringHelper::parseText($this->content, $data),
            ])
                ->setTo($email)
                    ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main')])
                ->setBcc(Yii::$app->params['adminEmail'])
                ->setSubject($subject)
                ->send();
            Yii::info([$sent, Yii::$app->params], 'Mailer');
            return $sent;
        }
        return true;

    }

    protected function sendEmailEnabled()
    {
        $enabled = false;

        $params = Yii::$app->params;
        if ($params['notification.enabledMail'] == true && $this->mail == 1)
        {
            $enabled = true;
        }
        return $enabled;
    }

    public function addUserNotification($users, $attrs = [], $obj=null)
    {
        foreach ($users as $user)
        {
            $model = new UserNotification([
                'user_id'=>$user->id,

            ]);
            if ($obj===null)
            {
                $obj = $this;
            }

            $model->target_class = get_class($obj);
            $model->target_id = $obj->id;
            $model->attributes = $attrs;
            $model->content = $this->content;
            $model->title = $this->label;
            $model->save();
        }
    }

    public function addForUsers($users, $data=[], $attrs = [])
    {
        foreach ($users as $user)
        {
            $model = new UserNotification([
                'user_id'=>$user->id,

            ]);
            $obj = $data['obj'];
            if ($obj===null)
            {
                $obj = $this;
            }


            $model->target_class = get_class($obj);
            //FIXME: Dla złożonych kluczy, trzeba zmienić pole w tabeli.
//            $model->target_id = serialize($obj->getPrimaryKey(true));
            $model->attributes = $attrs;
            $model->content = $this->content;
            $model->title = $this->label;
            $model->data = serialize($data);
            $model->save();
        }
    }

    public function getPlaceholderMap()
    {
        $map = [
            'name' => $this->title,
        ];

        return $map;
    }

    public static function sendUserNotifications(User $user, $notification_name, $data) {
        $settings = self::getByName($notification_name);
        $time = new DateTime();
        if ($settings->sms && $user->phone){
            self::sendUserSmsNotification($user, StringHelper::parseText($settings->content, $data), true);
        }
        if ($settings->mail && $user->email) {
            $subject = $settings->title;
            if ($settings->title == null) {
                $subject = Yii::t('app', 'Wiadomość automatyczna');
            }
            $body = StringHelper::parseText($settings->content, $data);
            if ($notification_name== self::USER_ADDED_TO_EVENT) {
                $settings = Settings::find()->indexBy('key')->all(); 
                if ((isset($settings['crewConfirm']))&&($settings['crewConfirm']->value==1)){
                    $body.='<p><a style="padding:14px 25px; color:white; background-color:#1ab394; border-radius:21px; text-transform:uppercase; font-size:12px; letter-spacing:1px; text-decoration:none;" target="_blank" href="http://'.Yii::$app->getRequest()->serverName.'/admin/event-user/confirm?event_id='.$data[1]->event_id.'&user_id='.$data[1]->user_id.'&id='.$data[1]->getHash().'">'.Yii::t('app', 'Potwierdź udział').'</a></p>';
                }
                }
            self::sendUserMailNotification($user, $subject, $body);
        }
        if ($settings->push) {
            $content = str_replace("{link}","",$settings->content);
            $content = str_replace("<p>","",$content);
            $content = str_replace("</p>","",$content);
        	self::sendUserPushNotification($user, Yii::t('app', 'New Event Management'), StringHelper::parseText($settings->content, $data), self::getPushType($notification_name), $data[0]->id);
        }
    }

    public static function sendUserNotificationsType(User $user, $notification_name, $data, $type) {
        $settings = self::getByName($notification_name);
        $time = new DateTime();
        if (($type=="sms") && ($user->phone)){
            $time = $time->format("Y-m-d H:i:s");
            self::sendUserSmsNotification($user, StringHelper::parseText($settings->content, $data), $time);
        }
        if (($type=="mail") && ($user->email)) {
            $subject = $settings->title;
            if ($settings->title == null) {
                $subject = Yii::t('app', 'Wiadomość automatyczna');
            }
            self::sendUserMailNotification($user, $subject, StringHelper::parseText($settings->content, $data));
        }
        if ($type=="push"){
            $content = str_replace("{link}","",$settings->content);
            $content = str_replace("<p>","",$content);
            $content = str_replace("</p>","",$content);
            self::sendUserPushNotification($user, Yii::t('app', 'Powiadomienie z serwisu eventowego'), StringHelper::parseText($content, $data), self::getPushType($notification_name), $data[0]->id);
        }
    }

    public static function getPushType($name) {
    	switch ($name) {
		    case 'userCreate': return self::PUSH_TYPE_EVENTS;
		    case 'customerCreate': return self::PUSH_TYPE_EVENTS;
		    case 'userTaskAdd': return self::PUSH_TYPE_TASKS;
		    case 'userEventAdd': return self::PUSH_TYPE_EVENTS;
		    case 'userEventRemove': return self::PUSH_TYPE_EVENTS;
		    case 'eventGearChange': return self::PUSH_TYPE_EVENTS;
		    case 'eventScheduleChange': return self::PUSH_TYPE_EVENTS;
		    case 'readyToInvoice': return self::PUSH_TYPE_EVENTS;
		    case 'costsAdded': return self::PUSH_TYPE_EVENTS;
	    }
	    return null;
    }

    public static function sendUserSmsNotification(User $user, $text, $time) {
        if ($user->phone) {
            $sms = new NotificationSms();
            $sms->text = $text;
            $sms->user_id = $user->id;
            $sms->sending_time = $time;
            if ($time === true) {
                $sms->sending_time = date("Y-m-d H:i:s");
            }
            $sms->send(true);
        }
    }

    public static function sendUserMailNotification(User $user, $subject, $text) {
        $sender = 'New Event Management';
        $sender = Yii::$app->settings->get('companyName', 'main');
        Yii::$app->mailer->compose('mailNotification', [
            'title' => $subject,
            'content' => $text
        ])
            ->setFrom([
                    Yii::$app->params['mailingEmail'] => $sender,
                ])
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();
    }

    public static function sendUserPushNotification(User $user, $subject, $text, $type, $object_id=null, $user_from = null) {
    	$devices = Device::find()->where(['user_id' => $user->id])->all();
    	foreach ($devices as $device) {
    		$device->sendPush($subject, $text, $type, $object_id, $user_from);
	    }
    }

    public static function sendCustomerNotification(Customer $customer, $notification_name, $data) {
        $settings = self::getByName($notification_name);
        if ($settings->sms && $customer->phone){
            $smsEnabled = Yii::$app->params['smsEnabled'];
            $response = null;
            try {
                Sms::load()->messages->sendSms(
                $customer->phone,
                strip_tags(StringHelper::parseText($settings->content, $data)),
                Sms::getSender(),
                [
                    'test' => $smsEnabled ? false : true,
                    'details'=>true,
                    'date' => null,
                ]);
            }
            catch (\Exception $e) {
                $response = $customer->company.'; '.$customer->phone.'; '.$settings->text.'; '.$e->getMessage();
                Yii::error($response, 'sms.error');
            }
        }
        if ($settings->mail && $customer->email) {
            $subject = $settings->title;
            if ($settings->title == null) {
                $subject = Yii::t('app', 'Wiadomość automatyczna');
            }
            Yii::$app->mailer->compose('mailNotification', ['title'=>$subject, 'content'=>StringHelper::parseText($settings->content, $data)])
                ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
                ->setTo($customer->email)
                ->setSubject($subject)
                ->send();
        }
    }

    public function getPlaceholders() {
        if ($this->name == self::CREATE_NEW_USER) {
            return "{username}, {imie}, {nazwisko}, {tel}, {mail}, {password}, {link}";
        }
        if ($this->name == self::CREATE_NEW_CUSTOMER) {
            return "{name}, {tel}, {mail}";
        }
        if ($this->name == self::USER_ADDED_TO_EVENT) {
            return "{crewCreator}, {name}, {timeStart}, {timeEnd}, {link}";
        }
        if ($this->name == self::USER_REMOVED_FROM_EVENT) {
            return "Event: {name}, {timeStart}, {timeEnd}, {link}. User: {username}, {imie}, {nazwisko}, {tel}, {mail}";
        }
        if ($this->name == self::USER_NEW_TASK) {
            return "Task: {tytul}, {opis}, {termin}, {creator.username}, {creator.imie}, {creator.nazwisko}. User: {username}, {imie}, {nazwisko}, {tel}, {mail}";
        }
        if ($this->name == self::EVENT_GEAR_CHANGE) {
            return "Event: {name}, {timeStart}, {timeEnd}, {link}. Sprzęt: {gear.name}, {gear.timeStart}, {gear.timeEnd}. Kto dodał: {username}, {imie}, {nazwisko}, {tel}, {mail}";
        }
        if ($this->name == self::EVENT_SCHEDULE_CHANGE) {
            return "{name}, {timeStart}, {timeEnd}, {link}";
        }
        if ($this->name == self::READY_TO_INVOICE) {
            return "{name}, {link}";
        }
        if ($this->name == self::COSTS_ADDED) {
            return "{name}, {link}";
        }
    }
}
