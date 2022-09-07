<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "user_note".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $creator_id
 * @property string $datetime
 * @property string $name
 *
 * @property \common\models\User $creator
 * @property \common\models\User $user
 */
class UserNote extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'creator',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'creator_id'], 'integer'],
            [['datetime'], 'safe'],
            [['name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_note';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'creator_id' => 'Creator ID',
            'datetime' => 'Datetime',
            'name' => '',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
