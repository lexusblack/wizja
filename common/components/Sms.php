<?php
namespace common\components;

use SerwerSMS\SerwerSMS;

class Sms extends SerwerSMS
{
    public static function load()
    {
        $params = \Yii::$app->params;
        return new SerwerSMS($params['smsUsername'], $params['smsPassword']);
    }

    public static function getSender()
    {
        return \Yii::$app->params['smsSender'];
    }

}