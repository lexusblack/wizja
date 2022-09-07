<?php

namespace common\models;

use Yii;
use \common\models\base\GearPrice as BaseGearPrice;

/**
 * This is the model class for table "gear_price".
 */
class GearPrice extends BaseGearPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'gears_price_id'], 'required'],
            [['gear_id', 'gears_price_id', 'add_to_event', 'one_per_event'], 'integer'],
            [['price', 'cost'], 'number'],
            [['cost_name'], 'string']
        ]);
    }
	
}
