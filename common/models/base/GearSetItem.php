<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "gear_set_item".
 *
 * @property integer $id
 * @property integer $gear_id
 * @property integer $gear_set_id
 * @property integer $quantity
 *
 * @property \common\models\Gear $gear
 * @property \common\models\GearSet $gearSet
 */
class GearSetItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gear',
            'gearSet'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gear_id', 'gear_set_id', 'quantity'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_set_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_id' => 'Urządzenie',
            'gear_set_id' => 'Gear Set ID',
            'quantity' => 'Liczba sztuk',
        ];
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
    public function getGearSet()
    {
        return $this->hasOne(\common\models\GearSet::className(), ['id' => 'gear_set_id']);
    }
}
