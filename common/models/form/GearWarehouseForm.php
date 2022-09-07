<?php
namespace common\models\form;
use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

class GearWarehouseForm extends \yii\base\Model
{

    /**
     * @var Offer;
     */
    public $warehouses= [];


    public function loadData($gear, $warehouses)
    {
        $warehouses = \common\models\Warehouse::find()->all();
        if ($gear->no_items)
        {
            foreach ($warehouses as $w)
                {
                    $q = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$w->id])->andWhere(['gear_id'=>$gear->id])->one();
                    if ($q)
                        $this->warehouses[$gear->id][$w->id] = $q->quantity;
                    else
                        $this->warehouses[$gear->id][$w->id] = 0;
                }
        }else{
            foreach ($gear->gearItems as $item)
            {
                foreach ($warehouses as $w)
                {
                    if ($item->warehouse_id==$w->id)
                        $this->warehouses[$item->id][$w->id] = true;
                    else
                        $this->warehouses[$item->id][$w->id] = false;
                }
            }
        }
    }
}