<?php

namespace common\models;

use Yii;
use \common\models\base\OfferProvisionGroup as BaseOfferProvisionGroup;

/**
 * This is the model class for table "offer_provision_group".
 */
class OfferProvisionGroup extends BaseOfferProvisionGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_id', 'team_id', 'level', 'type', 'main_only', 'add_to_users', 'is_pm', 'customer_group_id'], 'integer'],
            [['provision'], 'number'],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
}
