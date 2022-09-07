<?php

namespace common\models;

use Yii;
use \common\models\base\EventOfferVehicle as BaseEventOfferVehicle;

/**
 * This is the model class for table "event_offer_vehicle".
 */
class EventOfferVehicle extends BaseEventOfferVehicle
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'vehicle_model_id', 'quantity'], 'integer'],
            [['schedule'], 'string', 'max' => 255]
        ]);
    }
	
}
