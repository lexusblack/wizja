<?php
namespace common\models\form;

use backend\modules\permission\models\BasePermission;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\EventGearItem;
use common\models\OfferGear;
use common\models\EventOuterGearModel;
use common\models\EventOuterGear;
use common\models\EventVehicle;
use common\models\Event;
use common\models\Rent;
use common\models\EventCost;
use common\models\GearCategory;
use common\models\Ride;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use Yii;
class Stat extends Model
{
    public $c_ids;
    public $category;
    public function getStats($m, $y, $category)
    {
        $this->category = $category;
        $date = \DateTime::createFromFormat('Yn', $y.$m);
        $date_from = $date->format('Y-m')."-01";
        $date_to = $date->format('Y-m-t'); 
            $ids = [];
            $tmpCat = GearCategory::findOne($category);
            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }
            $this->c_ids = array_merge([$category], $ids);
            echo var_Dump($this->c_ids);

        $stats['chart1'] = $this->getGearStat($date_from, $date_to);
        $stats['chart2'] = $this->getOuterGearStat($date_from, $date_to);
        $stats['chart3'] = $this->getCarStat($date_from, $date_to);
        return $stats;
    }

    public function getChart3($m, $y)
    {
        if ($m>0)
        {
            $date = \DateTime::createFromFormat('Yn', $y.$m);
            $date_from = $date->format('Y-m')."-01";
            $date_to = $date->format('Y-m-t'); 
        }else{
            $date_from = $y."-01-01";
            $date_to = $y."-12-31";            
        }
        return $this->getCarStat($date_from, $date_to);
    }

    public function getChartVehicle($m, $y)
    {
        if ($m>0)
        {
            $d = 1;
            $date = \DateTime::createFromFormat('Y-n-d', $y."-".$m."-".$d);
            $date_from = $date->format('Y-m-d');
            $date_to = $date->format('Y-m-t'); 
        }else{
            $date_from = $y."-01-01";
            $date_to = $y."-12-31";            
        }
        return $this->getVehicleStat($date_from, $date_to);
    }

    public function getChart2($m, $y, $category)
    {
        if ($m>0)
        {
            $date = \DateTime::createFromFormat('Yn', $y.$m);
            $date_from = $date->format('Y-m')."-01";
            $date_to = $date->format('Y-m-t'); 
        }else{
            $date_from = $y."-01-01";
            $date_to = $y."-12-31";            
        }
        $ids = [];
            $tmpCat = GearCategory::findOne($category);
            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }
            $list=[];
            $this->c_ids = array_merge([$category], $ids);
        $stats['chart2'] = $this->getOuterGearStat($date_from, $date_to);
        $stats['chart1'] = $this->getOuterGearStat2($date_from, $date_to);
        $stats['chart3'] = $this->getOuterGearStat3($date_from, $date_to);
        return $stats;        
    }

    public function getChart1($m, $y, $category)
    {
        if ($m>0)
        {
            $date = \DateTime::createFromFormat('Yn', $y.$m);
            $date_from = $date->format('Y-m')."-01";
            $date_to = $date->format('Y-m-t'); 
        }else{
            $date_from = $y."-01-01";
            $date_to = $y."-12-31";            
        } 
        $ids = [];
            $tmpCat = GearCategory::findOne($category);
            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }
        $this->c_ids = array_merge([$category], $ids);
        if ($m!=0)
            $stats['chart1'] = $this->getGearStat($date_from, $date_to);
        $stats['chart2'] = $this->getGearStat2($date_from, $date_to);
        $chart3 = $this->getGearStat3($date_from, $date_to);
        $stats['chart3'] = $chart3['gear_av'];
        $stats['chart4'] = $chart3['gear_total'];
        return $stats;        
    }

        public function getChartCustomer($m, $y, $category)
    {
        if ($m>0)
        {
            $date = \DateTime::createFromFormat('Yn', $y.$m);
            $date_from = $date->format('Y-m')."-01";
            $date_to = $date->format('Y-m-t'); 
        }else{
            $date_from = $y."-01-01";
            $date_to = $y."-12-31";            
        } 
        $events = Event::find()->where(['>', 'event_start', $date_from])->andWhere(['<', 'event_start', $date_to])->all();
        $rents = Rent::find()->where(['>', 'start_time', $date_from])->andWhere(['<', 'start_time', $date_to])->all();
        $stats = [];
        foreach ($events as $e)
        {
            if (!isset($stats[$e->customer_id]))
            {
                $stats[$e->customer_id]['name'] = $e->customer->name;
                $stats[$e->customer_id]['value'] = 0;
                $stats[$e->customer_id]['cost'] = 0;
            }
            $cost = EventCost::find()->where(['event_id'=>$e->id])->andWhere(['section'=>Yii::t('app', 'Suma')])->one();
            if ($cost)
            {
                $stats[$e->customer_id]['cost'] += $cost->value;
            }
            $stats[$e->customer_id]['value'] += $e->getEventValueSum();
        }
        foreach ($rents as $r)
        {
            if (!isset($stats[$r->customer_id]))
            {
                $stats[$r->customer_id]['name'] = $r->customer->name;
                $stats[$r->customer_id]['value'] = 0;
                $stats[$r->customer_id]['cost'] = 0;
            }
            $stats[$r->customer_id]['value'] += $r->getEventValueSum();
        }
        array_multisort (array_column($stats, 'value'), SORT_DESC, $stats);
        return $stats;        
    }

    private function getGearStat3($date_from, $date_to)
    {
        $category_gears = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
        $statuts = ArrayHelper::map(\common\models\OfferStatut::find()->where(['visible_in_finances'=>1])->asArray()->all(), 'id', 'id');
        $offers = \yii\helpers\ArrayHelper::map(\common\models\Offer::find()->where(['and',['>','event_start', $date_from], ['<','event_start', $date_to], ['status'=> $statuts]])->orWhere(['and',['>','event_end', $date_from], ['<','event_end', $date_to], ['status'=> 1]])-> asArray()->all(), 'id', 'id');
        //echo var_dump($category_gears);
        $rows = OfferGear::find()->where(['and', ['in', 'offer_id', $offers], ['in', 'gear_id', $category_gears]])->all();
        $gear_stats = [];
        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->orderBy('name')->asArray()->all(), 'id', 'name');

        foreach ($rows as $model)
        {
            $gear_id = $model->gear_id;
            $offer = $model->offer;
            $sums = $offer->getOfferValues();
            $recalculate_val = 1;
            $recalculate = false;
            if (isset($offer->budget))
            {
                if ($offer->budget<$offer->value)
                {
                        $price = $offer->budget-$sums[Yii::t('app', 'Transport')]-$sums[Yii::t('app', 'Obsługa')]-$sums[Yii::t('app', 'Inne')];
                        $price2 = $offer->value-$sums[Yii::t('app', 'Transport')]-$sums[Yii::t('app', 'Obsługa')]-$sums[Yii::t('app', 'Inne')];
                        if ($price2>0)
                        {
                            $recalculate_val = $price/$price2;
                            $recalculate = true;
                        }else{
                            $recalculate_val = 0;
                            $recalculate = true;
                        }
                        if ($price<0)
                        {
                            $recalculate_val = 0;
                            $recalculate = true;
                        }

                }
            }
            $quantity = $model->quantity===null ? 1 : $model->quantity;
            $value = $model->getValue()*$recalculate_val;


            if (!isset($gear_stats[$gear_id]))
            {
                $gear_stats[$gear_id]['name'] = $gearNames[$gear_id];
                $gear_stats[$gear_id]['id'] = $gear_id;
                $gear_stats[$gear_id]['quantity'] = $value;
            }else{
                $gear_stats[$gear_id]['quantity'] += $value;
            }
        }
        foreach($gear_stats as $gs)
        {
            $g = \common\models\Gear::find()->where(['id'=>$gs['id']])->one();
            if ($g->no_items)
            {
                        $quantity =  $g->quantity;
            }
            else
            {
                        $quantity =   $g->getGearItems()->andWhere(['active'=>1])->count();
            }
            if ($quantity==0)
                $quantity=1;
            $gear_stats[$gs['id']]['total'] = $gear_stats[$gs['id']]['quantity'];
            $gear_stats[$gs['id']]['quantity'] = floor($gear_stats[$gs['id']]['quantity']/$quantity);
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['quantity'] <=> $item1['quantity'];
        });
        $return['gear_av'] = $gear_stats;
        usort($gear_stats, function ($item1, $item2) {
            return $item2['total'] <=> $item1['total'];
        });
        $return['gear_total'] = $gear_stats;
        return $return;
    }


    private function getGearStat2($date_from, $date_to)
    {
        /*$category_gears = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
        $cat_items = \yii\helpers\ArrayHelper::map(\common\models\GearItem::find()->where(['gear_id'=>$category_gears])->asArray()->all(), 'id', 'id');
        $query = new Query;
        $query->select('gear_item.gear_id, event_gear_item.event_id')
            ->from('event_gear_item, gear_item')->where('event_gear_item.gear_item_id = gear_item.id')->andWhere(['in', 'gear_item_id', $cat_items])->andWhere(['or', ['and',['>','start_time', $date_from], ['<','start_time', $date_to]],['and',['>','end_time', $date_from], ['<','end_time', $date_to]]])->groupBy('event_gear_item.event_id, gear_item.gear_id');
            $rows = $query->all();
        $gear_stats = [];
        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        */
        $category_gears = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
        $rows = \common\models\EventGear::find()->where(['gear_id'=>$category_gears])->andWhere(['or', ['and',['>','start_time', $date_from], ['<','start_time', $date_to]],['and',['>','end_time', $date_from], ['<','end_time', $date_to]]])->all();
        foreach ($rows as $row)
        {
            $gear_id = $row->gear_id;
            if (!isset($gear_stats[$gear_id]))
            {
                $gear_stats[$gear_id]['name'] = $row->gear->name;;
                $gear_stats[$gear_id]['quantity'] = $row->quantity;
            }else{
                $gear_stats[$gear_id]['quantity'] += $row->quantity;
            }
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['quantity'] <=> $item1['quantity'];
        });
        return $gear_stats;
    }


    private  function getGearStat($date_from, $date_to)
    {
        if ($this->category!=1)
        {
            $category_gears = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
            $cat_items = \yii\helpers\ArrayHelper::map(\common\models\GearItem::find()->where(['gear_id'=>$category_gears])->asArray()->all(), 'id', 'id');
            $gears = EventGearItem::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to], ['in', 'gear_item_id', $cat_items]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to], ['in', 'gear_item_id', $cat_items]])->asArray()->all(); 
        }else{
            $gears = EventGearItem::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to]])->asArray()->all();            
        }

        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $gearItems = \yii\helpers\ArrayHelper::map(\common\models\GearItem::find()->orderBy('name')->asArray()->all(), 'id', 'gear_id');
        $gear_stats = [];
        $gear_stat2 = [];
        foreach ($gears as $gear)
        {
            if ($gear['start_time'] < $date_from)
            {
                $days = $this->countDays($date_from, $gear['end_time']);
                
            }else{
                if ($gear['end_time'] > $date_to)
                {
                    $days = $this->countDays($date_to, $gear['end_time']);
                }else{
                    $days = $this->countDays($gear['start_time'], $gear['end_time']);
                }                
            }
            $gear_id = $gearItems[$gear['gear_item_id']];
            if (!isset($gear_stats[$gear_id]))
            {
                $gear_stats[$gear_id]['name'] = $gearNames[$gear_id];
                if ($gear['quantity'])
                    $gear_stats[$gear_id]['days'] = $days*$gear['quantity'];
                else
                    $gear_stats[$gear_id]['days'] = $days;
            }else{
                if ($gear['quantity'])
                    $gear_stats[$gear_id]['days'] += $days*$gear['quantity'];
                else
                    $gear_stats[$gear_id]['days'] += $days;
            }
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['days'] <=> $item1['days'];
        });
        return $gear_stats;        
    }
    private  function getOuterGearStat($date_from, $date_to)
    {
        
        if ($this->category!=1)
        {
            $category_gears = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
            $gears = EventOuterGearModel::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to], ['IN', 'outer_gear_model_id', $category_gears]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to], ['IN', 'outer_gear_model_id', $category_gears]])->asArray()->all(); 
        }else{
                    $gears = EventOuterGearModel::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to]])->asArray()->all();           
        }


        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $gear_stats = [];
        foreach ($gears as $gear)
        {
            if ($gear['start_time'] < $date_from)
            {
                $days = $this->countDays($date_from, $gear['end_time']);
                
            }else{
                if ($gear['end_time'] > $date_to)
                {
                    $days = $this->countDays($date_to, $gear['end_time']);
                }else{
                    $days = $this->countDays($gear['start_time'], $gear['end_time']);
                }                
            }
            if (!isset($gear_stats[$gear['outer_gear_model_id']]))
            {
                $gear_stats[$gear['outer_gear_model_id']]['name'] = $gearNames[$gear['outer_gear_model_id']];
                $gear_stats[$gear['outer_gear_model_id']]['days'] = $days*$gear['quantity'];
            }else{
                $gear_stats[$gear['outer_gear_model_id']]['days'] += $days*$gear['quantity'];
            }
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['days'] <=> $item1['days'];
        });
        return $gear_stats;        
    }

    private  function getOuterGearStat2($date_from, $date_to)
    {
        
        if ($this->category!=1)
        {
            $category_gears = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
            $gears = EventOuterGearModel::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to], ['IN', 'outer_gear_model_id', $category_gears]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to], ['IN', 'outer_gear_model_id', $category_gears]])->asArray()->all(); 
        }else{
                    $gears = EventOuterGearModel::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to]])->asArray()->all();           
        }


        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $gear_stats = [];
        foreach ($gears as $gear)
        {
            if (!isset($gear_stats[$gear['outer_gear_model_id']]))
            {
                $gear_stats[$gear['outer_gear_model_id']]['name'] = $gearNames[$gear['outer_gear_model_id']];
                $gear_stats[$gear['outer_gear_model_id']]['quantity'] = 1;
            }else{
                $gear_stats[$gear['outer_gear_model_id']]['quantity'] += 1;
            }
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['quantity'] <=> $item1['quantity'];
        });
        return $gear_stats;        
    }

    private  function getOuterGearStat3($date_from, $date_to)
    {
        
        if ($this->category!=1)
        {
            $category_gear_model = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->where(['category_id'=>$this->c_ids])->asArray()->all(), 'id', 'id');
            $category_gears= \yii\helpers\ArrayHelper::map(\common\models\OuterGear::find()->where(['outer_gear_model_id'=>$category_gear_model])->asArray()->all(), 'id', 'id');
            $gears = EventOuterGear::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to], ['IN', 'outer_gear_id', $category_gears]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to], ['IN', 'outer_gear_id', $category_gears]])->asArray()->all(); 
        }else{
            $gears = EventOuterGear::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to]])->asArray()->all();           
        }


        $gearNames = \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $g = \common\models\OuterGear::find()->asArray()->all();
        $ids =  \yii\helpers\ArrayHelper::map($g, 'id', 'outer_gear_model_id');
        $prices = \yii\helpers\ArrayHelper::map($g, 'id', 'price');
        $gear_stats = [];
        foreach ($gears as $gear)
        {
            $id = $ids[$gear['outer_gear_id']];
            $price = $prices[$gear['outer_gear_id']];
            if (!isset($gear_stats[$id]))
            {
                $gear_stats[$id]['name'] = $gearNames[$id];
                $gear_stats[$id]['quantity'] = $price*$gear['quantity'];
            }else{
                $gear_stats[$id]['quantity'] += $price*$gear['quantity'];
            }
        }
        usort($gear_stats, function ($item1, $item2) {
            return $item2['quantity'] <=> $item1['quantity'];
        });
        return $gear_stats;        
    }

    private function getCarStat($date_from, $date_to)
    {
        $cars = EventVehicle::find()->where(['and',['>','start_time', $date_from], ['<','start_time', $date_to]])->orWhere(['and',['>','end_time', $date_from], ['<','end_time', $date_to]])->asArray()->all();
        $vehicles = \yii\helpers\ArrayHelper::map(\common\models\Vehicle::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $car_stats = [];
        foreach ($cars as $car)
        {
            $event = Event::find()->where(['id'=>$car['event_id']])->one();
            $distance = 0;
            if (isset($event->location)){
                $distance = $event->location->getGoogleDistance();
            }
            
            if (!isset($car_stats[$car['vehicle_id']]))
            {
                $car_stats[$car['vehicle_id']]['distance'] = $distance*2;
                $car_stats[$car['vehicle_id']]['name'] = $vehicles[$car['vehicle_id']];
            }else{
                $car_stats[$car['vehicle_id']]['distance'] += $distance*2;
            }
        }
        return $car_stats;
    }

    private function getVehicleStat($date_from, $date_to)
    {
        $vehicles = \yii\helpers\ArrayHelper::map(\common\models\Vehicle::find()->orderBy('name')->asArray()->all(), 'id', 'name');
        $car_stats = [];
        $cars = Ride::find()->where(['and',['>','start', $date_from], ['<','start', $date_to]])->asArray()->all();
        foreach ($cars as $car)
        {
            if (($car['km_start'])&&($car['km_end']))
                $distance = $car['km_end']-$car['km_start'];
            else
                $distance = 0;
            
            if (!isset($car_stats[$car['vehicle_id']]))
            {
                $car_stats[$car['vehicle_id']]['distance'] = $distance;
                $car_stats[$car['vehicle_id']]['name'] = $vehicles[$car['vehicle_id']];
            }else{
                $car_stats[$car['vehicle_id']]['distance'] += $distance;
            }
        }
        return $car_stats;
    }

    private function countDays($start, $end)
    {
        $start = strtotime($start);
        $end = strtotime($end);
        $datediff = $end - $start;
        return floor($datediff / (60 * 60 * 24)); +1;
    }
}