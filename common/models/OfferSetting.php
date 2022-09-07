<?php

namespace common\models;

use Yii;
use \common\models\base\OfferSetting as BaseOfferSetting;

/**
 * This is the model class for table "offer_setting".
 */
class OfferSetting extends BaseOfferSetting
{
    const TYPE_GEAR = 1;
    const TYPE_VEHICLE = 2;

    public static function loadGear($offerId)
    {
        $models = static::find()
            ->innerJoinWith(['category'])
            ->where([
                'offer_id'=>$offerId,
                'type'=>self::TYPE_GEAR,
            ])
            ->indexBy(function($row){
                return $row->category->name;
            })
            ->all();

        return $models;
    }

}
