<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_gear".
 *
 * @property integer $id
 * @property integer $hall_group_id
 * @property integer $gear_id
 * @property integer $quantity
 *
 * @property \common\models\HallGroup $hallGroup
 * @property \common\models\Gear $gear
 */
class HallGroupGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallGroup',
            'gear'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_group_id', 'gear_id', 'quantity'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_group_id' => 'Hall Group ID',
            'gear_id' => Yii::t('app', 'Sprzęt'),
            'quantity' => Yii::t('app', 'Liczba sztuk'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
    }
