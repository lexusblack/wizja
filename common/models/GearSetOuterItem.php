<?php

namespace common\models;

use Yii;
use \common\models\base\GearSetOuterItem as BaseGearSetOuterItem;

/**
 * This is the model class for table "gear_set_outer_item".
 */
class GearSetOuterItem extends BaseGearSetOuterItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_set_id', 'outer_gear_model_id', 'quantity'], 'integer']
        ]);
    }
	
}
