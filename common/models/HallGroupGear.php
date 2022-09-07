<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupGear as BaseHallGroupGear;

/**
 * This is the model class for table "hall_group_gear".
 */
class HallGroupGear extends BaseHallGroupGear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_group_id', 'gear_id', 'quantity'], 'integer']
        ]);
    }
	
}
