<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerDiscountCustomer as BaseCustomerDiscountCustomer;

/**
 * This is the model class for table "customer_discount_customer".
 */
class CustomerDiscountCustomer extends BaseCustomerDiscountCustomer
{
	public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(4, 'customerDiscountAdded', $this, $this->customer_id);
            }
         
    }
}
