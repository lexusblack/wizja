<?php

namespace common\models;

use common\components\Sms;
use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\EventMessage as BaseEventMessage;

/**
 * This is the model class for table "event_message".
 */
class EventMessage extends BaseEventMessage
{
    public function send()
    {
        if ($this->validate())
        {
            //zebrać odbiorców, wysłać, ustawić status, zapisać
            if ($this->sms == 1)
            {
                $this->sendSms();
            }

            if ($this->email == 1)
            {
                if ($this->sendEmail())
                {
                    $this->status = 10;
                }
            }
            if ($this->push == 1)
            {
                //$this->sendPush();
                
            }

            $this->save(false);

            return true;
        }

        return false;
    }

    public function getEmailRecipients()
    {
        $recipietns = [];
        $model = $this->event;
        //user,customer, project manger

        $users = $model->getUsers()->select('email')->column();
        $projectManger = $model->manager===null ? [] : [$model->manager->email];

        $customer = [];
        if ($model->customer !== null && $model->customer->contacts !== null)
        {
            //!!!: Klienci wyłączeni
//            $customer = $model->customer->getContacts()->select('email')->column();
        }

        $recipietns = ArrayHelper::merge($users, $projectManger, $customer);
        $this->recipients_email = implode(';', $recipietns);

        return $recipietns;
    }

    public function sendPush()
    {
        $users = $model->getUsers();
        foreach ($users as $user)
        {
            Notification::sendUserPushNotification($user, $this->getUserName(), $this->text, 2, $this->chat_id, $this->user_from);
        }
        
    }
    


    public function sendEmail()
    {
        $recipients = $this->getEmailRecipients();
        $subject = '['.Yii::t('app', 'Wydarzenia automat').'] '.$this->title;
        $this->content = str_replace( "\n", "<br />",$this->content);
        return Yii::$app->mailer->compose('message', ['model'=>$this])
            ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
            ->setTo($recipients)
            ->setSubject($subject)
            ->send();
    }

    public function getSmsRecipients()
    {
        $recipietns = [];
        $model = $this->event;

        //user,customer, project manger

        $users = $model->getUsers()->select('phone')->column();
        $projectManger = $model->manager===null ? [] : [$model->manager->phone];

        $customer = [];
        if ($model->customer !== null && $model->customer->contacts !== null)
        {
            //!!!: Klienci wyłączeni
//            $customer = $model->customer->getContacts()->select('phone')->column();
        }

        $recipietns = ArrayHelper::merge($users, $projectManger, $customer);
        $tmp = [];

        foreach ($recipietns as $r)
        {
            preg_match_all('@\+?[\d\-\ ]+@', $r, $match);
            $m = array_map('trim', $match[0]);
            $tmp = array_filter($tmp);
            $tmp = ArrayHelper::merge($tmp, $m);
        }
        $recipietns = array_unique($tmp);

        $this->recipients_sms = implode(';', $recipietns);

        return $recipietns;
    }

    public function sendSms()
    {
        $model = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $smsEnabled = $model->sms;
        if ($model->sms_sender)
        {
            $sender = $model->sms_sender;
        }else{
            $sender = null;
        }
        $response = '';
        $recipients = $this->getSmsRecipients();
        if (empty($recipients) == true)
        {
            Yii::info(Yii::t('app', 'Brak odbiorców'), 'sms.info');
            return;
        }
        $smsContent = $this->title.'; '.$this->content;
        try
        {
            $response = Sms::load()->messages->sendSms(
                $recipients,
                $smsContent,
                Sms::getSender(),
                [
                    'test' => $smsEnabled ? false : true,
                    'sender'=>$sender,
                    'details'=>true,
                    'date' => null,
                ]);
        }
        catch (\Exception $e)
        {
            $response = $recipients.'; '.$smsContent.'; '.$e->getMessage();
            Yii::error($response, 'sms.error');
        }
        Yii::info(ArrayHelper::toArray($response), 'sms.response');
    }
}
