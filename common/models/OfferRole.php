<?php

namespace common\models;

use Yii;
use \common\models\base\OfferRole as BaseOfferRole;

/**
 * This is the model class for table "offer_role".
 */
class OfferRole extends BaseOfferRole
{
    public function beforeSave($insert)
    {
        if ($this->price === null)
        {
            $this->price = $this->role->salary_customer;
        }
        if ($insert)
        {
            $this->salary_type = $this->role->default_salary;
        }
        return parent::beforeSave($insert);
    }

    public function getValue()
    {
        return $this->duration * $this->price * $this->quantity;
    }



    public function afterSave($insert, $changedAttributes)
    {
         parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            OfferLog::addLog('role_add', $this, $this->offer_id);
        }else{
            if ($changedAttributes)
            {
                if (isset($changedAttributes['quantity'])&&($this->quantity!=$changedAttributes['quantity']))
                {
                    OfferLog::addLog('role_edit', $this, $this->offer_id, 'quantity', $changedAttributes['quantity']);
                }
                if (isset($changedAttributes['duration'])&&($this->duration!=$changedAttributes['duration']))
                {
                    OfferLog::addLog('role_edit', $this, $this->offer_id, 'duration', $changedAttributes['duration']);
                }
                if (isset($changedAttributes['price'])&&($this->price!=$changedAttributes['price']))
                {
                    OfferLog::addLog('role_edit', $this, $this->offer_id, 'price', $changedAttributes['price']);
                }
            }
        }
    }

    public function beforeDelete()
    {
        OfferLog::addLog('role_delete', $this, $this->offer_id);
        return true;
    }
}
