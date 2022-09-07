<?php

namespace common\models;

use Yii;
use \common\models\base\OfferProvisionGroupProvision as BaseOfferProvisionGroupProvision;

/**
 * This is the model class for table "offer_provision_group_provision".
 */
class OfferProvisionGroupProvision extends BaseOfferProvisionGroupProvision
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['offer_provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ]);
    }
	
}
