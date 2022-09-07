<?php

namespace common\models;

use Yii;
use \common\models\base\TaskRole as BaseTaskRole;

/**
 * This is the model class for table "task_role".
 */
class TaskRole extends BaseTaskRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'user_event_role_id'], 'integer']
        ]);
    }
	
}
