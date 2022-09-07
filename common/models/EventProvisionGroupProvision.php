<?php

namespace common\models;

use Yii;
use \common\models\base\EventProvisionGroupProvision as BaseEventProvisionGroupProvision;

/**
 * This is the model class for table "event_provision_group_provision".
 */
class EventProvisionGroupProvision extends BaseEventProvisionGroupProvision
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ]);
    }
	
}
