<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "outcomes_for_rent".
 *
 * @property integer $id
 * @property integer $outcome_id
 * @property integer $rent_id
 *
 * @property OutcomesWarehouse $outcome
 * @property Rent $rent
 */
class OutcomesForRent extends \common\models\base\OutcomesForRent
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_for_rent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outcome_id', 'rent_id'], 'required'],
            [['outcome_id', 'rent_id'], 'integer'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutcomesWarehouse::className(), 'targetAttribute' => ['outcome_id' => 'id']],
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
            'outcome_id' => Yii::t('app', 'ID wydania z magazynu'),
            'rent_id' => Yii::t('app', 'ID wypożyczenia'),
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
    public function getRent()
    {
        return $this->hasOne(Rent::className(), ['id' => 'rent_id']);
    }

    public function afterSave($insert, $changeAttributes)
    {
                        $eventlog = new RentLog;
                        $eventlog->rent_id = $this->rent_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Wydano sprzęt. Wydanie nr ").$this->outcome_id;
                        $eventlog->save();                        
    }
}
