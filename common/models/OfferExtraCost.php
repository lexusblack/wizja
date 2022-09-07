<?php

namespace common\models;

use Yii;
use \common\models\base\OfferExtraCost as BaseOfferExtraCost;

/**
 * This is the model class for table "offer_extra_cost".
 */
class OfferExtraCost extends BaseOfferExtraCost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id', 'quantity', 'offer_extra_item_id'], 'integer'],
            [['cost'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['section'], 'string', 'max' => 45]
        ]);
    }
	
}
