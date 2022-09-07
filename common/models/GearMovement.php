<?php

namespace common\models;

use Yii;
use \common\models\base\GearMovement as BaseGearMovement;

/**
 * This is the model class for table "gear_movement".
 */
class GearMovement extends BaseGearMovement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['type', 'gear_id', 'user_id', 'quantity', 'datetime'], 'required'],
            [['type', 'gear_id', 'user_id', 'quantity', 'warehouse_from', 'warehouse_to'], 'integer'],
            [['datetime', 'info'], 'safe']
        ]);
    }

    public function getTypeLabel()
    {
        if ($this->type==1)
        {
            return Yii::t('app', 'Dodanie');
        }
        if ($this->type==2)
        {
            return Yii::t('app', 'Usunięcie');
        }
        if ($this->type==3)
        {
            return Yii::t('app', 'Przesunięcie');
        }
    }

    public function validateQuantites($check=false)
    {
        $return = true;
        if ($this->type==1)
        {
                if (!$this->warehouse_to)
                {
                        $this->addError('warehouse_to', Yii::t('app', 'Pole obowiązkowe'));
                        $return = false;
                }
        }
        if ($this->type==2)
        {
                if (!$this->warehouse_from)
                {
                        $this->addError('warehouse_from', Yii::t('app', 'Pole obowiązkowe'));
                        $return = false;
                }else{
                    $wq = WarehouseQuantity::find()->where(['gear_id'=>$this->gear_id, 'warehouse_id'=>$this->warehouse_from])->one();
                    if ($wq->quantity<$this->quantity)
                    {
                        $this->addError('quantity', Yii::t('app', 'W magazynie nie ma tyle sztuk sprzętu'));
                        $return = false;
                    }
                }
                
        }
        if ($this->type==3)
        {
                if (!$this->warehouse_from)
                {
                        $this->addError('warehouse_from', Yii::t('app', 'Pole obowiązkowe'));
                        $return = false;
                }else{
                    $wq = WarehouseQuantity::find()->where(['gear_id'=>$this->gear_id, 'warehouse_id'=>$this->warehouse_from])->one();
                    if ($wq->quantity<$this->quantity)
                    {
                        $this->addError('quantity', Yii::t('app', 'W magazynie nie ma tyle sztuk sprzętu'));
                        $return = false;
                    }
                }
                if (!$check)
                    if (!$this->warehouse_to)
                    {
                            $this->addError('warehouse_to', Yii::t('app', 'Pole obowiązkowe'));
                            $return = false;
                    }
                
        }
        return $return;
    }
	
}
