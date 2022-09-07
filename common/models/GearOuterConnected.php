<?php

namespace common\models;

use Yii;
use \common\models\base\GearOuterConnected as BaseGearOuterConnected;

/**
 * This is the model class for table "gear_outer_connected".
 */
class GearOuterConnected extends BaseGearOuterConnected
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'connected_id', 'quantity', 'checked', 'gear_quantity', 'in_offer'], 'integer']
        ]);
    }
	
}
