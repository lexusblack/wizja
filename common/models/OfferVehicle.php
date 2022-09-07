<?php

namespace common\models;

use Yii;
use \common\models\base\OfferVehicle as BaseOfferVehicle;

/**
 * This is the model class for table "offer_vehicle".
 */
class OfferVehicle extends BaseOfferVehicle
{
    const PRICE_TYPE_KM = 1;
    const PRICE_TYPE_CITY = 2;

	public static function assign($attributes)
    {   /* @var $model static */

        $className = static::className();
        $model = new $className($attributes);

        if (($model->offer->isOfferInCompanyCity() == true)||($model->offer->getVehicleType()==2))
        {
            $model->price = $model->vehicle->price_city;
            $model->distance = 0;
            $model->price_type = self::PRICE_TYPE_CITY;
        }
        else
        {
            $model->price = $model->vehicle->price_km;
            if ($model->offer->location) {
                $model->distance = $model->offer->location->getGoogleDistance() * 2;
            }
            else {
                $model->distance = 0;
            }
            $model->price_type = self::PRICE_TYPE_KM;
        }

        return $model->save();
    }

    public function changePriceType($type) {
	    if ($type == self::PRICE_TYPE_KM) {
            $this->price_type = self::PRICE_TYPE_KM;
            $this->price = $this->vehicle->price_km;
            $this->save();
            return true;
        }
        if ($type == self::PRICE_TYPE_CITY) {
            $this->price_type = self::PRICE_TYPE_CITY;
            $this->price = $this->vehicle->price_city;
            $this->save();
            return true;
        }
        return false;
    }

    public static function remove($attributes)
    {
        return static::deleteAll($attributes);
    }

    public function afterSave($insert, $changedAttributes)
    {
         parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            OfferLog::addLog('vehicle_add', $this, $this->offer_id);
        }else{
            if ($changedAttributes)
            {
                if (isset($changedAttributes['quantity'])&&($this->quantity!=$changedAttributes['quantity']))
                {
                    OfferLog::addLog('vehicle_edit', $this, $this->offer_id, 'quantity', $changedAttributes['quantity']);
                }
                if (isset($changedAttributes['distance'])&&($this->distance!=$changedAttributes['distance']))
                {
                    OfferLog::addLog('vehicle_edit', $this, $this->offer_id, 'distance', $changedAttributes['distance']);
                }
                if (isset($changedAttributes['price'])&&($this->price!=$changedAttributes['price']))
                {
                    OfferLog::addLog('vehicle_edit', $this, $this->offer_id, 'price', $changedAttributes['price']);
                }
            }
        }
    }

    public function beforeDelete()
    {
        OfferLog::addLog('vehicle_delete', $this, $this->offer_id);
        return true;
    }

    public function getValue()
    {
        if ($this->price_type==1)
                    {
                        $value = $this->price;
                        $distance = $this->distance;
                        $price = $this->quantity*$value*$distance;
                    }else{
                        $value = $this->price;
                        $price = $this->quantity*$value;
                    }
        return $price;
    }
}
