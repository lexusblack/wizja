<?php

namespace common\models;

use barcode\barcode\BarcodeGenerator;
use \common\models\base\GearGroup as BaseGearGroup;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "gear_group".
 */
class GearGroup extends BaseGearGroup
{
    public function getItemNumbers()
    {
        $data = $this->getGearItems()->select('number')->column();
        return implode(', ', $data);
    }

    public function getIsGroupAssigned($event)
    {
        //w grupie
        $ids = $this->getGearItems()->column();
        //w evencie
        $assigned = $event->getGearItems()->column();
        $value = true;
        foreach ($ids as $id)
        {
            if (in_array($id, $assigned)==false)
            {
                $value=false;
                break;
            }
        }
        return $value;
    }

    public function getItemsInfo()
    {
        $info = "";
        foreach ($this->gearItems as $item)
        {
            if ($item->info)
            {
                $info .= "nr ".$item->number." - ".$item->info."<br/>";
            }
                                    
        }
        return $info;
    }

    public function getItemsCount()
    {
        return $this->getGearItems()->count();
    }

    public function generateBarCode() {
        $options = [
            'elementId' => 'bar-'.$this->id,
            'value' => $this->getBarCodeValue(),
            'type' => 'code128',
            'settings' => [
                'output' => 'bmp',
                'barWidth' => 1,
                'barHeight' => 50,
            ],
        ];
        return '<div id="bar-'.$this->id.'" data-name="' . $this->name . '"></div><div style="margin-top:-5px;text-align:center;font-size:9px;">' . $this->getBarCodeValue() . '</div>' . BarcodeGenerator::widget($options);
    }

    public function generateQrCode($width=null) {
        if ($width)
        {
            return Html::img(Url::to(['qr-code/get-big-img', 'text'=>$this->getBarCodeValue()]), ['width'=>$width]);
        }else
            return Html::img(Url::to(['qr-code/get-img', 'text'=>$this->getBarCodeValue()]));
    }

    public function generateQrCodeAsLink() {
        return Html::a($this->generateQrCode(), Url::toRoute(['qr-code/get-big-img', 'text' => $this->getBarCodeValue()]), ['download' => $this->name.'.png']);
    }

    public function getBarCodeValue() {
        // 13 digits
        $c = Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        if ($c->own_ean)
        {
            return $this->code;
        }else{
            return BarCode::ITEMS_GROUP . BarCode::OUR_WAREHOUSE . $this->getNineDigits();
        }
        
    }

    private function getNineDigits() {
        $id_length = strlen($this->id);
        return str_repeat('0', 9-$id_length) . $this->id;
    }
    public function numberOfAvailable() {
        if (!$this->active) {
            return 0;
        }
        $available = true;
        $gear_items = GearItem::find()->where(['group_id'=>$this->id])->andWhere(['active'=>1])->andWhere(['status'=>1])->all();
        foreach ($gear_items as $gear_item) {
            if ($gear_item->outcomed){
                    return 0;
            
            }
        }
        if ($available) {
            return 1;
        }
        return 0;
    }

    public function getTotalWeight()
    {
        $sum = $this->weight;
        foreach ($this->gearItems as $item)
        {
            $sum+=$item->gear->weight;
        }
        return $sum;
    }

    public function getCalculatedVolume()
    {
        if ($this->volume)
            return $this->volume;
        else{
            $volume = $this->width * $this->height * $this->depth/1000000;
            return $volume;
        }
        

        
    }
}
