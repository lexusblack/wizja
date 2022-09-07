<?php

namespace common\models;

use Yii;
use \common\models\base\EventRequest as BaseEventRequest;

/**
 * This is the model class for table "event_request".
 */
class EventRequest extends BaseEventRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'request_id'], 'integer']
        ]);
    }
	
}
