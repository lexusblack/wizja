<?php

namespace common\models;

use Yii;
use \common\models\base\GearConnected as BaseGearConnected;

/**
 * This is the model class for table "gear_connected".
 */
class GearConnected extends BaseGearConnected
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'connected_id', 'quantity', 'gear_quantity'], 'required'],
            [['gear_id', 'connected_id', 'quantity', 'in_offer', 'checked', 'gear_quantity'], 'integer']
        ]);
    }
	
}
