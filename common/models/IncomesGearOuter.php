<?php

namespace common\models;

use common\models\OuterGear;
use Yii;

/**
 * This is the model class for table "incomes_gear_outer".
 *
 * @property integer $id
 * @property integer $income_id
 * @property integer $outer_gear_id
 * @property integer $gear_quantity
 *
 * @property IncomesWarehouse $income
 * @property OuterGear $outerGear
 */
class IncomesGearOuter extends \common\models\base\IncomesGearOuter
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'incomes_gear_outer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['income_id', 'outer_gear_id', 'gear_quantity'], 'required'],
            [['income_id', 'outer_gear_id', 'gear_quantity'], 'integer'],
            [['income_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomesWarehouse::className(), 'targetAttribute' => ['income_id' => 'id']],
            [['outer_gear_id'], 'exist', 'skipOnError' => true, 'targetClass' => OuterGear::className(), 'targetAttribute' => ['outer_gear_id' => 'id']],
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
            'outer_gear_id' => Yii::t('app', 'ID sprzętu zewnętrznego'),
            'gear_quantity' => Yii::t('app', 'Liczba'),
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
    public function getOuterGear()
    {
        return $this->hasOne(OuterGear::className(), ['id' => 'outer_gear_id']);
    }
}
