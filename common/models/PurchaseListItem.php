<?php

namespace common\models;

use Yii;
use \common\models\base\PurchaseListItem as BasePurchaseListItem;

/**
 * This is the model class for table "purchase_list_item".
 */
class PurchaseListItem extends BasePurchaseListItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['purchase_list_id', 'quantity', 'outer_gear_id', 'event_id', 'status', 'position'], 'integer'],
            [['name', 'company_name', 'company_address', 'description'], 'string', 'max' => 255]
        ]);
    }

        public function getStatusList()
    {
        return [0=>Yii::t('app', 'Nowa'), 1=>Yii::t('app', 'Zrealizowana'), 2=>Yii::t('app', 'Anulowana')];
    }

        public function afterDelete()
    {
        if ($this->event_expense_id)
        {
            EventExpense::deleteAll(['id'=>$this->event_expense_id]);
        }
        parent::afterDelete();
    }

    public function beforeSave($insert)
    {
        
        parent::beforeSave($insert);
        $this->updateExpense();
        return true;
    }

    public function updateExpense()
    {
        if (($this->event_id)&&(!$this->outer_gear_id))
        {
            if ($this->event_expense_id)
            {
                $expense = EventExpense::findOne(['id'=>$this->event_expense_id]);
            }else{
                $expense = new EventExpense();

            }
            $expense->event_id = $this->event_id;
            $expense->name = $this->name." [x".$this->quantity."]";
            $expense->amount = $this->price*$this->quantity;       
            if ($expense->save())
                $this->event_expense_id = $expense->id;
        }

        if ($this->outer_gear_id)
        {
            $this->eventOuterGear->quantity = $this->quantity;
            $this->eventOuterGear->price = $this->price*$this->quantity;
            $this->eventOuterGear->save();
        }
        return true;
                
    }
	
}
