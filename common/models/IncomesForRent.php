<?php

namespace common\models;

use common\models\Rent;
use Yii;

/**
 * This is the model class for table "incomes_for_rent".
 *
 * @property integer $id
 * @property integer $income_id
 * @property integer $rent_id
 *
 * @property IncomesWarehouse $income
 * @property Rent $rent
 */
class IncomesForRent extends \common\models\base\IncomesForRent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incomes_for_rent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['income_id', 'rent_id'], 'required'],
            [['income_id', 'rent_id'], 'integer'],
            [['income_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomesWarehouse::className(), 'targetAttribute' => ['income_id' => 'id']],
            [['rent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rent::className(), 'targetAttribute' => ['rent_id' => 'id']],
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
            'rent_id' => Yii::t('app', 'ID wypożyczenia'),
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
    public function getRent()
    {
        return $this->hasOne(Rent::className(), ['id' => 'rent_id']);
    }

    public function afterSave($insert, $changeAttributes)
    {
                        $eventlog = new RentLog;
                        $eventlog->rent_id = $this->rent_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Przyjęto sprzęt. Przyjęcie nr ").$this->income_id;
                        $eventlog->save();                        
    }
}
