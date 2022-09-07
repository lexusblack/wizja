<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\EventGear;
use common\models\EventGearItem;
use common\models\OutcomesForEvent;
use common\models\OutcomesGearOur;
use common\models\RentGearItem;
use common\models\RentGear;
use common\models\OutcomesForRent;
use common\models\OutcomesWarehouse;
use common\models\GearItem;

use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class OutcomeController extends BaseController {

    public $modelClass = '\common\models\Outcome';


    public function actionList()
    {
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');
        $gear_item_out = [];
        if ($type=='event')
        {
            $event = $id;
            $gear_event_items = EventGearItem::find()->where(['event_id' => $event])->all();
            $gear_event = EventGear::find()->where(['event_id'=>$event])->all();
            $outcomes = OutcomesForEvent::find()->where(['event_id' => $event])->all();
            $gear_models = [];
            foreach ($gear_event as $gear)
            {
                $gear_models[$gear->gear_id]['gear'] = $gear->gear;
                $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
                $gear_models[$gear->gear->id]['items'] = [];
            }
            foreach ($outcomes as $outcome) {
                foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
                    $gear_item_out[] = $gearOur->gear_id;
                    $gear_models[$gearOur->gear->gear_id]['quantity'] -= $gearOur->gear_quantity;
                }
            }

            foreach ($gear_event_items as $gear_event_item)
            {
                if (!in_array($gear_event_item->gearItem->id, $gear_item_out))
                {
                    $gear_item = $gear_event_item->gearItem;
                    $gear_models[$gear_item->gear_id]['items'][] = $gear_item;        
                }
            }
            foreach ($gear_models as $gm)
                {
                    if ($gm['quantity']<=0)
                    {
                        unset($gear_models[$gm['gear']->id]);
                    }
                }
            //return $gear_models;
        }else{
            $event = $id;
            $gear_event_items = RentGearItem::find()->where(['rent_id' => $event])->all();
            $gear_event = RentGear::find()->where(['rent_id'=>$event])->all();
            $outcomes = OutcomesForRent::find()->where(['rent_id' => $event])->all();
            $gear_models = [];
            foreach ($gear_event as $gear)
            {
                $gear_models[$gear->gear_id]['gear'] = $gear->gear;
                $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
                $gear_models[$gear->gear->id]['items'] = [];
            }
            foreach ($outcomes as $outcome) {
                foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
                    $gear_item_out[] = $gearOur->gear_id;
                    $gear_models[$gi->gear_id]['quantity'] -= $gearOur->gear_quantity;
                }
            }

            foreach ($gear_event_items as $gear_event_item)
            {
                if (!in_array($gear_event_item->gearItem->id, $gear_item_out))
                {
                    $gear_item = $gear_event_item->gearItem;
                    $gear_models[$gear_item->gear_id]['items'][] = $gear_item;        
                }
            }
            foreach ($gear_models as $gm)
                {
                    if ($gm['quantity']<=0)
                    {
                        unset($gear_models[$gm['gear']->id]);
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
        $model = new OutcomesWarehouse();
        $model->user = Yii::$app->getUser()->id;
        $model->start_datetime = date('Y-m-d H:i:s');
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
        $return['outcome_id'] = $model->id;
        $return['items'] = [];
        $return['groups'] = [];
        $return['no_items'] = [];
        if ($type=='event')
        {
            $outcomes_for = new OutcomesForEvent();
            $outcomes_for->event_id = $id;
            $outcomes_for->outcome_id = $model->id;
            $outcomes_for->save();
            foreach ($items as $gear_item){
                        $gearItem = GearItem::findOne($gear_item);
                        $rg = EventGear::find()->where(['gear_id'=>$gearItem->gear_id, 'event_id'=>$id])->one();
                        if ($rg)
                        {
                            $gear = new OutcomesGearOur();
                            $gear->outcome_id = $model->id;
                            $gear->gear_id = $gear_item;
                            $gear->gear_quantity = 1;
                            $gearItem->outcomed = 1;
                            $gear->save();
                            $gearItem->save(); 

                            $egi = EventGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['event_id'=>$id])->one();
                            if (!$egi)
                            {
                                $egi = new EventGearItem();
                                $egi->event_id = $id;
                                $egi->gear_item_id = $gear_item;
                                $egi->planned = 0;
                                $egi->save();
                            }
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                                    $egi->start_time = date('Y-m-d H').":00:00";   
                                    $egi->save();
                            }
                            $return['items'][$gear_item] = 1;
                        }else{
                            $return['items'][$gear_item] = 0;
                        }                           
                }
            foreach ($groups as $gear_group){
                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();    
                        if ($gear_items)
                        {
                            $rg = EventGear::find()->where(['gear_id'=>$gear_items[0]->gear_id, 'event_id'=>$id])->one();
                            if ($rg)
                            {
                                foreach ($gear_items as $gearItem)
                                {
                                        $gear_item = $gearItem->id;
                                        $gear = new OutcomesGearOur();
                                        $gear->outcome_id = $model->id;
                                        $gear->gear_id = $gear_item;
                                        $gear->gear_quantity = 1;
                                        $gearItem = GearItem::findOne($gear_item);
                                        $gearItem->outcomed = 1;
                                        $gear->save();
                                        $gearItem->save();
                                        $egi = EventGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['event_id'=>$id])->one();
                                        if (!$egi)
                                        {
                                            $egi = new EventGearItem();
                                            $egi->event_id = $id;
                                            $egi->gear_item_id = $gear_item;
                                            $egi->planned = 0;
                                            $egi->save();
                                        }
                                        if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                                        {
                                                $rg->start_time = date('Y-m-d H').":00:00";
                                                $rg->save();
                                                $egi->start_time = date('Y-m-d H').":00:00";   
                                                $egi->save();
                                        }
                                }
 

                                
                                $return['groups'][$gear_group] = 1;
                            }else{
                                $return['groups'][$gear_group] = 0;
                            }       
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
                            $gear = new OutcomesGearOur();
                            $gear->outcome_id = $model->id;
                            $gear->gear_id = $gearItem->id;
                            $gear->gear_quantity = $quantity;
                            $gearItem->outcomed+= $quantity;
                            $gear->save();
                            $gearItem->save();   
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                            }
                            $return['no_items'][$gear_item] = 1;                          
                        }else{
                            $return['no_items'][$gear_item] = 0;
                        }
                }  

        }else{
            $outcomes_for = new OutcomesForRent();
            $outcomes_for->rent_id = $id;
            $outcomes_for->outcome_id = $model->id;
            $outcomes_for->save();
            foreach ($items as $gear_item){
                        $gearItem = GearItem::findOne($gear_item);
                        $rg = RentGear::find()->where(['gear_id'=>$gearItem->gear_id, 'rent_id'=>$id])->one();
                        if ($rg)
                        {
                            $gear = new OutcomesGearOur();
                            $gear->outcome_id = $model->id;
                            $gear->gear_id = $gear_item;
                            $gear->gear_quantity = 1;
                            $gearItem->outcomed = 1;
                            $gear->save();
                            $gearItem->save(); 

                            $egi = RentGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['rent_id'=>$id])->one();
                            if (!$egi)
                            {
                                $egi = new RentGearItem();
                                $egi->rent_id = $id;
                                $egi->gear_item_id = $gear_item;
                                $egi->planned = 0;
                                $egi->save();
                            }
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                                    $egi->start_time = date('Y-m-d H').":00:00";   
                                    $egi->save();
                            }
                            $return['items'][$gear_item] = 1;
                        }else{
                            $return['items'][$gear_item] = 0;
                        }                           
                }
            foreach ($groups as $gear_group){
                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();    
                        if ($gear_items)
                        {
                            $rg = RentGear::find()->where(['gear_id'=>$gear_items[0]->gear_id, 'rent_id'=>$id])->one();
                            if ($rg)
                            {
                                foreach ($gear_items as $gearItem)
                                {
                                        $gear_item = $gearItem->id;
                                        $gear = new OutcomesGearOur();
                                        $gear->outcome_id = $model->id;
                                        $gear->gear_id = $gear_item;
                                        $gear->gear_quantity = 1;
                                        $gearItem = GearItem::findOne($gear_item);
                                        $gearItem->outcomed = 1;
                                        $gear->save();
                                        $gearItem->save();
                                        $egi = RentGearItem::find()->where(['gear_item_id'=>$gear_item])->andWhere(['rent_id'=>$id])->one();
                                        if (!$egi)
                                        {
                                            $egi = new EventGearItem();
                                            $egi->rent_id = $id;
                                            $egi->gear_item_id = $gear_item;
                                            $egi->planned = 0;
                                            $egi->save();
                                        }
                                        if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                                        {
                                                $rg->start_time = date('Y-m-d H').":00:00";
                                                $rg->save();
                                                $egi->start_time = date('Y-m-d H').":00:00";   
                                                $egi->save();
                                        }
                                }
 

                                
                                $return['groups'][$gear_group] = 1;
                            }else{
                                $return['groups'][$gear_group] = 0;
                            }       
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
                            $gear = new OutcomesGearOur();
                            $gear->outcome_id = $model->id;
                            $gear->gear_id = $gearItem->id;
                            $gear->gear_quantity = $quantity;
                            $gearItem->outcomed+= $quantity;
                            $gear->save();
                            $gearItem->save();   
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                            }
                            $return['no_items'][$gear_item] = 1;                          
                        }else{
                            $return['no_items'][$gear_item] = 0;
                        }
                } 
        }
        return $return; 
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