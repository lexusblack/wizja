<?php

namespace common\models;

use Yii;
use \common\models\base\WarehouseQuantity as BaseWarehouseQuantity;

/**
 * This is the model class for table "warehouse_quantity".
 */
class WarehouseQuantity extends BaseWarehouseQuantity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id'], 'required'],
            [['gear_id', 'warehouse_id', 'quantity'], 'integer'],
            [['location'], 'safe']
        ]);
    }
	
}
