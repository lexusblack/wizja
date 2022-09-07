<?php

namespace common\models;

use Yii;
use \common\models\base\ExpenseContent as BaseExpenseContent;

/**
 * This is the model class for table "expense_content".
 */
class ExpenseContent extends BaseExpenseContent
{
    public function rules()
    {
        $rules = [
            [['name', 'price'], 'required'],
//            [['event_expense_id'], 'validateCustomer'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function beforeValidate()
    {
        if (!is_numeric($this->discount_percent))
            $this->discount_percent = 0;
        $this->discount = $this->price*$this->count * ($this->discount_percent/100);
        $this->netto = $this->price*$this->count - $this->discount;
        $this->tax = $this->netto*($this->vat/100);
        $this->brutto = $this->netto + $this->tax;

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->eventExpense !== null)
        {
            $model = $this->eventExpense;
            $attributes = [
                'invoice_nr' => $this->expense->number,
                'expense_id' => $this->expense->id,
                'amount' =>$this->netto,
                'status' => EventExpense::STATUS_INVOICE_BOOKED,
            ];
            $model->updateAttributes($attributes);
        }
        if ($insert)
        {
            $type = ExpenseType::find()->where(['id'=>$this->expense->expense_type])->one();
            if ($type->investition)
            {
                    $investition = new Investition();
                    $investition->expense_id = $this->expense->id;
                    $investition->name = $this->name;
                    $investition->quantity = (int)$this->count;
                    $investition->price = $this->price;
                    $investition->total_price= $this->netto;
                    $investition->vat= $this->vat;
                    $investition->sections = "";
                    $investition->datetime = $this->expense->date;
                    $investition->save();

            }
        }
        
    }

    public function beforeDelete()
    {
        if (empty($this->expense->events) == false)
    {
        foreach ($this->expense->events as $event)
        {
            $event->updateStatutes(true);
        }
    }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }




}
