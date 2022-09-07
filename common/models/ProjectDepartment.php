<?php

namespace common\models;

use Yii;
use \common\models\base\ProjectDepartment as BaseProjectDepartment;

/**
 * This is the model class for table "project_department".
 */
class ProjectDepartment extends BaseProjectDepartment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['project_id', 'department_id'], 'required'],
            [['project_id', 'department_id'], 'integer'],
            [['project_id', 'department_id'], 'unique', 'targetAttribute' => ['project_id', 'department_id'], 'message' => 'The combination of Project ID and Department ID has already been taken.']
        ]);
    }
	
}
