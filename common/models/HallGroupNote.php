<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupNote as BaseHallGroupNote;

/**
 * This is the model class for table "hall_group_note".
 */
class HallGroupNote extends BaseHallGroupNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['text'], 'string'],
            [['hall_group_id', 'user_id'], 'integer'],
            [['datetime'], 'safe']
        ]);
    }
	
}
