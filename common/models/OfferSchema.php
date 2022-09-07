<?php

namespace common\models;

use Yii;
use \common\models\base\OfferSchema as BaseOfferSchema;

/**
 * This is the model class for table "offer_schema".
 */
class OfferSchema extends BaseOfferSchema
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
