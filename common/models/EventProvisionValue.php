<?php

namespace common\models;

use Yii;
use \common\models\base\EventProvisionValue as BaseEventProvisionValue;

/**
 * This is the model class for table "event_provision_value".
 */
class EventProvisionValue extends BaseEventProvisionValue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'provision_group_id'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ]);
    }
	
}
