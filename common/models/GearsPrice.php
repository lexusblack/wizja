<?php

namespace common\models;

use Yii;
use \common\models\base\GearsPrice as BaseGearsPrice;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "gears_price".
 */
class GearsPrice extends BaseGearsPrice
{
    /**
     * @inheritdoc
     */

    public $priceGroupIds;

    public $gears_price_id;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'priceGroupIds',
            ],
            'relations' => [
                'priceGroups',
            ],
            'modelClasses'=>[
                'common\models\PriceGroup',
            ],
        ];

        return $behaviors;
    }

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['gear_id', 'gear_category_id', 'type', 'gears_price_id'], 'integer'],
            ['vat', 'number'],
            [['name', 'currency'], 'string', 'max' => 45],
            [['priceGroupIds'], 'each', 'rule'=>['integer']],
        ]);
    }

    public function getPricesNames($cat, $group_id)
    {
        $ids = ArrayHelper::map(GearsPriceGroup::find()->where(['price_group_id'=>$group_id])->asArray()->all(), 'gears_price_id', 'gears_price_id');
        $prices = ArrayHelper::map(GearsPrice::find()->where(['or',['type'=>1], ['type'=>2, 'gear_category_id'=>$cat]])->andWhere(['id'=>$ids])->all(), 'id', 'name');
        $prices2 = ["brak"=>""];
        $prices2 += [null=>Yii::t('app', "brak stawki")];
        $prices2 +=$prices;
        return $prices2;
    } 

	
    public function getTypeList()
    {
        return [1=>Yii::t('app', 'Stawka dla całego magazynu'), 2=>Yii::t('app', 'Stawka dla kategorii'), 3=>Yii::t('app', 'Stawka dla jednego modelu sprzętu')];
    }

    public function savePercents($percents)
    {
        GearsPricePercent::deleteAll(['gears_price_id'=>$this->id]);
        if ($percents)
        {
             foreach ($percents as $p)
            {
                $percent = new GearsPricePercent(['gears_price_id'=>$this->id, 'value'=>$p['value'], 'day'=>$p['day']]);
                $percent->save();
            }           
        }

    }

    public function getPercentes()
    {
        $content = "";
        $content2 = "";
        $i=0;
        foreach ($this->gearsPricePercents as $p)
        {
            $content .= Yii::t('app', "Od ").$p->day.Yii::t('app', " dzień")." - ".$p->value."%<br/>";
            $i++;
            if ($i<4)
                $content2 .= Yii::t('app', "Od ").$p->day.Yii::t('app', " dzień")." - ".$p->value."%<br/>";
        }
        if ($i>3)
            return "<span title='".str_replace("<br/>", ", ", $content)."'>".$content2."</span>";
        else    
            return $content;
    }

    public function copyPrices()
    {
        if ((isset($this->gears_price_id))&&($this->gears_price_id))
        {
            GearPrice::deleteAll(['gears_price_id'=>$this->id]);
            if ($this->type==1)
                $prices = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id])->all();
            if ($this->type==2)
            {
                //szukamy sprzętu z danej kategorii
                $sub = $this->gear_category_id;
                $categoryIds = [];
                $ids = [];
                $tmpCat = GearCategory::findOne($sub);

                if ($tmpCat !== null)
                {
                    $ids = $tmpCat->children()->column();
                }

                $categoryIds = array_merge([$sub], $ids);
                $gear_ids = ArrayHelper::map(Gear::find()->where(['category_id'=>$categoryIds])->asArray()->all(), 'id', 'id');
                $prices = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id, 'gear_id'=>$gear_ids])->all();
            }
            if ($this->type==3)
            {
                $prices = GearPrice::find()->where(['gears_price_id'=>$this->gears_price_id, 'gear_id'=>$this->gear_id])->all();
            }
            foreach ($prices as $p)
            {
                $price = new GearPrice(['gears_price_id'=>$this->id, 'gear_id'=>$p->gear_id, 'price'=>$p->price]);
                $price->save();
            }
        }
    }

    public function calculateValue($price, $quantity, $days)
    {
        $total_price = $price*$quantity;
        $percents = $this->gearsPricePercents;
        $day_percent = [];
        if ($days<1)
        {
            $total_price = $total_price*$days;
        }else{
            for ($i=2; $i<=$days+1; $i++)
            {
                $day_percent[$i]=100;
            }
            foreach ($percents as $percent)
            {
                for ($i=$percent->day; $i<=$days+1; $i++)
                {
                    $day_percent[$i]=$percent->value;
                }
            }
            for ($i=2; $i<=$days; $i++)
            {
                $total_price += $price*$quantity*$day_percent[$i]/100;
            }
            if ($days>floor($days))
            {
                $total_price += $price*$quantity*$day_percent[floor($days)+1]/100*($days-floor($days));
            }
        }

        

        return $total_price;
    }

    public function getSinglePrice($total, $quantity, $discount, $days)
    {
        $percents = $this->gearsPricePercents;
        $day_percent = [];
        if (!$percents)
        {
            $duration = $days;
        }else{
            for ($i=2; $i<=$days; $i++)
            {
                $day_percent[$i]=100;
            }
            foreach ($percents as $percent)
            {
                for ($i=$percent->day; $i<=$days; $i++)
                {
                    $day_percent[$i]=$percent->value;
                }
            }
            $duration = 1;
            for ($i=2; $i<=$days; $i++)
            {
                $duration+=$day_percent[$i]/100;
            }
                
        }
        $value = $total/($quantity*$duration*(1-$discount/100));
        return $value;
        
    }

    public function beforeDelete()
    {
        GearPrice::deleteAll(['gears_price_id'=>$this->id]);
        return true;
    }
}
