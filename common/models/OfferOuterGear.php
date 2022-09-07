<?php

namespace common\models;
use common\helpers\ArrayHelper;

use Yii;
use \common\models\base\OfferOuterGear as BaseOfferOuterGear;

/**
 * This is the model class for table "offer_outer_gear".
 */
class OfferOuterGear extends BaseOfferOuterGear
{

	public function loadOfferSettings()
    {
        $params = [
            'type'=>OfferSetting::TYPE_GEAR,
            'offer_id' => $this->offer_id,
            'category_id'=>$this->outerGearModel->category->getMainCategory()->id,
        ];

        $model = OfferSetting::findOne($params);
        if ($model === null)
        {
            $discountList = $this->offer->customer->getDiscountsList();
            $model = new OfferSetting($params);


            $model->discount = ArrayHelper::getValue($discountList, $model->category_id, 0);
            $model->duration = 1;
            $model->first_day_percent = Yii::$app->settings->get('firstDayPercent','offer', 50);
            $model->save();
        }
        if ($this->discount === null)
        {
            $this->discount = $model->discount;
        }

        if ($this->first_day_percent === null)
        {
            $this->first_day_percent = $model->first_day_percent;
        }

        if ($this->duration === null)
        {
            $this->duration = $model->duration;
        }
    }

    public function getValue()
    {
        $price = $this->price;
        if ($price == null)
            $price = (float)$this->outerGearModel->getSellingPrice();
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
            OfferLog::addLog('outer_add', $this, $this->offer_id);
        }else{
            if ($changedAttributes)
            {
                if (isset($changedAttributes['quantity'])&&($this->quantity!=$changedAttributes['quantity']))
                {
                    OfferLog::addLog('outer_edit', $this, $this->offer_id, 'quantity', $changedAttributes['quantity']);
                }
                if (isset($changedAttributes['duration'])&&($this->duration!=$changedAttributes['duration']))
                {
                    OfferLog::addLog('outer_edit', $this, $this->offer_id, 'duration', $changedAttributes['duration']);
                }
                if (isset($changedAttributes['price'])&&($this->price!=$changedAttributes['price']))
                {
                    OfferLog::addLog('outer_edit', $this, $this->offer_id, 'price', $changedAttributes['price']);
                }
                if (isset($changedAttributes['first_day_percent'])&&($this->first_day_percent!=$changedAttributes['first_day_percent']))
                {
                    OfferLog::addLog('outer_edit', $this, $this->offer_id, 'first_day_percent', $changedAttributes['first_day_percent']);
                }
            }
        }
    }

    public function beforeDelete()
    {
        OfferLog::addLog('outer_delete', $this, $this->offer_id);
        OfferGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_outer_gear_id'=>$this->id]);
        OfferOuterGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_outer_gear_id'=>$this->id]);
        return true;
    }
}
