<?php

namespace common\models;

use Yii;
use \common\models\base\TaskCategory as BaseTaskCategory;

/**
 * This is the model class for table "task_category".
 */
class TaskCategory extends BaseTaskCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['type', 'event_id', 'order'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
