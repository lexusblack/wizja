<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerLog as BaseCustomerLog;

/**
 * This is the model class for table "customer_log".
 */
class CustomerLog extends BaseCustomerLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['content'], 'string'],
            [['user_id', 'customer_id'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ]);
    }
	
}
