<?php
namespace common\models\form;

use backend\modules\permission\models\BasePermission;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\Event;
use common\models\UserPayment;
use common\models\EventExpense;
use common\models\User;
use common\models\SettlementUser;
use common\models\Rent;
use common\models\MonthCost;
use common\models\Investition;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use Yii;
class FinanceStat extends Model
{
    public $y;
    public $m;

    public function getEventsStats()
    {
        $event_value = [Yii::t('app', 'Suma')=>0];
        $event_cost = [Yii::t('app', 'Suma')=>0];
        $event_predicted_cost = [Yii::t('app', 'Suma')=>0];
        $event_zaliczka = [Yii::t('app', 'Suma')=>0];
        $event_paid = [Yii::t('app', 'Suma')=>0];
        $salaries = [Yii::t('app', 'Suma')=>0];
        $events = Event::find()->where(['MONTH(event_start)'=>$this->m, 'YEAR(event_start)'=>$this->y])->all();
        $salaries_paid = UserPayment::find()->where(['month'=>$this->m, 'year'=>$this->y])->all();
        foreach ($salaries_paid as $s)
        {
            $salaries[Yii::t('app', 'Suma')]+=$s->amount;
        }
        foreach ($events as $event)
        {
            $value = $event->getEventValueAll();
            foreach ($value as $key=>$val)
            {
                if (!isset($event_value[$key]))
                    $event_value[$key] = 0;
                $event_value[$key] = $event_value[$key]+$val;
            }
            $cost = $event->getEventCosts();
            foreach ($cost as $key=>$val)
            {
                if (!isset($event_cost[$key]))
                    $event_cost[$key] = 0;
                $event_cost[$key] = $event_cost[$key]+$val;
            }
            $expenses = $event->getEventExpenses()
            ->where([
                'type'=>EventExpense::TYPE_SINGLE,
                'section'=>Yii::t('app', 'Obsługa')
            ])
            ->all();
            $event_cost[Yii::t('app', 'Suma')] -= $event_cost[Yii::t('app', 'Obsługa')];
            $event_cost[Yii::t('app', 'Obsługa')] = 0;
            foreach ($expenses as $e)
            {
                $event_cost[Yii::t('app', 'Obsługa')]+=$e->amount;
                $event_cost[Yii::t('app', 'Suma')]+=$e->amount;
            }
            $predicted = $event->getEventPredictedCost();
            foreach ($predicted as $key=>$val)
            {
                if (!isset($event_predicted_cost[$key]))
                    $event_predicted_cost[$key] = 0;
                $event_predicted_cost[$key] = $event_predicted_cost[$key]+$val;
            }
            $event_paid[Yii::t('app', 'Suma')]+=$event->getEventPaid();
            $event_zaliczka[Yii::t('app', 'Suma')]+=$event->getEventPMcost();
        }
        return ['value'=>$event_value, 'cost'=>$event_cost, 'predicted'=>$event_predicted_cost, 'paid'=>$event_paid, 'zaliczka'=>$event_zaliczka, 'salaries'=>$salaries];
    }

    public function getRentsStats()
    {
        $event_value = [Yii::t('app', 'Suma')=>0];
        $events = Rent::find()->where(['MONTH(start_time)'=>$this->m, 'YEAR(start_time)'=>$this->y])->all();
        foreach ($events as $event)
        {
            $value = $event->getEventValueAll();
            foreach ($value as $key=>$val)
            {
                if (!isset($event_value[$key]))
                    $event_value[$key] = 0;
                $event_value[$key] = $event_value[$key]+$val;
            }
        }
        return $event_value;
    }

    public function getProjectsStats()
    {
        
    }

    public function getEmployeeStats()
    {
        $costs = SettlementUser::find()->where(['month'=>$this->m, 'year'=>$this->y])->all();
        $sum = 0;
        $sum_netto = 0;
        $sum_brutto = 0;
        $userSum = [];
        foreach ($costs as $model)
        {
            if (!isset($userSum[$model->user_id])){
                $userSum[$model->user_id]['user'] = $model->user;
                $userSum[$model->user_id]['sum'] = 0;
                $userSum[$model->user_id]['sum_brutto'] = 0;
                $userSum[$model->user_id]['sum_vat'] = 0;
            }    
            $userSum[$model->user_id]['sum'] += $model->sum;        
        }
        
        $pms = User::find()->where(['role'=>30])->andWhere(['active'=>1])->all();
        foreach ($pms as $pm)
        {
            if (!isset($userSum[$pm->id])){
                $userSum[$pm->id]['user'] = $pm;
                $userSum[$pm->id]['sum'] = 0;
                $userSum[$pm->id]['sum_brutto'] = 0;
                $userSum[$pm->id]['sum_vat'] = 0;
            }
            $provision = $pm->getEventProvisions($this->y, $this->m);
            //$userSum[$pm->id]['sum']+=$provision;
            if ($pm->rate_type==720)
            {
                $userSum[$pm->id]['sum']+=$pm->rate_amount;
            }           
        }
            foreach ($userSum as $k=>$v) {
            $user = $v['user'];
            $brutto = ($v['sum']+5*$user->nfz_rate/36)/(1-$user->tax_rate/100)+$user->zus_rate;
            $brutto2 = $v['sum']+$user->nfz_rate+$user->zus_rate;
                        if ($brutto>$brutto2)
                        {
                            $podatek = $brutto-$brutto2;
                        }else{
                            $podatek = 0;
                            $brutto = $brutto2;
                        }
            $userSum[$k]['sum_brutto'] = $brutto;
            $userSum[$k]['sum_vat'] = $brutto*(1+$user->vat_rate/100);
        }
        foreach ($userSum as $us)
        {
            $sum_netto += $us['sum_brutto'];
            $sum_brutto += $us['sum_vat'];
            $sum += $us['sum'];
        }

        $userNormal = User::find()->where(['active'=>1])->andWhere(['rate_type'=>720])->all();
        foreach ($userNormal as $model)
        {
            if ($model->role!=30)
            {
             $total = $model->rate_amount;

                $brutto = ($total+5*$model->nfz_rate/36)/(1-$model->tax_rate/100)+$model->zus_rate;
                $brutto2 = $total+$model->nfz_rate+$model->zus_rate;
                            if ($brutto>$brutto2)
                            {
                                $podatek = $brutto-$brutto2;
                            }else{
                                $podatek = 0;
                                $brutto = $brutto2;
                            }
                $vat= $brutto*(1+$model->vat_rate/100);
                $sum_netto += $brutto;
                $sum_brutto += $vat; 
                $sum +=   $model->rate_amount;           
            }

        }

        return ['netto'=>$sum_netto, 'brutto'=>$sum_brutto, 'sum'=>$sum];
    }

    public function getMonthCosts()
    {
        $costs = MonthCost::find()->all();
        $all = [Yii::t('app', 'Suma')=>0];
        foreach ($costs as $cost)
        {
            if (!$cost->group_id)
            {
                $all[Yii::t('app', 'Suma')] +=$cost->amount;
            }
            if ($cost->type==2){
                if (!isset($all[$cost->section]))
                    $all[$cost->section] = 0;
                $all[$cost->section] +=$cost->amount;
            }
        }
        return $all;
    }

    public function getInvestitions()
    {
        $costs = Investition::find()->where(['MONTH(datetime)'=>$this->m])->andWhere(['YEAR(datetime)'=>$this->y])->all();
        $all = [Yii::t('app', 'Suma')=>0];
        foreach ($costs as $cost)
        {
            if (!$cost->group_id)
            {
                $all[Yii::t('app', 'Suma')] +=$cost->total_price;
            }
            if ($cost->type==2){
                if (!isset($all[$cost->section]))
                    $all[$cost->section] = 0;
                $all[$cost->section] +=$cost->total_price;
            }
        }
        return $all;
    }

    public function getVehicleStats()
    {

    }

}