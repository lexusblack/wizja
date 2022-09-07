<?php

namespace common\models;

use Yii;
use \common\models\base\InvoiceSend as BaseInvoiceSend;

/**
 * This is the model class for table "invoice_send".
 */
class InvoiceSend extends BaseInvoiceSend
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['invoice_id', 'user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['recipient', 'filename'], 'string', 'max' => 255]
        ]);
    }
	
}
