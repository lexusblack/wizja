<?php

namespace common\models;

use \common\models\base\EventLog as BaseEventLog;

/**
 * This is the model class for table "event_log".
 */
class EventLog extends BaseEventLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['content'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['create_time', 'update_time'], 'safe']
        ]);
    }
	
}
