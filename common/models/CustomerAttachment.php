<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerAttachment as BaseCustomerAttachment;

/**
 * This is the model class for table "customer_attachment".
 */
class CustomerAttachment extends BaseCustomerAttachment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['customer_id'], 'integer'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ]);
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/customer-attachment/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/customer-attachment/'.$this->filename);
    }
	
    public function beforeDelete()
    {
        $this->customer->createLog('file_delete', $this->id);
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {
            
                $customer = Customer::findOne($this->customer_id);     
                $customer->createLog('file_create', $this->id);   
                Note::createNote(4, 'customerAttachmentAdded', $this, $this->customer_id);
            }
         
    }
}
