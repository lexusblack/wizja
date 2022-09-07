<?php

namespace common\models;

use Yii;
use \common\models\base\RentGearOutcomed as BaseRentGearOutcomed;

/**
 * This is the model class for table "rent_gear_outcomed".
 */
class RentGearOutcomed extends BaseRentGearOutcomed
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['rent_id', 'gear_id', 'quantity'], 'required'],
            [['rent_id', 'gear_id', 'quantity'], 'integer']
        ]);
    }
	
}
