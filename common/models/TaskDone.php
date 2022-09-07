<?php

namespace common\models;

use Yii;
use \common\models\base\TaskDone as BaseTaskDone;

/**
 * This is the model class for table "task_done".
 */
class TaskDone extends BaseTaskDone
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'user_id', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['note'], 'string']
        ]);
    }
	
}
