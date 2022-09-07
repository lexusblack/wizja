<?php

namespace common\models;

use Yii;
use \common\models\base\RentOuterGear as BaseRentOuterGear;

/**
 * This is the model class for table "rent_outer_gear".
 */
class RentOuterGear extends BaseRentOuterGear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['rent_id', 'outer_gear_id'], 'required'],
            [['rent_id', 'outer_gear_id', 'quantity', 'discount', 'type', 'planned', 'order_id', 'confirm', 'user_id', 'description'], 'integer'],
            [['start_time', 'end_time', 'return_time', 'reception_time', 'update_time', 'created_at'], 'safe'],
            [['price'], 'number'],
            [['rent_id', 'outer_gear_id'], 'unique', 'targetAttribute' => ['rent_id', 'outer_gear_id'], 'message' => 'The combination of Rent ID and Outer Gear ID has already been taken.']
        ]);
    }
	
}
