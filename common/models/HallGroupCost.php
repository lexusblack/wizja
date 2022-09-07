<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupCost as BaseHallGroupCost;

/**
 * This is the model class for table "hall_group_cost".
 */
class HallGroupCost extends BaseHallGroupCost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_group_id'], 'integer'],
            [['cost'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 45]
        ]);
    }
	
}
