<?php

namespace common\models;

use Yii;
use \common\models\base\RfidAntenna as BaseRfidAntenna;

/**
 * This is the model class for table "rfid_antenna".
 */
class RfidAntenna extends BaseRfidAntenna
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'parameters'], 'required'],
            [['parameters'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ]);
    }
	
}
