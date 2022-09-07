<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\EventInvoice as BaseEventInvoice;

/**
 * This is the model class for table "event_invoice".
 */
class EventInvoice extends BaseEventInvoice
{
    const TYPE_SELL = 1;
    const TYPE_EXPENSE = 2;
    const TYPE_INTERNAL = 3;

    public static function getTypeList()
    {
        $list = [
            self::TYPE_SELL => Yii::t('app', 'Sprzedażowa'),
            self::TYPE_EXPENSE => Yii::t('app', 'Kosztowa'),
            self::TYPE_INTERNAL => Yii::t('app', 'Wewnętrzna'),
        ];
        return $list;
    }

    public function getTypeLabel()
    {
        $index = $this->type;
        $list = static::getTypeList();
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/event-invoice/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/event-invoice/'.$this->filename);
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(2, 'invoiceAttachmentAdded', $this, $this->event_id);
            }
         
    }
}
