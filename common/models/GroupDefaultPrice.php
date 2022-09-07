<?php

namespace common\models;

use Yii;
use \common\models\base\GroupDefaultPrice as BaseGroupDefaultPrice;

/**
 * This is the model class for table "group_default_price".
 */
class GroupDefaultPrice extends BaseGroupDefaultPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'gears_price_id', 'price_group_id'], 'integer']
        ]);
    }
	
}
