<?php

namespace backend\modules\offers\models;

use Yii;
use \backend\modules\offers\models\base\OfferExtraItem as BaseOfferExtraItem;
use common\models\OfferLog;
use common\models\OfferGear;
use common\models\OfferOuterGear;
/**
 * This is the model class for table "offer_extra_item".
 */
class OfferExtraItem extends BaseOfferExtraItem {

    const TYPE_GEAR = 1;
    const TYPE_VEHICLE = 2;
    const TYPE_CREW = 3;
    const TYPE_PRODUKCJA = 4;
    public static function getTypes() {
        return [
            self::TYPE_GEAR => Yii::t('app', 'Sprzęt/Grupa'),
            self::TYPE_VEHICLE => Yii::t('app', 'Transport'),
            self::TYPE_CREW => Yii::t('app', 'Obsługa'),
            self::TYPE_PRODUKCJA => Yii::t('app', 'Produkcja'),
        ];
    }

    public function rules() {
        $rules = [
            ['category_id', 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_GEAR;
            },
                'whenClient' => "function (attr, val) {
                    return $('#type_dropdown').val() == 1;
                }",
            ],
        ];

        return array_merge(parent::rules(), $rules);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->quantity == null) {
                $this->quantity = 0;
            }
            if ($insert){
                if (isset($this->gearsPrice))
                    $this->vat_rate = $this->gearsPrice->vat;
                else
                    $this->vat_rate = 23;
            }
            if ($this->type!=1)
            {
                $this->gears_price_id = null;
                if (($this->type==3)&&(!$this->time_type))
                {
                    $this->time_type = 3;
                }
            }
            
            return true;
        } else {
            return false;
        }
    } 

    public function afterSave($insert, $changedAttributes)
    {
         parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            OfferLog::addLog('extra_add', $this, $this->offer_id);
        }else{
            if ($changedAttributes)
            {
                if (isset($changedAttributes['quantity'])&&($this->quantity!=$changedAttributes['quantity']))
                {
                    OfferLog::addLog('extra_edit', $this, $this->offer_id, 'quantity', $changedAttributes['quantity']);
                }
                if (isset($changedAttributes['duration'])&&($this->duration!=$changedAttributes['duration']))
                {
                    OfferLog::addLog('extra_edit', $this, $this->offer_id, 'duration', $changedAttributes['duration']);
                }
                if (isset($changedAttributes['price'])&&($this->price!=$changedAttributes['price']))
                {
                    OfferLog::addLog('extra_edit', $this, $this->offer_id, 'price', $changedAttributes['price']);
                }
                if (isset($changedAttributes['first_day_percent'])&&($this->first_day_percent!=$changedAttributes['first_day_percent']))
                {
                    OfferLog::addLog('extra_edit', $this, $this->offer_id, 'first_day_percent', $changedAttributes['first_day_percent']);
                }
            }
        }
    }

    public function beforeDelete()
    {
        OfferLog::addLog('extra_delete', $this, $this->offer_id);
        OfferGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_group_id'=>$this->id]);
        OfferOuterGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_group_id'=>$this->id]);
        return true;
    }

    public function getValue()
    {
        $price = $this->price;
        $price_with_discount = $price * (1 - $this->discount/100);
        if (isset($this->gearsPrice))
            return $this->gearsPrice->calculateValue($price_with_discount, $this->quantity, $this->duration);
        else
            if ($this->duration>=1)
                return $price_with_discount*$this->quantity+($this->duration-1)*$price_with_discount*$this->quantity*$this->first_day_percent/100;
            else
                return $price_with_discount*$this->quantity*$this->duration;
    }

    public function getVatValue()
    {
        $value = $this->getValue();
        if ($this->vat_rate===null)
        {
            $vat = 0;
        }else{
            $vat = $this->vat_rate;
        }
        return round($value*$vat/100,2);
    }


}
