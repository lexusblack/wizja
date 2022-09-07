<?php

namespace common\models;

use Yii;
use \common\models\base\EventStatut as BaseEventStatut;

/**
 * This is the model class for table "event_statut".
 */
class EventStatut extends BaseEventStatut
{
    /**
     * @inheritdoc
     */

    public $users;
    public $roles;
    public $permissions;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['position', 'active', 'blocks_costs', 'blocks_working_times', 'blocks_status_revert', 'blocks_gear', 'blocks_event', 'reminder', 'type', 'reminder_pm', 'reminder_mail', 'reminder_sms', 'delete_gear', 'delete_crew', 'delete_task', 'button', 'show_in_dashboard', 'show_in_plantimeline', 'border', 'button', 'count_provision'], 'integer'],
            [['reminder_text'], 'string'],
            [['name', 'reminder_roles', 'reminder_users', 'permission_users'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45],
            [['users', 'roles', 'permissions'], 'safe'],
        ]);
    }

    public function beforeSave($insert)
    {
            if ($this->users)
                $this->reminder_users = implode(';', $this->users);
            else
                $this->reminder_users = "";
            if ($this->permissions)
                $this->permission_users = implode(';', $this->permissions);
            else
                $this->permission_users = "";
            if ($this->roles)
                $this->reminder_roles = implode(';', $this->roles);
            else
                $this->reminder_roles = "";

        return parent::beforeSave($insert);
    }
	
}
