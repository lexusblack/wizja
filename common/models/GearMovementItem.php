<?php

namespace common\models;

use Yii;
use \common\models\base\GearMovementItem as BaseGearMovementItem;

/**
 * This is the model class for table "gear_movement_item".
 */
class GearMovementItem extends BaseGearMovementItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'gear_item_id', 'gear_movement_id'], 'integer']
        ]);
    }
	
}
