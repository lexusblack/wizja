<?php

namespace common\models;

use Yii;
use \common\models\base\EventField as BaseEventField;

/**
 * This is the model class for table "event_field".
 */
class EventField extends BaseEventField
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'event_field_setting_id'], 'integer'],
            [['value_int'], 'number'],
            [['value_text'], 'string']
        ]);
    }
	
}
