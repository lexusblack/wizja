<?php

namespace common\models;

use Yii;
use \common\models\base\EventExtraItem as BaseEventExtraItem;

/**
 * This is the model class for table "event_extra_item".
 */
class EventExtraItem extends BaseEventExtraItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_extra_item_id', 'quantity', 'gear_category_id'], 'integer'],
            [['weight', 'volume'], 'number'],
            [['name'], 'string', 'max' => 255]
        ]);
    }


	
}
