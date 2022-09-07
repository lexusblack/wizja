<?php

namespace common\models;

use Yii;
use \common\models\base\Deal as BaseDeal;

/**
 * This is the model class for table "deal".
 */
class Deal extends BaseDeal
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
        return Yii::getAlias('@uploads/deal/'.$this->filename);
    }


    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/deal/'.$this->filename);
    }
	
}
