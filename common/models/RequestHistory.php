<?php

namespace common\models;

use Yii;
use \common\models\base\RequestHistory as BaseRequestHistory;

/**
 * This is the model class for table "request_history".
 */
class RequestHistory extends BaseRequestHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['request_id', 'status', 'user_id'], 'integer'],
            [['datetime'], 'safe']
        ]);
    }
	
}
