<?php

namespace common\models;

use Yii;
use \common\models\base\EventFinance as BaseEventFinance;

/**
 * This is the model class for table "event_finance".
 */
class EventFinance extends BaseEventFinance
{
    public static function getProvisionList()
    {
        $formatter = Yii::$app->formatter;
        $list = [];
        $range = range(0.5, 100, 0.5);

        foreach ($range as $v)
        {
            $index = number_format($v/100, 3);
            $list[$index.''] = $formatter->asDecimal($v,1).'%';
        }
//        var_dump($list);
        return $list;
    }
}
