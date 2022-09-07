<?php

namespace common\models;

use Yii;
use \common\models\base\RequestRead as BaseRequestRead;

/**
 * This is the model class for table "request_read".
 */
class RequestRead extends BaseRequestRead
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['id', 'request_id', 'user_id', 'type'], 'integer']
        ]);
    }
	
}
