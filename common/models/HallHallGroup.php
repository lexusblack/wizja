<?php

namespace common\models;

use Yii;
use \common\models\base\HallHallGroup as BaseHallHallGroup;

/**
 * This is the model class for table "hall_hall_group".
 */
class HallHallGroup extends BaseHallHallGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_id', 'hall_group_id'], 'integer']
        ]);
    }
	
}
