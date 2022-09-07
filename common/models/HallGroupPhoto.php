<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupPhoto as BaseHallGroupPhoto;

/**
 * This is the model class for table "hall_group_photo".
 */
class HallGroupPhoto extends BaseHallGroupPhoto
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['status', 'hall_group_id', 'type'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }

        public function getFileUrl()
    {
        return Yii::getAlias('@uploads/hall/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/hall/'.$this->filename);
    }

    public function beforeSave($insert)
    {
            if (!$this->type)
            {  
                $this->type = 1;
            }
            return true;
         
    }

        public function getTypeName()
    {
        if ($this->type!=1)
        {
            return HallGroupPhotoType::findOne($this->type)->name;
        }else{
            return Yii::t('app', 'Bez folderu');
        }
    }
	
}
