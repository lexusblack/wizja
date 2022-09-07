<?php

namespace common\models;

use Yii;
use \common\models\base\TaskSchemaNotification as BaseTaskSchemaNotification;

/**
 * This is the model class for table "task_schema_notification".
 */
class TaskSchemaNotification extends BaseTaskSchemaNotification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_schema_id', 'time_type', 'time', 'email', 'sms', 'push'], 'integer']
        ]);
    }
	
}
