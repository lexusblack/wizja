<?php

namespace common\models;

use Yii;
use \common\models\base\EventType as BaseEventType;

/**
 * This is the model class for table "event_type".
 */
class EventType extends BaseEventType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
