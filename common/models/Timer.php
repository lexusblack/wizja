<?php

namespace common\models;

use \common\models\base\Timer as BaseTimer;
use Yii;
/**
 * This is the model class for table "timer".
 */
class Timer extends BaseTimer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['create_time', 'update_time'], 'safe'],
            [['name', 'filename'], 'string', 'max' => 255]
        ]);
    }
    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/timer/'.$this->filename);
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/timer/'.$this->filename);
    }
	
}
