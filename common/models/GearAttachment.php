<?php

namespace common\models;

use Yii;
use \common\models\base\GearAttachment as BaseGearAttachment;

/**
 * This is the model class for table "gear_attachment".
 */
class GearAttachment extends BaseGearAttachment
{
    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/gear-attachment/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/gear-attachment/'.$this->filename);
    }

    public function beforeSave($insert)
    {
            if (!$this->type)
            {  
                $this->type = 1;
            }
            return true;
         
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(4, 'gearAttachmentAdded', $this, $this->gear_id);
            }
         
    }

        public function beforeDelete()
    {
        Note::createNote(4, 'gearAttachmentDeleted', $this, $this->gear_id);
        return true;
    }

    public function getTypeName()
    {
        if ($this->type!=1)
        {
            return GearAttachemntType::findOne($this->type)->name;
        }else{
            return Yii::t('app', 'Bez folderu');
        }
    }
}
