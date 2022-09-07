<?php

namespace common\models;

use Yii;
use \common\models\base\ScheduleType as BaseScheduleType;

/**
 * This is the model class for table "schedule_type".
 */
class ScheduleType extends BaseScheduleType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string', 'max' => 45],
            [['default'], 'safe']
        ]);
    }
	
}
