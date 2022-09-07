<?php

namespace common\models;

use Yii;
use \common\models\base\TaskNotificationUser as BaseTaskNotificationUser;

/**
 * This is the model class for table "task_notification_user".
 */
class TaskNotificationUser extends BaseTaskNotificationUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'user_id'], 'integer']
        ]);
    }
	
}
