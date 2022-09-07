<?php

namespace common\models;

use Yii;
use \common\models\base\TaskNotificationRole as BaseTaskNotificationRole;

/**
 * This is the model class for table "task_notification_role".
 */
class TaskNotificationRole extends BaseTaskNotificationRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'user_event_role'], 'integer']
        ]);
    }
	
}
