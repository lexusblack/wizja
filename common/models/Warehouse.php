<?php

namespace common\models;

use Yii;
use \common\models\base\Warehouse as BaseWarehouse;

/**
 * This is the model class for table "warehouse".
 */
class Warehouse extends BaseWarehouse
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name', 'address'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 12],
            [['color'], 'string', 'max' => 45],
            [['type'], 'safe']
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
            $gears = Gear::find()->where(['active'=>1])->all();
            foreach ($gears as $gear)
            {
                $wq = new WarehouseQuantity();
                $wq->gear_id = $gear->id;
                $wq->warehouse_id = $this->id;
                $wq->quantity = 0;
                $wq->location = $gear->location;
                $wq->save();
            }
        }
    }

    public function checkGroups($groups)
    {
        $groupsNew = [];
        foreach ($groups as $id =>$val)
        {
            if ($val){
            $remove = false;
            $gear_items = GearItem::find()->where(['group_id'=>$id])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();
            foreach ($gear_items as $gear_item)
            {
                if ($gear_item->warehouse_id!=$this->id)
                {
                    $remove = true;
                }
            }
            if (!$remove)
            {
                $groupsNew[$id] = $id;
            }
        }

        }
        return $groupsNew;
    }

    public function checkItems($items)
    {
        $itemsNew = [];
        foreach ($items as $gear_item => $value) 
        {
            $remove = false;
            $gear_item = GearItem::findOne($gear_item);
            if ($gear_item->gear->no_items)
            {
                $wq = WarehouseQuantity::findOne(['warehouse_id'=>$this->id, 'gear_id'=>$gear_item->gear_id]);
                if ($wq->quantity>$value)
                {
                    $itemsNew[$gear_item->id]=$value;
                }else{
                    if ($wq->quantity>0)
                        $itemsNew[$gear_item->id]=$wq->quantity;
                }
            }else{
                if ($gear_item->warehouse_id==$this->id)
                {
                    $itemsNew[$gear_item->id]=$value;
                }
            }

        }
        return $itemsNew;
    }

        public function getNumberLabel($gear)
    {
        if ($gear->no_items)
        {
            $q = WarehouseQuantity::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->one();
            if ($q)
            {
                if ($q->quantity>0)
                    return "<br/><span class='label label-primary' style='padding:1px; background-color:".$this->color."'>".$q->quantity."</span> ".$this->short_name;
                else
                    return "";
            }else{
                return "";
            }
        }else{
            $q = GearItem::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->andWhere(['active'=>1])->count();
            if ($q)
            {
                return "<br/><span class='label label-primary' style='padding:1px; background-color:".$this->color."'>".$q."</span> ".$this->short_name;
            }else{
                return "";
            }
        }
    }

    public function getNumber($gear)
    {
        if ($gear->no_items)
        {
            $q = WarehouseQuantity::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->one();
            if ($q)
            {
                if ($q->quantity>0)
                    return $q->quantity;
                else
                    return 0;
            }else{
                return 0;
            }
        }else{
            $q = GearItem::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->andWhere(['active'=>1])->count();
            if ($q)
            {
                return $q;
            }else{
                return 0;
            }
        }
    }

    public function getWQ($gear)
    {
        return WarehouseQuantity::find()->where(['warehouse_id'=>$this->id])->andWhere(['gear_id'=>$gear->id])->one();
    }

    public static function getList($service=false)
    {
        if ($service)
                return \common\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->orderBy(['position'=>SORT_ASC])->all(), 'id', 'name');
        else
                return \common\helpers\ArrayHelper::map(\common\models\Warehouse::find()->where(['type'=>1])->asArray()->orderBy(['position'=>SORT_ASC])->all(), 'id', 'name');
    }

    public function getMovement()
    {
        $session = Yii::$app->session;
        $gears = $session->get('moveGears');
        $total = 0;
        if (isset($gears[$this->id])){
        foreach ($gears[$this->id] as $gear)
        {
            $total += $gear['quantity'];
        }
    }
        return $total;
    }

	
}
