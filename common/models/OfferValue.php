<?php

namespace common\models;

use Yii;
use \common\models\base\OfferValue as BaseOfferValue;

/**
 * This is the model class for table "offer_value".
 */
class OfferValue extends BaseOfferValue
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
