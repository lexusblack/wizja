<?php

namespace common\models;

use Yii;
use \common\models\base\ExpenseContentRate as BaseExpenseContentRate;

/**
 * This is the model class for table "expense_content_rate".
 */
class ExpenseContentRate extends BaseExpenseContentRate
{
    public function beforeValidate()
    {
//        $this->discount = $this->price*$this->count * ($this->discount_percent/100);
//        $this->netto = $this->price*$this->count - $this->discount;
        $this->tax = $this->netto*($this->vat/100);
        $this->brutto = $this->netto + $this->tax;

        return parent::beforeValidate();
    }
}
