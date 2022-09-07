<?php

namespace common\models;

use Yii;
use \common\models\base\ExpenseType as BaseExpenseType;

/**
 * This is the model class for table "expense_type".
 */
class ExpenseType extends BaseExpenseType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['investition'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ]);
    }
	
}
