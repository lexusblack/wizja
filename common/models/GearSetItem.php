<?php

namespace common\models;

use Yii;
use \common\models\base\GearSetItem as BaseGearSetItem;

/**
 * This is the model class for table "gear_set_item".
 */
class GearSetItem extends BaseGearSetItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'gear_set_id', 'quantity'], 'integer']
        ]);
    }
	
}
