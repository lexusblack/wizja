<?php

namespace common\models;

use Yii;
use \common\models\base\InvoiceAttachment as BaseInvoiceAttachment;

/**
 * This is the model class for table "invoice_attachment".
 */
class InvoiceAttachment extends BaseInvoiceAttachment
{
    const UPLOAD_DIR = 'invoice-attachment';

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
}
