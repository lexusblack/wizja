<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupPrice as BaseHallGroupPrice;

/**
 * This is the model class for table "hall_group_price".
 */
class HallGroupPrice extends BaseHallGroupPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['hall_group_id', 'default'], 'integer'],
            [['price', 'vat'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 45]
        ]);
    }
	
}
