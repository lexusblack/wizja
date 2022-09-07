<?php

namespace common\models;

use Yii;
use \common\models\base\ExpensePaymentHistory as BaseExpensePaymentHistory;

/**
 * This is the model class for table "expense_payment_history".
 */
class ExpensePaymentHistory extends BaseExpensePaymentHistory
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestampBehavior'] = [
            'class' => \yii\behaviors\TimestampBehavior::className(),
            'createdAtAttribute' => 'create_time',
            'updatedAtAttribute' => 'update_time',
            'value' => new \yii\db\Expression('NOW()'),
        ];
        return $behaviors;
    }

    public function beforeValidate()
    {
        if (empty($this->date))
        {
            $this->date = date('Y-m-d');
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            $p = ExpenseUserPayment::find()->where(['expense_id'=>$this->expense_id])->one();
            if ($p)
            {
                $pu = new UserPayment();
                $pu->user_id = $p->user_id;
                $pu->month = $p->month;
                $pu->year = $p->year;
                $pu->amount = $this->amount;
                $pu->creator_id = $this->creator_id;
                $pu->payment_method = $this->payment_method;
                $pu->datetime = $this->date;
                $pu->save();
            }
        }
    }
}
