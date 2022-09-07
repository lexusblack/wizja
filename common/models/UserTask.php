<?php

namespace common\models;

use Yii;
use \common\models\base\UserTask as BaseUserTask;

/**
 * This is the model class for table "user_task".
 */
class UserTask extends BaseUserTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'task_id'], 'required'],
            [['user_id', 'task_id'], 'integer']
        ]);
    }
	
}
