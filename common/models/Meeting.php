<?php

namespace common\models;

use common\helpers\ArrayHelper;
use DateInterval;
use DateTime;
use Yii;
use \common\models\base\Meeting as BaseMeeting;
use common\models\interfaces\EventInterface;
use dmstr\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

/**
 * This is the model class for table "meeting".
 */
class Meeting extends BaseMeeting implements EventInterface
{
    public $dateRange;
    public $userIds;

    public function prepareForCalendar()
    {
        $description = $this->name."<br/>";
            $users = "[";

            $description .= "<br/>".$this->description;
        $users .="]";
        $whole = false;
            if ((substr($this->end_time, 11, 8)==substr($this->start_time, 11, 8))&&(substr($this->start_time, 11, 8)=="00:00:00"))
            {
                $whole = true;
            }

        $att = 0;
        $notes = 0;
        return ['title'=> $this->name, 'type'=>'meeting', 'id'=>$this->id, 'start'=>substr($this->start_time, 0, 10)."T".substr($this->start_time, 11, 8), 'end'=>substr($this->end_time, 0, 10)."T".substr($this->end_time, 11, 8), 'className'=>'meeting typ-'.$this->type.' status-'.$this->status, 'notes'=>$notes, 'users'=>$users, 'files'=>$att, 'allDay'=>$whole, 'description'=>$description];
    }

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
            ['dateRange', 'string'],
            [['userIds'], 'each', 'rule'=>['integer']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [
            'userIds' => Yii::t('app', 'Pracownicy'),

        ];

        return array_merge(parent::attributeLabels(), $labels);
    }


    public function prepareDateAttributes()
    {
        $this->dateRange = $this->start_time.' - '.$this->end_time;
    }

    public function getClassType()
    {
        return 'meeting';
    }
    public static function getClassTypeLabel()
    {
        return Yii::t('app', 'Spotkanie');
    }

    public function getUsersLabel()
    {
        $list = ArrayHelper::map($this->users, 'id', 'displayLabel');
        return implode('; ', $list);
    }

    public function getTooltipContent()
    {
        $info = Html::tag('strong',$this->name);

        $info .= Html::tag('div', Yii::t('app', 'Termin').':<br />'. $this->getTimeRange());
        $info .= Html::tag('hr');
        if ($this->customer !== null)
        {
            $info .= Html::tag('strong', Yii::t('app', 'Klient').': ').$this->customer->getDisplayLabel();
            if ($this->contact !== null)
            {
                $info .= Html::tag('div', $this->contact->getDisplayLabel());
            }
            $info .= Html::tag('hr');
        }
        $info .= Html::tag('div', Yii::t('app', 'Uczestnicy').':<br />'. $this->getUsersLabel());
        return $info;
    }

    public function getTimeRange($separator = ' - ', $format='short')
    {
        $formatter = Yii::$app->formatter;

        $start = $this->start_time;
        $end = $this->end_time;

        return $formatter->asDatetime($start, $format).$separator.$formatter->asDatetime($end, $format);
    }

    public function getAssignedUsers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getUsers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function save($runValidation = true, $attributeNames = null) {
        if ($this->dateRange)
        {
            $date_arr = explode(" - ", $this->dateRange);
            $this->start_time = $date_arr[0];
            $this->end_time = $date_arr[1];
            
        }
        $result = parent::save($runValidation, $attributeNames);

        if ($this->reminder && $this->active == 1) {
            $sending_time = new DateTime($this->start_time);
            $sending_time->sub(new DateInterval('PT' . $this->reminder . 'M'));

            if ($sending_time <= new DateTime('now')) {
                //throw new Exception(Yii::t('app', 'Data powiadomienia nie może być z przeszłości'));
            }
        }
        /*
        if ($this->remind_sms && $this->reminder && $this->active == 1) {
            foreach ($this->users as $user) {
                if ($user->phone) {
                    $sms = new NotificationSms();
                    $sms->setPersonalEventText($this->name, $this->start_time);
                    $sms->user_id = $user->id;
                    $sms->sending_time = $sending_time->format("Y-m-d H:i:s");

                    $reminder_sms = new MeetingSmsReminder();
                    $reminder_sms->meeting_id = $this->id;
                    $reminder_sms->sms_id = $sms->send();;
                    $reminder_sms->save();
                }
            }
        }
        if ($this->remind_email && $this->reminder && $this->active == 1) {
            foreach ($this->users as $user) {
                $mail = new NotificationMail();
                $mail->setPersonalEventText($this->name, $this->start_time);
                $mail->user_id = $user->id;
                $mail->sending_time = $sending_time->format('Y-m-d H:i:s');
                $mail->email_address = $user->email;
                $mail->save();

                $mailReminder = new MeetingMailReminder();
                $mailReminder->mail_id = $mail->id;
                $mailReminder->meeting_id = $this->id;
                $mailReminder->save();
            }
        }
        */
        return $result;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                
            }else{
                if (((isset($changedAttributes['start_time']))&&($changedAttributes['start_time']!=$this->start_time))||((isset($changedAttributes['start_time']))&&($changedAttributes['start_time']!=$this->start_time)))
                    Note::createNote(4, 'meetingScheduleChange', $this, $this->customer_id);
            }
         
    }

}
