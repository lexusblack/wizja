<?php

namespace common\models;

use Yii;
use \common\models\base\RentLog as BaseRentLog;

/**
 * This is the model class for table "rent_log".
 */
class RentLog extends BaseRentLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['content'], 'string'],
            [['user_id', 'rent_id'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ]);
    }

    
	
}
