<?php

namespace common\models;

use Yii;
use \common\models\base\RfidCommand as BaseRfidCommand;

/**
 * This is the model class for table "rfid_command".
 */
class RfidCommand extends BaseRfidCommand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['reader', 'command'], 'required'],
            [['status'], 'integer'],
            [['create_time', 'done_time'], 'safe'],
            [['reader', 'command', 'content'], 'string', 'max' => 255]
        ]);
    }
	
}
