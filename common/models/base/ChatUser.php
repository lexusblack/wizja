<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "chat_user".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $chat_id
 *
 * @property \common\models\Chat $chat
 * @property \common\models\User $user
 */
class ChatUser extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'chat_id'], 'integer']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_user';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','ID użytkownika'),
            'chat_id' => Yii::t('app','ID chatu'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(\common\models\Chat::className(), ['id' => 'chat_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
