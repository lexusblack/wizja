<?php

namespace common\models;

use Yii;
use \common\models\base\InvoiceContent as BaseInvoiceContent;

/**
 * This is the model class for table "invoice_content".
 */
class InvoiceContent extends BaseInvoiceContent
{
    public function loadTmpName()
    {
        $class = $this->item_class;
        $model = $class::findOne($this->item_id);
        $this->item_tmp_name = $this->item_id;
    }

    public function loadOwnerModel()
    {
        $model = null;
        if (empty($this->item_class) == false)
        {
            $class = $this->item_class;
            $model = $class::findOne($this->item_id);
        }
        return $model;
    }

    public function beforeValidate()
    {
        $this->price = str_replace(' ', '', $this->price);
        if (!$this->discount_percent)
            $this->discount_percent = 0;
        $this->discount = $this->price*$this->count * ($this->discount_percent/100);
        $this->netto = $this->price*$this->count - $this->discount;
        $this->tax = $this->netto*($this->vat/100);
        $this->brutto = $this->netto + $this->tax;

        return parent::beforeValidate();
    }

    public function loadGearItem($model)
    {

    }
}
