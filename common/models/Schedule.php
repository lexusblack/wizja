<?php

namespace common\models;

use Yii;
use \common\models\base\Schedule as BaseSchedule;

/**
 * This is the model class for table "schedule".
 */
class Schedule extends BaseSchedule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['schedule_type_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['name', 'color', 'prefix'], 'string', 'max' => 255]
        ]);
    }
	
}
