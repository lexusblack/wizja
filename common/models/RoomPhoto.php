<?php

namespace common\models;
use Yii;
use \common\models\base\RoomPhoto as BaseRoomPhoto;

/**
 * This is the model class for table "room_photo".
 */
class RoomPhoto extends BaseRoomPhoto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['status', 'room_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }
	
    public function getFilePath()
    {
        if ($this->room->location->public==2)
            return Yii::getAlias('@uploadroot/room-photo/'.$this->filename);
        else
            return Yii::getAlias('@uploadrootAll/room-photo/'.$this->filename);
    }
    public function getFileUrl()
    {
        if ($this->room->location->public==2)
                    return Yii::getAlias('@uploadsAll/room-photo/'.$this->filename);
                else
                    return Yii::getAlias('@uploads/room-photo/'.$this->filename);
    }  
}
