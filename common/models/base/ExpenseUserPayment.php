<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "expense_user_payment".
 *
 * @property integer $id
 * @property integer $expense_id
 * @property integer $user_id
 * @property integer $year
 * @property integer $month
 *
 * @property \common\models\Expense $expense
 * @property \common\models\User $user
 */
class ExpenseUserPayment extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'expense',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expense_id', 'user_id', 'year', 'month'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expense_user_payment';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expense_id' => 'Expense ID',
            'user_id' => 'User ID',
            'year' => 'Year',
            'month' => 'Month',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpense()
    {
        return $this->hasOne(\common\models\Expense::className(), ['id' => 'expense_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
