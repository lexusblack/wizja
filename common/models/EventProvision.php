<?php

namespace common\models;

use Yii;
use \common\models\base\EventProvision as BaseEventProvision;

/**
 * This is the model class for table "event_provision".
 */
class EventProvision extends BaseEventProvision
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 45]
        ]);
    }
	
}
