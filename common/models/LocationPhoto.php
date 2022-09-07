<?php

namespace common\models;

use Yii;
use \common\models\base\LocationPhoto as BaseLocationPhoto;

/**
 * This is the model class for table "location_photo".
 */
class LocationPhoto extends BaseLocationPhoto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['status', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }

    public function getFilePath()
    {
        if ($this->location->public==2)
            return Yii::getAlias('@uploadroot/location-photo/'.$this->filename);
        else
            return Yii::getAlias('@uploadrootAll/location-photo/'.$this->filename);
    }
    public function getFileUrl()
    {
        if ($this->location->public==2)
                    return Yii::getAlias('@uploadsAll/location-photo/'.$this->filename);
                else
                    return Yii::getAlias('@uploads/location-photo/'.$this->filename);
    }
}
