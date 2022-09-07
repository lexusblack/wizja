<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_set_outer_item".
 *
 * @property integer $id
 * @property integer $gear_set_id
 * @property integer $outer_gear_model_id
 * @property integer $quantity
 *
 * @property \common\models\OuterGearModel $outerGearModel
 * @property \common\models\GearSet $gearSet
 */
class GearSetOuterItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'outerGearModel',
            'gearSet'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_set_id', 'outer_gear_model_id', 'quantity'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_set_outer_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_set_id' => 'Gear Set ID',
            'outer_gear_model_id' => Yii::t('app', 'Sprzęt'),
            'quantity' => Yii::t('app', 'Liczba sztuk'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearModel()
    {
        return $this->hasOne(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGearSet()
    {
        return $this->hasOne(\common\models\GearSet::className(), ['id' => 'gear_set_id']);
    }
    }
