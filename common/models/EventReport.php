<?php

namespace common\models;

use Yii;
use \common\models\base\EventReport as BaseEventReport;

/**
 * This is the model class for table "event_report".
 */
class EventReport extends BaseEventReport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'manager_id', 'customer_id', 'status', 'event_model_id', 'event_type_id'], 'integer'],
            [['event_start', 'event_end', 'paying_date'], 'safe'],
            [['total_value', 'total_cost', 'total_provision', 'total_predicted_cost', 'total_predicted_provision'], 'number'],
            [['name', 'location'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ]);
    }
	
}
