<?php

namespace common\models;

use Yii;
use \common\models\base\PurchaseEvent as BasePurchaseEvent;

/**
 * This is the model class for table "purchase_event".
 */
class PurchaseEvent extends BasePurchaseEvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'purchase_id'], 'integer']
        ]);
    }
	
}
