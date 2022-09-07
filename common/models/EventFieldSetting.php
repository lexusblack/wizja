<?php

namespace common\models;

use Yii;
use \common\models\base\EventFieldSetting as BaseEventFieldSetting;

/**
 * This is the model class for table "event_field_setting".
 */
class EventFieldSetting extends BaseEventFieldSetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active', 'type', 'column_in_list', 'visible_on_packlist'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
