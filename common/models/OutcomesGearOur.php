<?php

namespace common\models;

use Yii;


/**
 * This is the model class for table "outcomes_gear_our".
 *
 * @property integer $id
 * @property integer $outcome_id
 * @property integer $gear_id
 * @property integer $gear_quantity
 *
 * @property OutcomesWarehouse $outcome
 * @property Gear $gear
 */
class OutcomesGearOur extends \common\models\base\OutcomesGearOur
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_gear_our';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outcome_id', 'gear_id', 'gear_quantity'], 'required'],
            [['outcome_id', 'gear_id', 'gear_quantity'], 'integer'],
            [['outcome_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutcomesWarehouse::className(), 'targetAttribute' => ['outcome_id' => 'id']],
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
            'outcome_id' => Yii::t('app', 'ID wydania z magazynu'),
            'gear_id' => Yii::t('app', 'ID modelu sprzętu'),
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
    public function getGear()
    {
        return $this->hasOne(GearItem::className(), ['id' => 'gear_id']);
    }

    public function beforeDelete()
    {
        $this->gear->outcomed -= $this->gear_quantity;
        if ($this->gear->outcomed)
            $this->gear->outcomed = 0;
        $this->gear->save();

        return true;
    }
            public function afterSave($insert, $changedAttributes)
    {
        //$this->getNoItemsItem();
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
                Note::createNote(4, 'gearOutcomed', $this, $this->gear_id);
           
        }

    }

}
