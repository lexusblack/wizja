<?php

namespace common\models;

use DateInterval;
use DateTime;
use Yii;
use \common\models\base\Personal as BasePersonal;
use common\models\interfaces\EventInterface;
use yii\db\Exception;

/**
 * This is the model class for table "personal".
 */
class Personal extends BasePersonal implements EventInterface
{
    public $dateRange;

    public function rules()
    {
        $rules = [
            ['dateRange', 'string'],
            [['repeat_since'], 'date', 'format'=>'php:Y-m-d'],
            [['repeat_since'], 'required', 'when'=>function($model) {
                return ($model->repeat == 0) ? false : true;
            }, 'whenClient'=>"function (attribute, value) {
                return $('#personal-repeat').val() == 0 ? false : true;
            }"],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [

        ];

        return array_merge(parent::attributeLabels(), $labels);
    }


    public function prepareDateAttributes()
    {
        $this->dateRange = $this->start_time.' - '.$this->end_time;
    }

    public static function getReminderList()
    {
        $list = [
            60 => Yii::t('app', 'Godzina przed'),
            120 => Yii::t('app', '2 godziny przed'),
            360 => Yii::t('app', '6 godzin przed'),
            1440 => Yii::t('app', '1 dzień przed'),
            2880 => Yii::t('app', '2 dni przed'),
            10080 => Yii::t('app', '7 dni przed'),
        ];

        return $list;
    }

    public function getReminderLabel()
    {
        $list = static::getReminderList();
        $index = $this->reminder;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function getRepeatList()
    {
        //liczba dni
        $list = [
            0 => Yii::t('app', 'Nie powtarzaj'),
            1 => Yii::t('app', 'Codziennie'),
            7 => Yii::t('app', 'Co tydzień'),
            30 => Yii::t('app', 'Co miesiąc'),
            90 => Yii::t('app', 'Co 3 miesiące'),
            180 => Yii::t('app', 'Co pół roku'),
            365 => Yii::t('app', 'Co rok'),
        ];

        return $list;
    }

    public function getRepeatLabel()
    {
        $list = static::getRepeatList();
        $index = $this->repeat;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function repeatMap()
    {
        $list = [
            0=> Yii::t('app', '0 dni'),
            1 => Yii::t('app', '+1 day'),
            7 => Yii::t('app', '+1 week'),
            30 => Yii::t('app', '+1 month'),
            90 => Yii::t('app', '+3 months'),
            180 => Yii::t('app', '+6 months'),
            365 => Yii::t('app', '+1 year'),
        ];

        return $list;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->parent_id == null)
        {
            $this->_storeRepeats();
        }
    }

    protected function _storeRepeats()
    {
        static::deleteAll(['parent_id'=>$this->id]);

        $map = self::repeatMap();
        if ($this->repeat == 0)
        {
            return;
        }
        $period = $map[$this->repeat];

        $since = strtotime($this->repeat_since.' 23:59:59');

        $start = strtotime($this->start_time.' '.$period);
        $end = strtotime($this->end_time.' '.$period);
        while ($since>=$start)
        {
            $model = new Personal($this->attributes);
            unset($model->id);
            $model->parent_id = $this->id;
            $model->dateRange = date('Y-m-d H:i:s', $start)." - ".date('Y-m-d H:i:s', $end);
            //$model->start_time = date('Y-m-d H:i:s', $start);
            //$model->end_time = date('Y-m-d H:i:s', $end);
            $model->save();

            $s = date('Y-m-d H:i:s', $start);
            $e = date('Y-m-d H:i:s', $end);
            $start = strtotime($s.' '.$period);
            $end = strtotime($e.' '.$period);
        }
    }

    public function getClassType() {
        return 'personal';
    }

    public static function getClassTypeLabel() {
        return Yii::t('app', 'Wydarzenie prywatne');
    }

    public function deleteNotificationSms() {
        if ($this->notificationSms) {
            $sms = $this->notificationSms;
            $this->notification_sms_id = null;
            $this->remind_sms = 0;
            $this->save();
            $sms->delete();
        }
    }

    public function deleteNotificationMail() {
        if ($this->notificationMail) {
            $mail = $this->notificationMail;
            $this->notification_mail_id = null;
            $this->remind_email = 0;
            $this->save();
            $mail->delete();
        }
    }

    public function save($runValidation = true, $attributeNames = null) {
        $sms = null;
        $mail = null;
        $sending_time = new DateTime($this->start_time);

        $date_arr = explode(" - ", $this->dateRange);
        $this->start_time = $date_arr[0];
        $this->end_time = $date_arr[1];

        if ($this->reminder) {
            $sending_time->sub(new DateInterval('PT' . $this->reminder . 'M'));

            if (new DateTime('now') >= $sending_time) {
                return $this->addError('dateRange', Yii::t('app', 'Data powiadomienia nie może być z przeszłości, wybrano').': ' . $sending_time->format("Y-m-d H:i:s"));
            }
            if ($this->remind_sms) {
                $sms = new NotificationSms();
                $sms->setPersonalEventText($this->name, $this->start_time);
                $sms->user_id = $this->user_id;
                $sms->sending_time = $sending_time->format("Y-m-d H:i:s");
                $this->notification_sms_id = $sms->send();
            }
            if ($this->remind_email) {
                $mail = new NotificationMail();
                $mail->setPersonalEventText($this->name, $this->start_time);
                $mail->user_id = $this->user_id;
                $mail->email_address = $this->user->email;
                $mail->sending_time = $sending_time->format("Y-m-d H:i:s");
                $mail->save();
                $this->notification_mail_id = $mail->id;
            }
        }

        if (parent::save($runValidation, $attributeNames)) {
            return true;
        }

        // kasujemy powiadomienia, jeżeli nie przeszedł walidacji
        if ($mail) {
            $mail->delete();
        }
        if ($sms) {
            $sms->delete();
        }
        return false;
    }
}
