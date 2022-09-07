<?php

namespace common\models;

use Yii;
use \common\models\base\NotificationMail as BaseNotificationMail;

/**
 * This is the model class for table "notification_mail".
 */
class NotificationMail extends BaseNotificationMail  {


    public function setPersonalEventText($event_name, $time) {
        $this->subject = Yii::t('app', 'Przypomnienie o wydarzeniu');
        $this->text = Yii::t('app', 'Przypominamy o spotkaniu').' '.$event_name.', ' .Yii::t('app', 'które odbędzie się').': '.$time;
    }

    public function setVehicleOcText($vehicle_name, $time) {
        $this->subject = Yii::t('app', 'Przypomnienie o kończącym się ubezpieczeniu pojazdu');
        $this->text = Yii::t('app', 'Przypominamy o kończącym się w dniu').' '.$time.' '.Yii::t('app', 'ubezpieczeniu OC samochodu').': '.$vehicle_name;
    }

    public function delete() {
        foreach ($this->meetingMailReminders as $reminder) {
            $reminder->delete();
        }
        foreach ($this->vehicleOcMailReminders as $reminder) {
            $reminder->delete();
        }
        return parent::delete();
    }

    public function updateMailAddress($mail) {
        $this->email_address = $mail;
        $this->save();
    }

    public function send() {
        return Yii::$app->mailer->compose('mailNotification', ['title'=>$this->subject, 'content'=>$this->text])
           ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
            ->setTo($this->email_address)
            ->setSubject($this->subject)
            ->send();
    }
}
