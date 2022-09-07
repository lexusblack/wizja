<?php

namespace common\models;

use Yii;
use \common\models\base\RfidLog as BaseRfidLog;

/**
 * This is the model class for table "rfid_log".
 */
class RfidLog extends BaseRfidLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['datetime', 'tag', 'reader'], 'required'],
            [['datetime'], 'safe'],
            [['tag', 'reader'], 'string', 'max' => 255]
        ]);
    }
	
}
