<?php

namespace common\models;

use Yii;
use \common\models\base\OfferCustomItems as BaseOfferCustomItems;

/**
 * This is the model class for table "offer_custom_items".
 */
class OfferCustomItems extends BaseOfferCustomItems
{
	 public function getValue()
    {
        $price = $this->price;
        $price_with_discount = $price * (1 - $this->discount/100);
       return $price_with_discount*$this->diff_count;
    }
}
