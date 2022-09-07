<?php

namespace common\models;

use Yii;
use \common\models\base\OfferCost as BaseOfferCost;

/**
 * This is the model class for table "offer_cost".
 */
class OfferCost extends BaseOfferCost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ]);
    }
	
}
