<?php

namespace common\models;

use Yii;
use \common\models\base\OfferRoleSchemaItem as BaseOfferRoleSchemaItem;

/**
 * This is the model class for table "offer_role_schema_item".
 */
class OfferRoleSchemaItem extends BaseOfferRoleSchemaItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['role_id', 'quantity', 'duration'], 'number'],
            [['price'], 'number'],
            [['time_type'], 'safe']
        ]);
    }
	
}
