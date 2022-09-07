<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupPricePercent as BaseHallGroupPricePercent;

/**
 * This is the model class for table "hall_group_price_percent".
 */
class HallGroupPricePercent extends BaseHallGroupPricePercent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_group_price_id', 'day'], 'integer'],
            [['value'], 'number']
        ]);
    }
	
}
