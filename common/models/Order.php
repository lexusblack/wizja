<?php

namespace common\models;

use \common\models\base\Order as BaseOrder;

/**
 * This is the model class for table "order".
 */
class Order extends BaseOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['company_id', 'contact_id', 'confirm', 'user_id'], 'integer'],
            [['create_at', 'update_at'], 'safe'],
            [['hash'], 'string', 'max' => 45]
        ]);
    }
	
}
