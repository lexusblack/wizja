<?php

namespace common\models;

use Yii;
use \common\models\base\TasksSchemaCat as BaseTasksSchemaCat;

/**
 * This is the model class for table "tasks_schema_cat".
 */
class TasksSchemaCat extends BaseTasksSchemaCat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['tasks_schema_id', 'order'], 'integer'],
            [['name', 'color'], 'string', 'max' => 255]
        ]);
    }
	
}
