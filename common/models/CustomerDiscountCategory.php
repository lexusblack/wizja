<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerDiscountCategory as BaseCustomerDiscountCategory;

/**
 * This is the model class for table "customer_discount_category".
 */
class CustomerDiscountCategory extends BaseCustomerDiscountCategory
{
	

	public function getCustomerDiscountCustomer()
    {
        return $this->hasMany(\common\models\CustomerDiscountCustomer::className(), ['customer_discount_id' => 'customer_discount_id']);
    }
}
