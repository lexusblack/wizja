<?php

namespace common\models;

use Yii;
use \common\models\base\UserProvision as BaseUserProvision;

/**
 * This is the model class for table "user_provision".
 */
class UserProvision extends BaseUserProvision
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'type'], 'integer'],
            [['section'], 'string', 'max' => 45],
            [['value'], 'number']
        ]);
    }
	
}
