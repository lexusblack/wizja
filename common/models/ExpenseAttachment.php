<?php

namespace common\models;

use Yii;
use \common\models\base\ExpenseAttachment as BaseExpenseAttachment;

/**
 * This is the model class for table "expense_attachment".
 */
class ExpenseAttachment extends BaseExpenseAttachment
{
    const UPLOAD_DIR = 'expense-attachment';

    public function getFileUrl()
    {
        $url = $this->loadFileUrl('filename', '@uploads/'.static::UPLOAD_DIR.'/');
        return $url;
    }

    public function getFilePath()
    {
        $url = $this->loadFileUrl('filename', '@uploadroot/'.static::UPLOAD_DIR.'/');
        return $url;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(4, 'expenseAttachmentAdded', $this, $this->id);
            }
         
    }
}
