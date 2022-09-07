<?php

namespace common\models;

use common\models\Customer;
use Yii;

/**
 * This is the model class for table "outcomes_for_customer".
 *
 * @property integer $id
 * @property integer $outcome_id
 * @property integer $customer_id
 *
 * @property OutcomesWarehouse $outcome
 * @property Customer $customer
 */
class OutcomesForCustomer extends \common\models\base\OutcomesForCustomer
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_for_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outcome_id', 'customer_id'], 'required'],
            [['outcome_id', 'customer_id'], 'integer'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutcomesWarehouse::className(), 'targetAttribute' => ['outcome_id' => 'id']],
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
            'outcome_id' => Yii::t('app', 'ID wydania z magazynu'),
            'customer_id' => Yii::t('app', 'ID klienta'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcome()
    {
        return $this->hasOne(OutcomesWarehouse::className(), ['id' => 'outcome_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
