<?php

namespace common\models;

use common\behaviors\WorkingTimeBehavior;
use DateInterval;
use DateTime;
use Yii;
use \common\models\base\Vehicle as BaseVehicle;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\bootstrap\Html;

/**
 * This is the model class for table "vehicle".
 */
class Vehicle extends BaseVehicle
{
    const TYPE_FIRM = 1;
    const TYPE_RENT = 5;

    public $reminderIds;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'reminderIds'
            ],
            'relations' => [
                'users',
            ],
            'modelClasses'=>[
                'common\models\User',
            ],
        ];
        $behaviors['workingTime']= [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventVehicle::className(),
                'itemIdAttribute'=>'vehicle_id',
        ];
        return $behaviors;
    }



    public function rules()
    {
        $rules = [
            [['reminderIds'], 'each', 'rule'=>['integer']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $lables = [
            'reminderIds'=>Yii::t('app', 'Przypomnij użytkownikom'),
        ];
        return array_merge(parent::attributeLabels(), $lables);
    }

    public static function typeList()
    {
        $list = [
            self::TYPE_FIRM => Yii::t('app', 'Firmowy'),
            self::TYPE_RENT => Yii::t('app', 'Do wypożyczenia'),
        ];
        return $list;
    }

    public function getTypeLabel()
    {
        $index = $this->type;
        $list = self::typeList();
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function getReminderList()
    {
        //w minutach
        $list = [
            1 => Yii::t('app', '1 dzień przed'),
            2 => Yii::t('app', '2 dni przed'),
            7 => Yii::t('app', 'Tydzień przed'),
            14=> Yii::t('app', '2 tygodnie przed'),
        ];
        return $list;
    }

    public function getPhotoUrl()
    {
        if ($this->photo)
            $url = $this->loadFileUrl('photo', '@uploads/vehicle/');
        else
            $url = "/img/truck.png";
        return $url;
    }

    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getVehicleAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedEvents($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getEvents();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedServices($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getVehicleServices();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getDisplayLabel() {
        return $this->name;
    }

    public function getTranslates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getVehicleTranslates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function save($runValidation = true, $attributeNames = null) {
        $result = parent::save($runValidation, $attributeNames);
        $sms = null;
        if ($this->oc_date && $this->reminder && $this->active == 1) {
            $sending_time = new DateTime($this->oc_date);
            $sending_time->sub(new DateInterval('P' . $this->reminder . 'D'));

            if (new DateTime('now') >= $sending_time) {
                return $this->addError('oc_date', Yii::t('app', 'Data powiadomienia nie może być z przeszłości, wybrano').': ' . $sending_time->format("Y-m-d H:i:s"));
            }
            /*
            foreach ($this->users as $user) {
                if ($user->phone) {
                    $sms = new NotificationSms();
                    $sms->setVehicleOcText($this->name, $this->oc_date);
                    $sms->user_id = $user->id;
                    $sms->sending_time = $sending_time->format("Y-m-d H:i:s");
                    $sms->
                    $reminder_sms = new VehicleOcSmsReminder();
                    $reminder_sms->vehicle_id = $this->id;
                    $reminder_sms->sms_id = $sms->send();;
                    $reminder_sms->save();
                }
                if ($user->email) {
                    $mail = new NotificationMail();
                    $mail->setVehicleOcText($this->name, $this->oc_date);
                    $mail->user_id = $user->id;
                    $mail->sending_time = $sending_time->format("Y-m-d H:i:s");
                    $mail->email_address = $user->email;
                    $mail->save();

                    $reminder_mail = new VehicleOcMailReminder();
                    $reminder_mail->mail_id = $mail->id;
                    $reminder_mail->vehicle_id = $this->id;
                    $reminder_mail->save();
                }
            }*/
        }
        return $result;
    }

    public static function getTranslateName($id, $language, $name)
    {
        if (!$language)
            return $name;
        $translate = VehicleTranslate::find()->where(['language_id'=>$language])->andWhere(['vehicle_id'=>$id])->one();
        if ($translate)
            return $translate->name;
        else
            return $name;
    }

        public function getMangeCrewDiv($vehicle, $event)
    {
        $return = "<div style='height:30px; width:400px;'>";

        $width = 0;
        if ($event->eventSchedules)
            $width = 100/(count($event->eventSchedules)+1);
        $return .="<div style='width:".$width."%; height:100%;' class='manage-crew-div'><input type='checkbox'  name='schedule".$this->id."_all' class='schedule-checkbox all' data-user-id=".$this->id."  data-vehicle-id=".$vehicle->id."/></div>";
        foreach ($event->eventSchedules as $schedule)
        {
            if ($schedule->start_time)
            {
                $add = "";
                if ($schedule->prefix)
                    $prefix = $schedule->prefix;
                else
                    $prefix = substr($schedule->name, 0, 1);
                $color1 = EventVehicleWorkingHours::find()->where(['<>', 'event_id', $event->id])->andWhere(['vehicle_id'=>$this->id])->andWhere(['<', 'start_time', $schedule->end_time])->andWhere(['>', 'end_time', $schedule->start_time])->count();
                $color_style = "background-color:#1ab394;";
                if ($color1)
                {
                    $color_style = "background-color:#cc0000;";
                    $add = " overlapping";
                }
                $isChecked = EventVehicleWorkingHours::find()->where(['event_schedule_id'=>$schedule->id, 'vehicle_id'=>$this->id])->count();
                    if ($isChecked)
                    {
                        $color_style = "background-color:#1ab394;";
                    }
                if ($isChecked)
                    $checked = " checked";
                else
                    $checked = "";
                if (!$this->status)
                    $color_style = "background-color:#f8ac59";
                $return .="<div style='width:".$width."%; height:100%; color:white;".$color_style."' class='manage-crew-div'>".$prefix."<input type='checkbox'  name='schedule".$this->id."_".$schedule->id."' class='schedule-vehicle-checkbox".$add."' data-schedule-id=".$schedule->id."  data-vehicle-id=".$this->id."  data-vehicle-model-id=".$vehicle->id." ".$checked."/></div>";
            }

        }
        $return .= "</div>";
        return $return;
    }

    public function getEventsConflictedArray($event, $schedule)
    {
            $array = [];
            $array2 = [];
        if ($schedule)
        {
            $array2[] = ['id'=>'a', 'title'=>$schedule->event->name];
            $tmp = ['title'=>"[".$schedule->name."] ".$schedule->event->name, 'id'=>$schedule->id, 'resourceId'=>'a', 'start'=>substr($schedule->start_time, 0, 10)."T".substr($schedule->start_time, 11, 8), 'end'=>substr($schedule->end_time, 0, 10)."T".substr($schedule->end_time, 11, 8), 'backgroundColor'=>"#111"] ;
            $array[] = $tmp;
        }
        $start = $event->getTimeStart();
        $end = $event->getTimeEnd();
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-10 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+10 days" ) );
            $hours = EventVehicleWorkingHours::find()->where(['vehicle_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->orderBy(['event_id'=>SORT_DESC])->all();
            $ids = \common\helpers\ArrayHelper::map(EventVehicleWorkingHours::find()->where(['vehicle_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->orderBy(['event_id'=>SORT_DESC])->asArray()->all(), 'event_id', 'event_id');
            $events = Event::find()->where(['id'=>$ids])->asArray()->all();

            foreach ($events as $e)
            {
                $array2[] = ['id'=>$e['id'], 'title'=>$e['name']];
            }

            $color = "#1ab394";
            $i = 0;
        $event_id = 0;
        foreach ($hours as $hour)
        {
            $t = "";
            if (isset($hour->eventSchedule->name))
            {
                if ($hour->eventSchedule->prefix)
                    $t = "[".$hour->eventSchedule->prefix."] ";
                else
                    $t = "[".substr($hour->eventSchedule->name, 0, 3)."] ";
            if ($hour->eventSchedule->color)
                    $color = $hour->eventSchedule->color;
                
            }
            $tmp = ['title'=>$t.$hour->event->name, 'id'=>$hour->id, 'resourceId'=>$hour->event_id, 'start'=>substr($hour->start_time, 0, 10)."T".substr($hour->start_time, 11, 8), 'end'=>substr($hour->end_time, 0, 10)."T".substr($hour->end_time, 11, 8), 'backgroundColor'=>$color] ;
            $array[] = $tmp;

        }  
        return ['events'=>$array, 'res'=>$array2];
    }

}
