<?php

namespace common\models;

use Yii;
use \common\models\base\PurchaseType as BasePurchaseType;

/**
 * This is the model class for table "purchase_type".
 */
class PurchaseType extends BasePurchaseType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string', 'max' => 45]
        ]);
    }
	
}
