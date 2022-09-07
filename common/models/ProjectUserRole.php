<?php

namespace common\models;

use Yii;
use \common\models\base\ProjectUserRole as BaseProjectUserRole;

/**
 * This is the model class for table "project_user_role".
 */
class ProjectUserRole extends BaseProjectUserRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['project_user_id', 'user_event_role_id'], 'required'],
            [['project_user_id', 'user_event_role_id'], 'integer']
        ]);
    }
	
}
