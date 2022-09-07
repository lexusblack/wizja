<?php

namespace common\models;

use common\models\Gear;
use Yii;

/**
 * This is the model class for table "incomes_gear_our".
 *
 * @property integer $id
 * @property integer $income_id
 * @property integer $gear_id
 * @property integer $quantity
 *
 * @property OutcomesWarehouse $income
 * @property Gear $gear
 */
class IncomesGearOur extends \common\models\base\IncomesGearOur
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incomes_gear_our';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['income_id', 'gear_id', 'quantity'], 'required'],
            [['income_id', 'gear_id', 'quantity'], 'integer'],
            [['income_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomesWarehouse::className(), 'targetAttribute' => ['income_id' => 'id']],
            [['gear_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearItem::className(), 'targetAttribute' => ['gear_id' => 'id']],
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
            'gear_id' => Yii::t('app', 'ID modelu sprzętu'),
            'quantity' => Yii::t('app', 'Liczba'),
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
    public function getGear()
    {
        return $this->hasOne(GearItem::className(), ['id' => 'gear_id']);
    }

                public function afterSave($insert, $changedAttributes)
    {
        //$this->getNoItemsItem();
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
                Note::createNote(4, 'gearIncomed', $this, $this->gear_id);
           
        }

    }
}
