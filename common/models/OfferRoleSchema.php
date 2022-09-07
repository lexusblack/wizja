<?php

namespace common\models;

use Yii;
use \common\models\base\OfferRoleSchema as BaseOfferRoleSchema;

/**
 * This is the model class for table "offer_role_schema".
 */
class OfferRoleSchema extends BaseOfferRoleSchema
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
