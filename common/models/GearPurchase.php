<?php

namespace common\models;

use Yii;
use \common\models\base\GearPurchase as BaseGearPurchase;

/**
 * This is the model class for table "gear_purchase".
 */
class GearPurchase extends BaseGearPurchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'quantity', 'customer_id', 'expense_id', 'user_id'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['datetime'], 'safe']
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            $gear = $this->gear;
            $gear->quantity+=$this->quantity;
            $gear->save();
        }else{
            if ((isset($changedAttributes['quantity']))&&($changedAttributes['quantity']!=$this->quantity))
            {
                $gear = $this->gear;
                $gear->quantity+=$this->quantity-$changedAttributes['quantity'];
                $gear->save();                
            }
        }
    }

    public function beforeDelete()
    {
        $gear = $this->gear;
        $gear->quantity-=$this->quantity;
        $gear->save();
        return true;
    }
	
}
