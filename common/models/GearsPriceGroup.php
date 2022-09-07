<?php

namespace common\models;

use Yii;
use \common\models\base\GearsPriceGroup as BaseGearsPriceGroup;

/**
 * This is the model class for table "gears_price_group".
 */
class GearsPriceGroup extends BaseGearsPriceGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gears_price_id', 'price_group_id'], 'integer']
        ]);
    }
	
}
