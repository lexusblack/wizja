<?php

namespace common\models;

use Yii;
use \common\models\base\UserNote as BaseUserNote;

/**
 * This is the model class for table "user_note".
 */
class UserNote extends BaseUserNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'creator_id'], 'integer'],
            [['datetime'], 'safe'],
            [['name'], 'string']
        ]);
    }
	
}
