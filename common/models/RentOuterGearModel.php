<?php

namespace common\models;

use Yii;
use \common\models\base\RentOuterGearModel as BaseRentOuterGearModel;

/**
 * This is the model class for table "rent_outer_gear_model".
 */
class RentOuterGearModel extends BaseRentOuterGearModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['rent_id', 'outer_gear_model_id', 'quantity', 'type', 'resolved'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ]);
    }
	
}
