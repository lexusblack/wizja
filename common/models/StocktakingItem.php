<?php

namespace common\models;

use Yii;
use \common\models\base\StocktakingItem as BaseStocktakingItem;

/**
 * This is the model class for table "stocktaking_item".
 */
class StocktakingItem extends BaseStocktakingItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'gear_item_id', 'stocktaking_id', 'quantity'], 'integer'],
            [['datetime'], 'safe']
        ]);
    }
	
}
