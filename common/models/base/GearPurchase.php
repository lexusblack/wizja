<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_purchase".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $quantity
 * @property string $price
 * @property string $total_price
 * @property string $datetime
 * @property integer $customer_id
 * @property integer $expense_id
 * @property integer $user_id
 *
 * @property \common\models\Customer $customer
 * @property \common\models\Expense $expense
 * @property \common\models\Gear $gear
 * @property \common\models\User $user
 */
class GearPurchase extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'customer',
            'expense',
            'gear',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'quantity', 'customer_id', 'expense_id', 'user_id'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['datetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_purchase';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => Yii::t('app', 'Sprzęt'),
            'quantity' => Yii::t('app', 'Liczba sztuk'),
            'price' => Yii::t('app', 'Cena'),
            'total_price' => Yii::t('app', 'Cena łącznie'),
            'datetime' => Yii::t('app', 'Data zakupu'),
            'customer_id' => Yii::t('app', 'Kontrahent'),
            'expense_id' => 'Expense ID',
            'user_id' => 'User ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
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
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
