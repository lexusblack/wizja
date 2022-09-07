<?php

namespace common\models;

use Yii;
use \common\models\base\GearsPricePercent as BaseGearsPricePercent;

/**
 * This is the model class for table "gears_price_percent".
 */
class GearsPricePercent extends BaseGearsPricePercent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gears_price_id', 'day'], 'integer'],
            [['value'], 'number']
        ]);
    }
	
}
