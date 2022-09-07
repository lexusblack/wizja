<?php

namespace common\models;

use \common\models\base\ChatUser as BaseChatUser;

/**
 * This is the model class for table "chat_user".
 */
class ChatUser extends BaseChatUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'chat_id'], 'integer']
        ]);
    }
	
}
