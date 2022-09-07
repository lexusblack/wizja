<?php

namespace common\models;

use \common\models\base\ChatMessage as BaseChatMessage;
use \common\models\User;
/**
 * This is the model class for table "chat_message".
 */
class ChatMessage extends BaseChatMessage
{
    /**
     * @inheritdoc
     */
    public $user_from_name;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_from', 'user_to', 'chat_id', 'read'], 'integer'],
            [['create_time'], 'safe'],
            [['text'], 'string']
        ]);
    }

    public function getUserName()
    {
        $user = User::findOne($this->user_from);
        return $user->first_name." ".$user->last_name;
    }

    public function getTextUrl()
    {
        $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i'; 
        $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $this->text);
        return str_replace("\n", "<br/>", $string);
    }

    public function getUserFrom()
    {
        return User::findOne($this->user_from);
    }

    public function getUserToName()
    {
        $user = User::findOne($this->user_to);
        return $user->first_name." ".$user->last_name;
    }

public function afterSave($insert, $changedAttributes)
{
    if ($insert)
    {
        $user = User::findOne($this->user_to);
        Notification::sendUserPushNotification($user, $this->getUserName(), $this->text, 2, $this->chat_id, $this->user_from);
    }
        parent::afterSave($insert, $changedAttributes);
}	
}
