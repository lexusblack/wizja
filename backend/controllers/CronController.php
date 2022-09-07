<?php


namespace backend\controllers;


use backend\components\Controller;
use common\components\filters\AccessControl;
use common\models\NotificationMail;
use DateTime;
use Yii;

class CronController extends Controller {

    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'baseName' => $this->id,
        ];

        return $behaviors;
    }

    public function actionSendMails() {
        $date_start = new DateTime(date('Y-m-d H').":00:00");
        $date_end = new DateTime(date('Y-m-d H').":59:59");
        $mails = NotificationMail::find()->where(['sent' => 0])->andWhere(['between', 'sending_time', $date_start->format("Y-m-d H:i:s"), $date_end->format("Y-m-d H:i:s")])->all();

        $i = 0;
        foreach ($mails as $mail) {
            if ($mail->send()) {
                $mail->sent = 1;
                $mail->save();
            }
            $i++;
        }

        echo Yii::t('app', "Wysłano")." " . $i . " ".Yii::t('app', "maili").".";
    }

}