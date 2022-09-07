<?php

namespace common\models;

use Yii;
use \common\models\base\ExpenseUserPayment as BaseExpenseUserPayment;

/**
 * This is the model class for table "expense_user_payment".
 */
class ExpenseUserPayment extends BaseExpenseUserPayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['expense_id', 'user_id', 'year', 'month'], 'integer']
        ]);
    }
	
}
