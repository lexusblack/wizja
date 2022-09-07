<?php

namespace common\models;

use Yii;
use \common\models\base\TasksSchema as BaseTasksSchema;

/**
 * This is the model class for table "tasks_schema".
 */
class TasksSchema extends BaseTasksSchema
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['default', 'type', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }


	
}
