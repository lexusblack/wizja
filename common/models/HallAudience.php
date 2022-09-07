<?php

namespace common\models;

use Yii;
use \common\models\base\HallAudience as BaseHallAudience;

/**
 * This is the model class for table "hall_audience".
 */
class HallAudience extends BaseHallAudience
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_audience_type_id', 'hall_group_id', 'audience'], 'integer']
        ]);
    }


	
}
