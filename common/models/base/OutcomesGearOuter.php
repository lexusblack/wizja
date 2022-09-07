<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "outcomes_gear_outer".
 *
 * @property integer $id
 * @property integer $outcome_id
 * @property integer $outer_gear_id
 * @property integer $gear_quantity
 *
 * @property \common\models\OutcomesWarehouse $outcome
 * @property \common\models\OuterGear $outerGear
 * @property string $aliasModel
 */
abstract class OutcomesGearOuter extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_gear_outer';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outcome_id', 'outer_gear_id', 'gear_quantity'], 'required'],
            [['outcome_id', 'outer_gear_id', 'gear_quantity'], 'integer'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutcomesWarehouse::className(), 'targetAttribute' => ['outcome_id' => 'id']],
            [['outer_gear_id'], 'exist', 'skipOnError' => true, 'targetClass' => OuterGear::className(), 'targetAttribute' => ['outer_gear_id' => 'id']]
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
            'outer_gear_id' => Yii::t('app', 'ID sprzętu zewnętrznego'),
            'gear_quantity' => Yii::t('app', 'Liczba'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcome()
    {
        return $this->hasOne(\common\models\OutcomesWarehouse::className(), ['id' => 'outcome_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGear()
    {
        return $this->hasOne(\common\models\OuterGear::className(), ['id' => 'outer_gear_id']);
    }




}
