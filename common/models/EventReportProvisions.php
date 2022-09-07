<?php

namespace common\models;

use Yii;
use \common\models\base\EventReportProvisions as BaseEventReportProvisions;

/**
 * This is the model class for table "event_report_provisions".
 */
class EventReportProvisions extends BaseEventReportProvisions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'provision_group_id'], 'integer'],
            [['value'], 'number']
        ]);
    }
	
}
