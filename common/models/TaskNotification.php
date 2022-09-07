<?php

namespace common\models;
use common\components\Sms;
use yii\helpers\ArrayHelper;

use Yii;
use \common\models\base\TaskNotification as BaseTaskNotification;

/**
 * This is the model class for table "task_notification".
 */
class TaskNotification extends BaseTaskNotification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'time_type', 'time', 'email', 'sms', 'push'], 'integer'],
            [['text'], 'string']
        ]);
    }
	

    public function checkSendDate($date)
    {
        if (($this->task->status==10)||(!$this->task->datetime))
        {
            return false;
        }
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $start = $this->task->datetime;
        $rok = substr($start, 0, 4);
        $miesiac = substr($start, 5, 2);
        $dzien = substr($start, 8, 2);
        $godzina = substr($start, 11, 2);
        $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);
        if ($this->time_type==1) {
            $secs = $this->time*60*(-1);
        }
        if ($this->time_type==2) {
            $secs = $this->time*3600*(-1);
        }
        if ($this->time_type==3) {
            $secs = $this->time*24*6060*(-1);
        }
        if ($this->time_type==4) {
            $secs = $this->time*60;
        }
        if ($this->time_type==5) {
            $secs = $this->time*3600;
        }
        if ($this->time_type==6) {
            $secs = $this->time*24*3600;
        }   
        $time = $time+$secs;
        $send_time = date("Y-m-d H:i:s", $time);
        if ($send_time<$date)
        {
            return true;
        }else{
            return false;
        }
    }

    public function sendReminders()
    {
        $users = $this->task->getAllUsers();
        foreach($users as $user)
        {
            
            if ($this->task->checkStatusForUser($user->id))
            {
            }else{
                 if ($this->email)
                {
                    $this->sendMailReminder($user);
                }
                if ($this->sms)
                {
                    $this->sendSMSReminder($user);
                }
                if ($this->push)
                {

                }               
            }

        }
        $this->sent = 1;
        $this->save();
    }
    public function sendSMSReminder($user)
    {
        $response = null;
        $subject = '[New Event Management] '.Yii::t('app', 'Masz do wykonania zadanie: ').$this->task->title;
        if ($this->task->event_id)
            $subject.= " ".$this->task->event->displayLabel;
        $subject.=" ".$this->text;
        $subject.=" Termin:".substr($this->task->datetime,0,11);
        if (empty($user->phone)) {
            Yii::info(Yii::t('app', 'Brak numeru telefonu').' '.$user->username, 'sms.info');
            return false;
        }
        $model = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $smsEnabled = $model->sms;
        if ($model->sms_sender)
        {
            $sender = $model->sms_sender;
        }else{
            $sender = null;
        }
        try {
                $date = null;
                $response = Sms::load()->messages->sendSms(
                $user->phone,
                strip_tags($subject),
                Sms::getSender(),
                [
                    'test' => $smsEnabled ? false : true,
                    'details'=>true,
                    'date' => $date,
                    'sender'=>$sender
                ]);
        }
        catch (\Exception $e) {
            $response = $user->username.'; '.$user->phone.'; '.$subject.'; '.$e->getMessage();
            Yii::error($response, 'sms.error');
        }
        Yii::info(ArrayHelper::toArray($response), 'sms.response');
        if (isset($response->success) && $response->success) {
            echo $user->displayLabel."OK";
        }
        else {
            //throw new \Exception(Yii::t('app', 'Nie udało się wysłać SMSa'));
        }        
    }


    public function sendMailReminder($user)
    {
        $params = Yii::$app->params;
        $subject = '[New Event Management] '.Yii::t('app', 'Masz do wykonania zadanie: ').$this->task->title;
        if ($this->task->event_id)
            $subject.= " ".$this->task->event->displayLabel;
        $sent = \Yii::$app->mailer->compose('@app/views/task/reminder-mail', [
                'model' =>  $this, 'user'=>$user, 'subject'=>$subject])
                ->setTo($user->email)
                ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
                ->setSubject($subject)
                ->send();
            Yii::info([$sent, Yii::$app->params], 'Mailer');
            return $sent;
    }
}
