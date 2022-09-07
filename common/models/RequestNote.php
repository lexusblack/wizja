<?php

namespace common\models;

use Yii;
use \common\models\base\RequestNote as BaseRequestNote;

/**
 * This is the model class for table "request_note".
 */
class RequestNote extends BaseRequestNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['request_id', 'user_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['user_name'], 'string', 'max' => 255]
        ]);
    }
	
}
