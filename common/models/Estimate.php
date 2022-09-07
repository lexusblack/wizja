<?php

namespace common\models;

use Yii;
use \common\models\base\Estimate as BaseEstimate;

/**
 * This is the model class for table "estimate".
 */
class Estimate extends BaseEstimate
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
        return Yii::getAlias('@uploads/estimate/'.$this->filename);
    }


    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/estimate/'.$this->filename);
    }	
}
