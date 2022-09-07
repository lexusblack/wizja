<?php

namespace common\models;

use Yii;
use \common\models\base\Ride as BaseRide;

/**
 * This is the model class for table "ride".
 */
class Ride extends BaseRide
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vehicle_id', 'user_id', 'event_id', 'km_start', 'km_end'], 'integer'],
            [['vehicle_id', 'user_id', 'km_start', 'start'], 'required'],
            [['start', 'end'], 'safe'],
            [['description'], 'string'],
            [['start_place', 'end_place'], 'string', 'max' => 255]
        ];
    }
	
}
