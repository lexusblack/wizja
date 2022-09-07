<?php

namespace common\models;

use Yii;
use \common\models\base\EventHallGroup as BaseEventHallGroup;

/**
 * This is the model class for table "event_hall_group".
 */
class EventHallGroup extends BaseEventHallGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'hall_group_id'], 'integer'],
            [['start_time', 'end_time'], 'safe']
        ]);
    }
	
}
