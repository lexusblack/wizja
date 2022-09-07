<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\EventGear;
use common\models\EventGearItem;
use common\models\OutcomesForEvent;
use common\models\OutcomesGearOur;
use common\models\IncomesGearOur;
use common\models\RentGearItem;
use common\models\RentGear;
use common\models\OutcomesForRent;
use common\models\IncomesForRent;
use common\models\IncomesForEvent;
use common\models\OutcomesWarehouse;
use common\models\IncomesWarehouse;
use common\models\GearItem;

use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class IncomeController extends BaseController {

    public $modelClass = '\common\models\IncomeWarehouse';


    public function actionList()
    {
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');
        $gear_our_out = [];
        $gear_no_item_our = [];
        if ($type=='event')
        {
            $event = $id;
            $outcomes = OutcomesForEvent::find()->where(['event_id' => $event])->all();
            $incomes = IncomesForEvent::find()->where(['event_id' => $event])->all();
            foreach ($outcomes as $outcome) {
                foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
                    if (!$gearOur->gear->isAvailableForOutcome())
                    {
                        $gear_our_out[$gearOur->gear->gear_id][] = $gearOur->gear;
                        if ($gearOur->gear->gear->no_items)
                            if (isset($gear_no_item_our[$gearOur->gear->gear_id]))
                                $gear_no_item_our[$gearOur->gear->gear_id] += $gearOur->gear_quantity;
                            else
                                $gear_no_item_our[$gearOur->gear->gear_id] = $gearOur->gear_quantity;           
                        }

                }
            }
              foreach ($incomes as $income) {
                foreach (IncomesGearOur::find()->where(['income_id' => $income->income_id])->all() as $gearOur) {
                    if (!$gearOur->gear->isAvailableForOutcome())
                    {
                        if ($gearOur->gear->gear->no_items)
                        {
                            if (isset($gear_no_item_our[$gearOur->gear->gear_id]))
                                $gear_no_item_our[$gearOur->gear->gear_id] -= $gearOur->quantity;
                           if ($gear_no_item_our[$gearOur->gear->gear_id]<=0)
                           {
                                unset($gear_no_item_our[$gearOur->gear->gear_id]);
                                unset($gear_our_out[$gearOur->gear->gear_id]);
                           }                  
                        }
                    }
                }
            }
            $gear_models = [];
            foreach ($gear_our_out as $key =>$gearItem)
            {
                $gear_models[$key]['gear'] = $gearItem[0]->gear;
                $gear_models[$key]['items'] = $gearItem;
                $gear_models[$key]['quantity'] = count($gearItem);
                
                if ($gearItem[0]->gear->no_items)
                {
                    $gear_models[$key]['quantity']=$gear_no_item_our[$key];
                }
                
            }
            //return $gear_models;
        }else{
            $event = $id;
            $outcomes = OutcomesForRent::find()->where(['rent_id' => $event])->all();
            $incomes = IncomesForRent::find()->where(['rent_id' => $event])->all();
            foreach ($outcomes as $outcome) {
                foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
                    if (!$gearOur->gear->isAvailableForOutcome())
                    {
                        $gear_our_out[$gearOur->gear->gear_id][] = $gearOur->gear;
                        if ($gearOur->gear->gear->no_items)
                            if (isset($gear_no_item_our[$gearOur->gear->gear_id]))
                                $gear_no_item_our[$gearOur->gear->gear_id] += $gearOur->gear_quantity;
                            else
                                $gear_no_item_our[$gearOur->gear->gear_id] = $gearOur->gear_quantity;           
                        }

                }
            }
              foreach ($incomes as $income) {
                foreach (IncomesGearOur::find()->where(['income_id' => $income->income_id])->all() as $gearOur) {
                    if (!$gearOur->gear->isAvailableForOutcome())
                    {
                        if ($gearOur->gear->gear->no_items)
                        {
                            if (isset($gear_no_item_our[$gearOur->gear->gear_id]))
                                $gear_no_item_our[$gearOur->gear->gear_id] -= $gearOur->quantity;
                           if ($gear_no_item_our[$gearOur->gear->gear_id]<=0)
                           {
                                unset($gear_no_item_our[$gearOur->gear->gear_id]);
                                unset($gear_our_out[$gearOur->gear->gear_id]);
                           }                  
                        }
                    }
                }
            }
            $gear_models = [];
            foreach ($gear_our_out as $key =>$gearItem)
            {
                $gear_models[$key]['gear'] = $gearItem[0]->gear;
                $gear_models[$key]['items'] = $gearItem;
                $gear_models[$key]['quantity'] = count($gearItem);
                
                if ($gearItem[0]->gear->no_items)
                {
                    $gear_models[$key]['quantity']=$gear_no_item_our[$key];
                }
                
            }
            //return $gear_models;
        }
    $return = [];
        foreach ($gear_models as $gear)
        {
            $return[] = $gear;
        }
        return $return;
    }

    public function actionCreateNew($id, $type)
    {
        $items = json_decode(Yii::$app->request->post('items'));
        $groups = json_decode(Yii::$app->request->post('groups'));
        $no_items = json_decode(Yii::$app->request->post('no_items'));
        if (!$items)
            $items = [];
        if (!$no_items)
            $no_items = [];
        if (!$groups)
            $groups = [];
        $model = new IncomesWarehouse();
        $model->user = Yii::$app->getUser()->id;
        $model->datetime = date('Y-m-d H:i:s');
        if ($type=='event')
        {
            $model->event_id = $id;
            $model->event_type =1;
        }else{
            $model->rent_id = $id;
             $model->event_type =2;
        }
        $model->save();
        $return = [];
        $return['income_id'] = $model->id;
        $return['items'] = [];
        $return['groups'] = [];
        $return['no_items'] = [];
        if ($type=='event')
        {
            $outcomes_for = new IncomesForEvent();
            $outcomes_for->event_id = $id;
            $outcomes_for->income_id = $model->id;
            $outcomes_for->save();
            foreach ($items as $gear_item){
                        $gearItem = GearItem::findOne($gear_item);
                        $egi = EventGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['event_id'=>$id])->one();
                        if ($egi)
                        {
                            $gear = new IncomesGearOur();
                            $gear->income_id = $model->id;
                            $gear->gear_id = $gear_item;
                            $gear->quantity = 1;
                            $gearItem->outcomed = 0;
                            $gear->save();
                            $gearItem->save(); 
                            $return['items'][$gear_item] = 1;
                        }else{
                            $return['items'][$gear_item] = 0;
                        }                           
                }
            foreach ($groups as $gear_group){
                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();    
                        if ($gear_items)
                        {
                                foreach ($gear_items as $gearItem)
                                {
                                        $egi = EventGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['event_id'=>$id])->one();
                                        if ($egi)
                                        {
                                            $gear_item = $gearItem->id;
                                            $gear = new IncomesGearOur();
                                            $gear->income_id = $model->id;
                                            $gear->gear_id = $gear_item;
                                            $gear->quantity = 1;
                                            $gearItem = GearItem::findOne($gear_item);
                                            $gearItem->outcomed = 0;
                                            $gear->save();
                                            $gearItem->save();
                                        }
                                }
 

                                
                                $return['groups'][$gear_group] = 1;
                                   
                        }else{
                            $return['groups'][$gear_group] = 0;
                        }                   
                          
                } 

                foreach ($no_items as $nitem)
                {
                        $quantity = $nitem->value;
                        $gear_item = $nitem->id;
                        $rg = EventGear::find()->where(['gear_id'=>$gear_item, 'event_id'=>$id])->one();
                        $gearItem = GearItem::find()->where(['gear_id'=>$gear_item])->andWhere(['active'=>1])->one();
                        if ($rg)
                        {
                            $gear = new IncomesGearOur();
                            $gear->income_id = $model->id;
                            $gear->gear_id = $gearItem->id;
                            $gear->quantity = $quantity;
                            $gearItem->outcomed-= $quantity;
                            $gear->save();
                            $gearItem->save();   
                            $return['no_items'][$gear_item] = 1;                          
                        }else{
                            $return['no_items'][$gear_item] = 0;
                        }
                }  
                return $return;
        }else{
            $outcomes_for = new IncomesForRent();
            $outcomes_for->rent_id = $id;
            $outcomes_for->income_id = $model->id;
            $outcomes_for->save();
            foreach ($items as $gear_item){
                        $gearItem = GearItem::findOne($gear_item);
                        $egi = RentGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['rent_id'=>$id])->one();
                        if ($egi)
                        {
                            $gear = new IncomesGearOur();
                            $gear->income_id = $model->id;
                            $gear->gear_id = $gear_item;
                            $gear->gear_quantity = 1;
                            $gearItem->outcomed = 0;
                            $gear->save();
                            $gearItem->save(); 
                            $return['items'][$gear_item] = 1;
                        }else{
                            $return['items'][$gear_item] = 0;
                        }                           
                }
            foreach ($groups as $gear_group){
                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();    
                        if ($gear_items)
                        {
                                foreach ($gear_items as $gearItem)
                                {
                                        $egi = RentGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['rent_id'=>$id])->one();
                                        if ($egi)
                                        {
                                            $gear_item = $gearItem->id;
                                            $gear = new IncomesGearOur();
                                            $gear->income_id = $model->id;
                                            $gear->gear_id = $gear_item;
                                            $gear->gear_quantity = 1;
                                            $gearItem = GearItem::findOne($gear_item);
                                            $gearItem->outcomed = 0;
                                            $gear->save();
                                            $gearItem->save();
                                        }
                                }
 

                                
                                $return['groups'][$gear_group] = 1;
                                   
                        }else{
                            $return['groups'][$gear_group] = 0;
                        }                   
                          
                } 

                foreach ($no_items as $gear_item=>$quantity)
                {
                        $rg = RentGear::find()->where(['gear_id'=>$gear_item, 'rent_id'=>$id])->one();
                        $gearItem = GearItem::find()->where(['gear_id'=>$gear_item])->andWhere(['active'=>1])->one();
                        if ($rg)
                        {
                            $gear = new IncomesGearOur();
                            $gear->income_id = $model->id;
                            $gear->gear_id = $gearItem->id;
                            $gear->gear_quantity = $quantity;
                            $gearItem->outcomed-= $quantity;
                            $gear->save();
                            $gearItem->save();   
                            $return['no_items'][$gear_item] = 1;                          
                        }else{
                            $return['no_items'][$gear_item] = 0;
                        }
                }
        return $return; 
        }
    }

}


/**
 *
 * Dzisiejsze wydarzenia:
 * get: /admin/api/dashboard/events?type=today // return array/error
 *
 * Najbliższe wydarzenia:
 * get: /admin/api/dashboard/events?type=upcoming // return array/error
 *
 * Wydarzenia działu:
 * get: /admin/api/dashboard/events?type=department // return array/error
 *
 * Powiadomienia:
 * get: /admin/api/dashboard/notifications // return array/error
 *
 * Zadania:
 * get: /admin/api/dashboard/tasks // return array/error
 * put: /admin/api/dashboard/tasks/{id}/status // && $_POST['status'] - zmienia status na: status = 0 => nowy, status = 10 => zrobiony // return status 200 / error
 * post: /admin/api/dashboard/tasks/{id}/comment //  && $_POST['comment'] - dodaje komentarz // return status 200 / error
 *
 * Checklista:
 * get: /admin/api/dashboard/checklist return array/error
 * put: /admin/api/dashboard/checklist/{id}/status // zmienia status // return status 200 / error
 *
 */