<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_hall_group".
 *
 * @property integer $id
 * @property integer $hall_id
 * @property integer $hall_group_id
 *
 * @property \common\models\HallGroup $hallGroup
 * @property \common\models\Hall $hall
 */
class HallHallGroup extends \yii\db\ActiveRecord
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
            'hall'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_id', 'hall_group_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_hall_group';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_id' => 'Hall ID',
            'hall_group_id' => 'Hall Group ID',
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
    public function getHall()
    {
        return $this->hasOne(\common\models\Hall::className(), ['id' => 'hall_id']);
    }
    }
