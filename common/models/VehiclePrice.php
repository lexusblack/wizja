<?php

namespace common\models;

use Yii;
use \common\models\base\VehiclePrice as BaseVehiclePrice;

/**
 * This is the model class for table "vehicle_price".
 */
class VehiclePrice extends BaseVehiclePrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['vehicle_model_id', 'default'], 'integer'],
            [['price', 'cost'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 45]
        ]);
    }
	
}
