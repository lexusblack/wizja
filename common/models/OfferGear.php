<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\OfferGear as BaseOfferGear;
use backend\modules\offers\models\OfferExtraItem;

/**
 * This is the model class for table "offer_gear".
 */
class OfferGear extends BaseOfferGear
{
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

    public function getParentName()
    {
        if ($this->type==1)
        {
            return "Bez grupy";
        } 
        if ($this->offer_gear_id)
        {
            $og = OfferGear::findOne($this->offer_gear_id);
            return $og->gear->name;
        }
        if ($this->offer_outer_gear_id)
        {
            $og = OfferOuterGear::findOne($this->offer_outer_gear_id);
            return $og->outerGearModel->name;
        }
        if ($this->offer_group_id)
        {
            $og = OfferExtraItem::findOne($this->offer_group_id);
            return $og->name;
        }
        return "";
    }

    public static function getOtherLabel($offer_id, $gear_id, $type, $item)
    {
        $params = ['offer_id'=>$offer_id, 'gear_id'=>$gear_id];
        $id = false;
        if (!$type)
        {
            $params['type'] = 2;
            $ogs = OfferGear::find()->where($params)->all();
        }else{
            if ($type==='gear')
            {
                $id = OfferGear::find()->where($params)->andWhere(['offer_gear_id'=>$item])->asArray()->one();

            }
            if ($type==='outerGear')
            {
                $id = OfferGear::find()->where($params)->andWhere(['offer_outer_gear_id'=>$item])->asArray()->one();
            }
            if ($type==='extraGear')
            {
                $id = OfferGear::find()->where($params)->andWhere(['offer_group_id'=>$item])->asArray()->one();
            }
            if ($id)
            {
                $ogs = OfferGear::find()->where($params)->andWhere(['<>', 'id', $id['id']])->all();
            }else{
                $ogs = OfferGear::find()->where($params)->all();
            }
        }
        if ($ogs)
        {
            $content = "";
            foreach ($ogs as $og)
            {
                $content.=$og->getParentName()." ".$og->quantity." szt.</br>";
            }
            return $content;

        }else{
            return "";
        }
        return "-";
    }

	public function rules()
    {
    	$rules = parent::rules();
//    	$rules[] = [['quantity'], 'checkQuantity'];
    	$rules[] = [['quantity'], 'number', 'min' => 0];
    	$rules[] = [['discount'], 'integer', 'min' => 0, 'max' => 100];
        return $rules;
    }

    public function getValue()
    {
        $price = $this->price;
        if ($price == null)
            $price = (float)$this->gear->price;
        $price_with_discount = $price * (1 - $this->discount/100);
        if (isset($this->gearsPrice))
            return $this->gearsPrice->calculateValue($price_with_discount, $this->quantity, $this->duration);
        else
            if ($this->duration>=1)
                return $price_with_discount*$this->quantity+($this->duration-1)*$price_with_discount*$this->quantity*$this->first_day_percent/100;
            else
                return $price_with_discount*$this->quantity*$this->duration;
        //$firstDay = $this->quantity * $price_with_discount;
        //$value = $firstDay;
        //$value += $this->quantity * ($this->duration-1)*($price_with_discount*($this->first_day_percent/100));
        //return $value;
    }

    public function getVatValue()
    {
        $value = $this->getValue();
        if ($this->vat_rate===null)
        {
            if (isset($this->gearsPrice))
                $vat = $this->gearsPrice->vat;
            else
                $vat = 0;
        }else{
            $vat = $this->vat_rate;
        }
        return round($value*$vat/100,2);
    }

    public function checkQuantity()
    {
    	$gear = Gear::findOne($this->gear_id);
    	if(($gear->quantity - $this->quantity) < 0){
    		$this->addError('quantity', Yii::t('app', 'Sztuk na stanie tylko {0, number}.', [$gear->quantity]));
    	}

    	// if( $this->quantity < 0){
    	// 	$this->addError('quantity', 'Liczba nie może być mniejsza 0.'.$gear->quantity.'.');
    	// }
    }

    // public function checkDiscount()
    // {
    // 	$gear = Gear::findOne($this->gear_id);
    // 	if( >= 0){
    // 		$this->addError('discount', 'Sztuk na stanie tylko '.$gear->quantity.'.');
    // 	}
    // }

    public function loadOfferSettings()
    {
        $params = [
            'type'=>OfferSetting::TYPE_GEAR,
            'offer_id' => $this->offer_id,
            'category_id'=>$this->gear->category->getMainCategory()->id,
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

    public function updateCost($insert, $changedAttributes)
    {
        $cost = OfferExtraCost::findOne(['offer_gear_id'=>$this->id]);
        if (!$cost)
        {
            if (isset($this->gearsPrice))
            {
                $price = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id, 'gear_id'=>$this->gear_id])->one();
                if ($price)
                {
                    $cost = new OfferExtraCost();
                    $cost->offer_gear_id = $this->id;
                    $cost->name = $price->cost_name." [".$this->gear->name."]";
                    $cost->cost = $price->cost;
                    $cost->quantity = $this->quantity;
                    if ($price->one_per_event)
                        $cost->cost = $cost->cost*$this->duration;
                    $cost->offer_id = $this->offer_id;
                    $cost->section = $this->gear->getMainCategory()->name;
                    $cost->save();
                }

            }

        }else{
            if (isset($changedAttributes['gears_price_id'])&&($this->gears_price_id!=$changedAttributes['gears_price_id']))
            {
                    if (isset($this->gearsPrice))
                    {
                        $price = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id, 'gear_id'=>$this->gear_id])->one();
                        if ($price)
                        {
                            $cost->name = $price->cost_name;
                            $cost->cost = $price->cost;
                        }
                    }
            }
            $cost->quantity = $this->quantity;
            if (isset($this->gearsPrice))
                    {
                        $price = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id, 'gear_id'=>$this->gear_id])->one();
                        if ($price)
                        {
                            if ($price->one_per_event)
                                $cost->cost = $cost->cost*$this->duration;
                        }
                    }
            
            $cost->save();
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
         parent::afterSave($insert, $changedAttributes);
         $this->updateCost($insert, $changedAttributes);
        if ($insert)
        {
            OfferLog::addLog('gear_add', $this, $this->offer_id);
        }else{
            if ($changedAttributes)
            {
                if (isset($changedAttributes['quantity'])&&($this->quantity!=$changedAttributes['quantity']))
                {
                    OfferLog::addLog('gear_edit', $this, $this->offer_id, 'quantity', $changedAttributes['quantity']);
                }
                if (isset($changedAttributes['duration'])&&($this->duration!=$changedAttributes['duration']))
                {
                    OfferLog::addLog('gear_edit', $this, $this->offer_id, 'duration', $changedAttributes['duration']);
                }
                if (isset($changedAttributes['price'])&&($this->price!=$changedAttributes['price']))
                {
                    OfferLog::addLog('gear_edit', $this, $this->offer_id, 'price', $changedAttributes['price']);
                }
                if (isset($changedAttributes['first_day_percent'])&&($this->first_day_percent!=$changedAttributes['first_day_percent']))
                {
                    OfferLog::addLog('gear_edit', $this, $this->offer_id, 'first_day_percent', $changedAttributes['first_day_percent']);
                }
            }
                
        }
    }

    public function beforeDelete()
    {
        OfferLog::addLog('gear_delete', $this, $this->offer_id);
        OfferGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_gear_id'=>$this->id]);
        OfferOuterGear::deleteAll(['offer_id'=>$this->offer_id, 'offer_gear_id'=>$this->id]);
        OfferExtraCost::deleteAll(['offer_gear_id'=>$this->id]);
        return true;
    }
}
