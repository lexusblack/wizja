<?php

namespace common\models;

use Yii;
use \common\models\base\OfferSend as BaseOfferSend;

/**
 * This is the model class for table "offer_send".
 */
class OfferSend extends BaseOfferSend
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id', 'user_id'], 'integer'],
            [['datetime', 'filename'], 'safe'],
            [['recipient'], 'string', 'max' => 255]
        ]);
    }
	
}
