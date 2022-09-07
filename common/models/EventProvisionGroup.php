<?php

namespace common\models;

use Yii;
use \common\models\base\EventProvisionGroup as BaseEventProvisionGroup;

/**
 * This is the model class for table "event_provision_group".
 */
class EventProvisionGroup extends BaseEventProvisionGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['team_id', 'event_id', 'level', 'type', 'main_only', 'add_to_users', 'is_pm', 'customer_group_id'], 'integer'],
            [['provision'], 'number'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
