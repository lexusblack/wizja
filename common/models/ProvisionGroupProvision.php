<?php

namespace common\models;

use Yii;
use \common\models\base\ProvisionGroupProvision as BaseProvisionGroupProvision;

/**
 * This is the model class for table "provision_group_provision".
 */
class ProvisionGroupProvision extends BaseProvisionGroupProvision
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ]);
    }
	
}
