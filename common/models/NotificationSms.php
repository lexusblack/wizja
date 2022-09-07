<?php

namespace common\models;

use common\components\Sms;
use Yii;
use \common\models\base\NotificationSms as BaseNotificationSms;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notification_sms".
 */
class NotificationSms extends BaseNotificationSms {


    public function send($now = false) {
        return $this->sendSms($now);
    }

    private function sendSms($now = false) {
        $model = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $smsEnabled = $model->sms;
        if ($model->sms_sender)
        {
            $sender = $model->sms_sender;
        }else{
            $sender = null;
        }
        $response = null;
        if (empty($this->user->phone)) {
            Yii::info(Yii::t('app', 'Brak numeru telefonu').' '.$this->user->username, 'sms.info');
            return false;
        }
        try {
            $date = $this->sending_time;
            if ($now) {
                $date = null;
            }
            $response = Sms::load()->messages->sendSms(
                $this->user->phone,
                strip_tags($this->text),
                Sms::getSender(),
                [
                    'test' => $smsEnabled ? false : true,
                    'details'=>true,
                    'date' => $date,
                    'sender'=>$sender
                ]);
        }
        catch (\Exception $e) {
            $response = $this->user->username.'; '.$this->user->phone.'; '.$this->text.'; '.$e->getMessage();
            Yii::error($response, 'sms.error');
        }
        Yii::info(ArrayHelper::toArray($response), 'sms.response');
        if (isset($response->success) && $response->success) {
            $this->sms_id = $response->items[0]->id;
            $this->save();
            return $this->id;
        }
        else {
            //throw new \Exception(Yii::t('app', 'Nie udało się wysłać SMSa'));
        }
    }

    public function setPersonalEventText($event_name, $time) {
        $this->text = Yii::t('app', 'Przypominamy o spotkaniu').' '.$event_name.', '.Yii::t('app', 'które odbędzie się').': '.$time;
    }

    public function setVehicleOcText($vehicle_name, $time) {
        $this->text = Yii::t('app', 'Przypominamy o kończącym się w dniu').' '.$time.' '.Yii::t('app', 'ubezpieczeniu OC samochodu').': '.$vehicle_name;
    }

    public function updatePhoneNumber() {
        if ($this->sms_id) {
            Sms::load()->messages->delete($this->sms_id);
        }
        $this->send();
    }

    public function delete() {
        if ($this->sms_id) {
            Sms::load()->messages->delete($this->sms_id);
        }
        $personal_meetings = Personal::findAll(['notification_sms_id' => $this->id]);
        foreach ($personal_meetings as $personal_meeting) {
            $personal_meeting->notification_sms_id = null;
            $personal_meeting->remind_sms = 0;
            $personal_meeting->save();
        }
        foreach ($this->meetingSmsReminders as $reminder) {
            $reminder->delete();
        }
        foreach ($this->vehicleOcSmsReminders as $reminder) {
            $reminder->delete();
        }
        return parent::delete();
    }
}
