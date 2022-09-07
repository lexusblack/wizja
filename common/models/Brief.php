<?php

namespace common\models;

use Yii;
use \common\models\base\Brief as BaseBrief;

/**
 * This is the model class for table "brief".
 */
class Brief extends BaseBrief
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['create_time', 'update_time'], 'safe'],
            [['event_id'], 'required'],
            [['event_id'], 'integer'],
            [['filename', 'extension', 'mime_type', 'base_name'], 'string', 'max' => 255]
        ]);
    }
    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/brief/'.$this->filename);
    }


    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/brief/'.$this->filename);
    }	
}
