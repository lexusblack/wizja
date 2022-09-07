<?php

namespace common\models;

use Yii;
use \common\models\base\OfferDraft as BaseOfferDraft;

/**
 * This is the model class for table "offer_draft".
 */
class OfferDraft extends BaseOfferDraft
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['price_group_id', 'firm_id'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ]);
    }
	
}
