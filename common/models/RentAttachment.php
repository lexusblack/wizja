<?php

namespace common\models;

use Yii;
use \common\models\base\RentAttachment as BaseRentAttachment;

/**
 * This is the model class for table "rent_attachment".
 */
class RentAttachment extends BaseRentAttachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['type', 'status', 'rent_id', 'public'], 'integer'],
            [['content', 'info'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['rent_id'], 'required'],
            [['filename', 'extension', 'mime_type', 'base_name'], 'string', 'max' => 255]
        ]);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/rent/'.$this->filename);
    }
	
}
