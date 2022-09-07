<?php

namespace common\models;

use Yii;
use \common\models\base\RfidReadings as BaseRfidReadings;

/**
 * This is the model class for table "dbo.GateReadings".
 */
class RfidReadings extends BaseRfidReadings
{
    public static function getDb() {
        return Yii::$app->dbrfid;
    }
}
