<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "chat".
 *
 * @property integer $id
 * @property string $name
 * @property string $last_message
 * @property integer $create_by
 * @property string $create_time
 * @property string $update_time
 * @property integer $event_id
 *
 * @property \common\models\Event $event
 * @property \common\models\User $createBy
 * @property \common\models\ChatMessage[] $chatMessages
 * @property \common\models\ChatUser[] $chatUsers
 */
class Chat extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_message', 'create_time', 'update_time'], 'safe'],
            [['create_by', 'event_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['userIds'], 'each', 'rule' => ['integer']],

        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'name' => Yii::t('app','Nazwa'),
            'last_message' => Yii::t('app','Ostatnia wiadomość'),
            'create_by' => Yii::t('app','Stworzony przez'),
            'create_time' => Yii::t('app','Stworzono'),
            'update_time' => Yii::t('app','Zaktualizowano'),
            'event_id' => Yii::t('app','Event ID'),
            'userIds' => Yii::t('app','Użytkownicy')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'create_by']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany(\common\models\ChatMessage::className(), ['chat_id' => 'id']);
    }

     public function getMessages()
    {
        return \common\models\ChatMessage::find()->where(['chat_id'=>$this->id])->orderBy(['create_time'=>SORT_ASC])->all();
    }     
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatUsers()
    {
        return $this->hasMany(\common\models\ChatUser::className(), ['chat_id' => 'id']);
    }
    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('chat_user', ['chat_id' => 'id']);
    }
    
/**
     * @inheritdoc
     * @return array mixed
     */ 
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        'link' => [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'userIds',
            ],
            'relations' => [
                'users',
            ],
            'modelClasses' => [
                'common\models\User',
            ],
            ],
        ];
    }
}
