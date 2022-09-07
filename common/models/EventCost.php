<?php

namespace common\models;

use Yii;
use \common\models\base\EventCost as BaseEventCost;

/**
 * This is the model class for table "event_cost".
 */
class EventCost extends BaseEventCost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 45]
        ]);
    }
	
}
