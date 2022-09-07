<?php

namespace common\models;

use common\models\Customer;
use Yii;

/**
 * This is the model class for table "incomes_for_customer".
 *
 * @property integer $id
 * @property integer $income_id
 * @property integer $customer_id
 *
 * @property IncomesWarehouse $income
 * @property Customer $customer
 */
class IncomesForCustomer extends \common\models\base\IncomesForCustomer
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incomes_for_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['income_id', 'customer_id'], 'required'],
            [['income_id', 'customer_id'], 'integer'],
            [['income_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomesWarehouse::className(), 'targetAttribute' => ['income_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'income_id' => Yii::t('app', 'ID przyjęcia'),
            'customer_id' => Yii::t('app', 'ID klienta'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncome()
    {
        return $this->hasOne(IncomesWarehouse::className(), ['id' => 'income_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
