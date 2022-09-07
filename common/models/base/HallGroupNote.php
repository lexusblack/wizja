<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_note".
 *
 * @property integer $id
 * @property string $text
 * @property integer $hall_group_id
 * @property string $datetime
 * @property integer $user_id
 *
 * @property \common\models\HallGroup $hallGroup
 * @property \common\models\User $user
 */
class HallGroupNote extends \yii\db\ActiveRecord
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
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['hall_group_id', 'user_id'], 'integer'],
            [['datetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_note';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => Yii::t('app', 'Treść'),
            'hall_group_id' => 'Hall Group ID',
            'datetime' => 'Datetime',
            'user_id' => 'User ID',
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
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
