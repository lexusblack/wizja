<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\GearItemsNoItemsRfid;
use Yii;
use sadovojav\image\Thumbnail;
use common\models\Gear;
use common\models\Packlist;
use common\models\Event;
use common\models\GearPrice;
use common\models\GearItem;
use common\models\GearSearch;
use common\models\EventSearch;
use common\models\EventGear;
use common\models\GearCategory;
use common\models\form\WarehouseSearch;
use common\models\form\PriceForm;
use backend\components\Controller;
use backend\models\GearInfoForm;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use backend\models\GearForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
use yii\web\Response;


/**
 * GearController implements the CRUD actions for Gear model.
 */
class GearController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,

                    'actions' => ['index', 'get-gear-as-json', 'deleted', 'calendar', 'favorite', 'calendar-array', 'wizja', 'save-wizja', 'wizja-all', 'wizja-export', 'show-events', 'repair','repair2', 'repair-outcome', 'repair-service', 'repair-prices', 'export'],
                    'roles' => ['gearModel'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload', 'count', 'import', 'import2'],
                    'roles' => ['gearCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['gearView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete', 'delete-info'],
                    'roles' => ['gearDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'update-info', 'prices', 'edit-items', 'movement', 'add-to-move', 'delete-from-movement'],
                    'roles' => ['gearEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['edit-items', 'movement', 'add-to-move', 'delete-from-movement'],
                    'roles' => ['gearOurWarehouseMoveGear'],
                ],

                [
                    'allow' => true,
                    'actions' => ['error']
                ]
            ],
        ];

        return $behaviors;
    }

    public function actionRepairPrices()
    {
       /* $gears = Gear::find()->all();
        foreach ($gears as $gear)
        {
            $prices = \common\models\GearPrice::find()->where(['gear_id'=>$gear->id, 'gears_price_id'=>4])->orderBy(['id'=>SORT_DESC])->all();
            if (count($prices)>1)
            {
                    $prices[0]->delete();
            }
        }
        exit;
    */
    }

    public function actionRepairService()
    {
          $statuts = ArrayHelper::map(\common\models\GearServiceStatut::find()->where(['type'=>[1,2,3]])->asArray()->all(), 'id', 'id');
            $services = \common\models\GearService::find()->where(['status'=>$statuts])->andWhere(['warehouse_from'=>null])->all();
            $serwis = \common\models\Warehouse::find()->where(['type'=>2])->one();
            $warehouse  = \common\models\Warehouse::find()->where(['type'=>1])->one();
            foreach ($services as $service)
            {
                if ($service->gearItem->gear->no_items)
                    $service->warehouse_from = $warehouse->id;
                else{
                    if ($service->gearItem->warehouse_id)
                        $service->warehouse_from = $service->gearItem->warehouse_id;
                    else
                        $service->warehouse_from = $warehouse->id;
                }
                $service->save();

            }
            //napraiowne
            exit;
            
    }

    public function actionRepairOutcome()
    {
        /*
        $outcomes = \common\models\OutcomesForEvent::find()->all();
        foreach ($outcomes as $o)
        {
            $p = \common\models\Packlist::find()->where(['event_id'=>$o->event_id])->one();
            $o->packlist_id = $p->id;
            $o->save();
        }
        $outcomes = \common\models\IncomesForEvent::find()->all();
        foreach ($outcomes as $o)
        {
            $p = \common\models\Packlist::find()->where(['event_id'=>$o->event_id])->one();
            $o->packlist_id = $p->id;
            $o->save();
        }
        $items = GearItem::find()->where(['>', 'outcomed', 0])->andWhere(['active'=>1])->all();

        foreach ($items as $item)
        {
            if ($item->gear->no_items)
            {
                $o = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$item->gear_id])->one();
                if (!$o)
                {
                $outcomes_ids = ArrayHelper::map(\common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->asArray()->all(), 'outcome_id', 'outcome_id');
                $event_ids = ArrayHelper::map(\common\models\OutcomesForEvent::find()->where(['outcome_id'=>$outcomes_ids])->asArray()->all(), 'event_id', 'event_id');
                $rent_ids = ArrayHelper::map(\common\models\OutcomesForRent::find()->where(['outcome_id'=>$outcomes_ids])->asArray()->all(), 'rent_id', 'rent_id');
                $events2 = Event::find()->where(['id'=>$event_ids])->orderBy(['event_start' =>SORT_DESC])->all();
                $rents = \common\models\Rent::find()->where(['id'=>$rent_ids])->orderBy(['start_time' =>SORT_DESC])->all();
                $total = 0;
                foreach ($events2 as $e)
                {
                    $e_total = 0;
                    if ($total<$item->outcomed)
                    {
                        $o_ids = ArrayHelper::map(\common\models\OutcomesForEvent::find()->where(['event_id'=>$e['id']])->asArray()->all(), 'outcome_id', 'outcome_id');
                        $outcomes = \common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['outcome_id'=>$o_ids])->asArray()->all();
                        foreach ($outcomes as $o)
                        {
                            $e_total+=$o['gear_quantity'];
                        } 
                        $i_ids = ArrayHelper::map(\common\models\IncomesForEvent::find()->where(['event_id'=>$e['id']])->asArray()->all(), 'income_id', 'income_id');
                        $incomes = \common\models\IncomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['income_id'=>$i_ids])->asArray()->all();
                        foreach ($incomes as $o)
                        {
                            $e_total-=$o['quantity'];
                        }
                        if ($e_total>0)
                        {
                            $eo = \common\models\EventGearOutcomed::find()->where(['event_id'=>$e->id, 'gear_id'=>$item->gear_id])->one();
                            if (!$eo)
                                $eo = new \common\models\EventGearOutcomed();
                            $eo->event_id = $e->id;
                            $eo->gear_id = $item->gear_id;
                            $eo->quantity = $e_total;
                            $p = \common\models\Packlist::find()->where(['event_id'=>$e->id])->one();
                            $eo->packlist_id = $p->id;
                            if ($eo->save()) {
                                $wqs = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$item->gear_id])->all();
                                $total +=$e_total;
                                foreach ($wqs as $wq)
                                {
                                    if ($e_total>0)
                                    {
                                        if ($wq->quantity>=$e_total)
                                        {
                                            $wq->quantity -=$e_total;
                                            $wq->save();
                                        }else{
                                            $e_total -=$wq->quantity;
                                            $wq->quantity =0;
                                            $wq->save();
                                        }
                                    }
                                }

                            }

                        }
                    }

                }
            }
                foreach ($rents as $e)
                {
                    $e_total = 0;
                    if ($total<$item->outcomed)
                    {
                        $o_ids = ArrayHelper::map(\common\models\OutcomesForRent::find()->where(['rent_id'=>$e->id])->asArray()->all(), 'outcome_id', 'outcome_id');
                        $outcomes = \common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['outcome_id'=>$o_ids])->asArray()->all();
                        foreach ($outcomes as $o)
                        {
                            $e_total+=$o['gear_quantity'];
                        } 
                        $i_ids = ArrayHelper::map(\common\models\IncomesForRent::find()->where(['rent_id'=>$e->id])->asArray()->all(), 'income_id', 'income_id');
                        $incomes = \common\models\IncomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['income_id'=>$i_ids])->asArray()->all();
                        foreach ($incomes as $o)
                        {
                             $e_total-=$o['quantity'];
                        }
                        if ($e_total>0)
                        {
                            $eo = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$e->id, 'gear_id'=>$item->gear_id])->one();
                            if (!$eo)
                                $eo = new \common\models\RentGearOutcomed();
                            $eo->rent_id = $e->id;
                            $eo->gear_id = $item->gear_id;
                            $eo->quantity = $e_total;
                            $eo->save();
                            $wqs = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$item->gear_id])->all();
                            $total +=$e_total;
                            foreach ($wqs as $wq)
                            {
                                if ($e_total>0)
                                {
                                    if ($wq->quantity>=$e_total)
                                    {
                                        $wq->quantity -=$e_total;
                                        $wq->save();
                                    }else{
                                        $e_total -=$wq->quantity;
                                        $wq->quantity =0;
                                        $wq->save();
                                    }
                                }
                            }

                        }
                                            }

                                        }
            }else{
                $item->warehouse_id = null;
                $event = $item->getLastEvent();
                $type = $event['type'];
                if ($type=="event")
                {
                    $item->event_id = $event['event']->id;
                    $eo = \common\models\EventGearOutcomed::find()->where(['event_id'=>$item->event_id, 'gear_id'=>$item->gear_id])->one();
                    if (!$eo){
                        $eo = new \common\models\EventGearOutcomed();
                        $eo->quantity = 0;
                    }
                            $eo->event_id = $item->event_id;
                            $eo->gear_id = $item->gear_id;
                            $eo->quantity+=1;
                            $p = \common\models\Packlist::find()->where(['event_id'=>$item->event_id])->one();
                            $eo->packlist_id = $p->id;
                            $eo->save();
                            $item->packlist_id = $p->id;
                    
                }else{
                    $item->rent_id = $event['event']->id;
                    $eo = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$item->rent_id, 'gear_id'=>$item->gear_id])->one();
                    if (!$eo){
                        $eo = new \common\models\RentGearOutcomed();
                        $eo->quantity = 0;
                    }
                            $eo->rent_id = $item->rent_id;
                            $eo->gear_id = $item->gear_id;
                            $eo->quantity+=1;
                            $eo->save();
                }
                $item->save();
            }
        }*/
    }

    public function actionRepair2()
    {
        $gears = Gear::find()->where(['active'=>1])->all();
        foreach ($gears as $gear)
        {
            if (!$gear->no_items)
            {
               $gear->recalculateWarehouses();
            }
        }
            
        exit;

    }

    public function actionRepair()
    {
        //tworzymy magazyn
       /* $warehouse = \common\models\Warehouse::find(['type'=>1])->one();
        if (!$warehouse)
        {
            //nie ma utworzonego magazynu
            $warehouse = new \common\models\Warehouse();
            $warehouse->name = "Magazyn główny";
            $warehouse->type = 1;
            $warehouse->short_name = "Magazyn";
            $warehouse->color = "#333333";
            $warehouse->address = "";
            $warehouse->save();
        }
        $serwis = \common\models\Warehouse::find()->where(['type'=>2])->one();
        if (!$serwis){
            $serwis = new \common\models\Warehouse();
            $serwis->name = "Magazyn serwisowy";
            $serwis->type = 2;
            $serwis->short_name = "Serwis";
            $serwis->color = "#990000";
            $serwis->address = "";
            $serwis->save();
        }

        $gears = Gear::find()->where(['active'=>1])->all();
        foreach ($gears as $gear)
        {
            if (!$gear->no_items)
            {
                GearItem::updateAll(['warehouse_id'=>$warehouse->id], ['warehouse_id'=>null, 'event_id'=>null, 'rent_id'=>null, 'active'=>1]);
                $gear->recalculateWarehouses();
            }else{
                $wq = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$gear->id])->all();
                if (count($wq)>0)
                {
                    $total = 0;
                    foreach ($wq as $q)
                    {
                        if ($q->warehouse_id!=$warehouse->id)
                            $total +=$q->quantity;
                    }
                    $egos = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$gear->id])->all();
                    foreach ($egos as $ego)
                    {
                        $total +=$ego->quantity;
                    }
                    $egos = \common\models\RentGearOutcomed::find()->where(['gear_id'=>$gear->id])->all();
                    foreach ($egos as $ego)
                    {
                        $total +=$ego->quantity;
                    }
                    if ($total<$gear->quantity)
                    {
                        $wq = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$gear->id, 'warehouse_id'=>$warehouse->id])->one();
                        if (!$wq)
                        {
                            $wq = new \common\models\WarehouseQuantity();
                            $wq->gear_id = $gear->id;
                            $wq->warehouse_id = $warehouse->id;
                            $wq->location = $gear->location;
                            
                        }
                            $wq->quantity = $gear->quantity-$total;
                            $wq->save();
                        
                        
                    }

                }else{
                    $wq = new \common\models\WarehouseQuantity();
                    $wq->gear_id = $gear->id;
                    $wq->warehouse_id = $warehouse->id;
                    $wq->quantity = $gear->quantity;
                    $wq->location = $gear->location;
                    $wq->save();
                }
            }
        }
        //sprawdzamy co jest wydane i dokąd
        exit;
        */

    }

    public function actionAddToMove($id, $w, $type)
    {
        $session = Yii::$app->session;
        $gears = $session->get('moveGears');
        if ($type=='gear')
        {
                $gear = Gear::findOne($id);
                $model = new \common\models\GearMovement();
                if ($model->load(Yii::$app->request->post()))
                {
                    $model->gear_id = $id;
                    $model->warehouse_from = $w;
                    $model->type = 3;
                    if ($model->validateQuantites(true))
                    {

                        $gears[$w][$id] = [];
                        $gears[$w][$id]['gear'] = $gear;
                        $gears[$w][$id]['quantity'] = $model->quantity;
                        $session->set('moveGears', $gears);

                        $warehouse = \common\models\Warehouse::findOne($w);
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ['success'=>1, 'total'=>$warehouse->getMovement()];
                        
                    }else{
                        return $this->renderAjax('add-to-move', [
                            'model' => $model,
                            'gear'=>$gear,
                            'w'=>$w,
                            'type'=>$type
                                ]);
                    }
                }else{
                    
                    if ($gears[$w][$id])
                        $model->quantity = $gears[$w][$id]['quantity'];
                    else
                        $model->quantity = 0;
                    return $this->renderAjax('add-to-move', [
                    'model' => $model,
                    'gear'=>$gear,
                    'w'=>$w,
                    'type'=>$type
                        ]);
                }

        }
        if ($type=='item')
        {
            $gearItem = GearItem::findOne($id);
            $gear = $gearItem->gear;
            if ($gears[$w][$gear->id])
            {
                if ($gears[$w][$gear->id]['items'][$id])
                {

                }else{
                    $gears[$w][$gear->id]['items'][$id] = $gearItem;
                    $gears[$w][$gear->id]['quantity'] = $gears[$w][$gear->id]['quantity']+1;
                }

            }else{
                 $gears[$w][$gear->id]['gear'] = $gear;
                $gears[$w][$gear->id]['items'][$id] = $gearItem;
                $gears[$w][$gear->id]['quantity'] = 1;
            }
            $session->set('moveGears', $gears);
            $warehouse = \common\models\Warehouse::findOne($w);
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ['success'=>1, 'total'=>$warehouse->getMovement()];
        }
    }

    public function actionDeleteFromMovement($gear_id, $warehouse_id)
    {
        $session = Yii::$app->session;

        $gears = $session->get('moveGears');
        $gears[$warehouse_id][$gear_id] = null;
        $session->set('moveGears', $gears);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $warehouse = \common\models\Warehouse::findOne($warehouse_id);
        return ['success'=>1, 'total'=>$warehouse->getMovement()];
    }

    public function actionMovement($w)
    {
        $model = new \common\models\GearMovement();
        $model->type = 3;
        //$model->gear_id = $id;
        $model->user_id = Yii::$app->user->id;
        $model->datetime = date("Y-m-d H:i:s");
        $model->warehouse_from = $w;
        $session = Yii::$app->session;
        $gears = $session->get('moveGears');
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->warehouse_to)
            {
                $added = [];
                $notAdded = [];
                foreach ($gears[$w] as $gear)
                {
                    $m = new \common\models\GearMovement();
                    $m->type = 3;
                    $m->gear_id = $gear['gear']->id;
                    $m->warehouse_from = $model->warehouse_from;
                    $m->warehouse_to = $model->warehouse_to;
                    $m->user_id = Yii::$app->user->id;
                    $m->datetime = $model->datetime;
                    $m->info = $model->info;
                    $m->quantity = $gear['quantity'];
                    if ($m->validateQuantites())
                    {
                        
                        if ($gear['gear']->no_items)
                        {
                            $m->save();
                            $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_from]);
                            $wq->quantity-=$m->quantity;
                            $wq->save();
                            $wq2 = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_to]);
                            $wq2->quantity+=$m->quantity;
                            $wq2->save();
                        }else{
                            $total = 0;
                            foreach ($gear['items'] as $item)
                            {
                                if ($item->warehouse_id==$w)
                                {
                                    $total++;
                                }
                            }
                            $m->quantity = $total;
                            $m->save();
                            foreach ($gear['items'] as $item)
                            {
                                if ($item->warehouse_id==$w)
                                {
                                    $item->warehouse_id = $m->warehouse_to;
                                    $item->save();
                                    $item->gear->recalculateWarehouses();
                                    $move = new \common\models\GearMovementItem();
                                    $move->gear_item_id = $item->id;
                                    $move->gear_movement_id = $m->id;
                                    $move->save();
                                }

                            }
                        }
                        $added[] = $gear;
                        unset($gears[$w][$gear['gear']->id]);
                    }else{
                        $notAdded[] = $gear;

                    }
                    $session->set('moveGears', $gears);
                }
                //return 1;
                return $this->renderAjax('movement_summary', [
                    'model' => $model,
                    'added'=>$added,
                    'w'=>$w,
                    'notAdded'=>$notAdded
                ]);
            }else{
                return $this->renderAjax('movement', [
                    'model' => $model,
                    'gears'=>$gears,
                    'w'=>$w
                ]);
            }
        }else{
            if ($gears){
                if ($gears[$w])
                    $gears = $gears[$w];
                else
                {
                    $gears[$w] = [];
                    $session->set('moveGears', $gears);
                    $gears = [];
                }
            }
            else
            {
                $gears = [];
                $session->set('moveGears', [$w=>[]]);
            }

        return $this->renderAjax('movement', [
                    'model' => $model,
                    'gears'=>$gears,
                    'w'=>$w
                ]);
        }


    }

    public function actionEditItems($id, $type, $items=null)
    {
        $gear  = Gear::findOne($id);
        if ($items)
            $items =explode(",", $items);
        if (!$gear->no_items)
        {
            $items = GearItem::find()->where(['id'=>$items])->all();
        }

        $model = new \common\models\GearMovement();
        $model->type = $type;
        $model->gear_id = $id;
        $model->user_id = Yii::$app->user->id;
        $model->datetime = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post()))
        {
            if ($gear->no_items)
            {
           if ($model->validateQuantites())
           {
                $model->save();
                if ($type==1)
                {
                    //dodajemy
                    $gear->quantity+=$model->quantity;
                    $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$model->gear_id, 'warehouse_id'=>$model->warehouse_to]);
                    $wq->quantity+=$model->quantity;
                    $wq->save();
                    $gear->save();
                }
                if ($type==2)
                {
                    $gear->quantity-=$model->quantity;
                    $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$model->gear_id, 'warehouse_id'=>$model->warehouse_from]);
                    $wq->quantity-=$model->quantity;
                    $wq->save();
                    $gear->save();
                }
                if ($type==3)
                {
                    $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$model->gear_id, 'warehouse_id'=>$model->warehouse_from]);
                    $wq->quantity-=$model->quantity;
                    $wq->save();
                    $wq2 = \common\models\WarehouseQuantity::findOne(['gear_id'=>$model->gear_id, 'warehouse_id'=>$model->warehouse_to]);
                    $wq2->quantity+=$model->quantity;
                    $wq2->save();
                    
                }
                return $this->redirect(['view', 'id'=>$model->gear_id]);

           }else{
                return $this->render('edit-items', [
                    'model' => $model,
                    'gear'=>$gear,
                    'items'=>$items
                ]);
           }
            }else{
                if (!$model->warehouse_to)
                {
                    $model->addError('warehouse_to', Yii::t('app', 'Pole obowiązkowe'));
                    return $this->render('edit-items', [
                    'model' => $model,
                    'gear'=>$gear,
                    'items'=>$items
                ]);
                }
                $model->quantity = count($items);
                $model->save();
                foreach ($items as $item)
                {
                    $item->warehouse_id = $model->warehouse_to;
                    $item->save();
                    $move = new \common\models\GearMovementItem();
                    $move->gear_item_id = $item->id;
                    $move->gear_movement_id = $model->id;
                    $move->save();
                }
                return $this->redirect(['view', 'id'=>$model->gear_id]);
            }
        }
        else
        {
            return $this->render('edit-items', [
                'model' => $model,
                'gear'=>$gear,
                'items'=>$items
            ]);
        }
    }

    public function actionShowEvents($id)
    {
        $gear  = Gear::findOne($id);

            $events = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$id])->andWhere(['>', 'quantity', 0])->all();
            $rents = \common\models\RentGearOutcomed::find()->where(['gear_id'=>$id])->andWhere(['>', 'quantity', 0])->all();

        return $this->renderAjax('show-events', ['gear'=>$gear, 'events'=>$events, 'rents'=>$rents]);
    }

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/gear'

            ]
        ];
    }

    public function actionSaveWizja($id)
    {
        $savedData = json_decode(Yii::$app->request->post("data"));

        $gear = Gear::findOne($id);
        $start = date("Y-m-d");

        $end = new \DateTime($start);
        $end->add(new \DateInterval('P6M'));
        $start = new \DateTime($start);
        $start->sub(new \DateInterval('P6D'));
        $end = $end->format('Y-m-d');
        $start = $start->format('Y-m-d');
        $events = $gear->getEvents($start, $end)['events'];
        $eventArray = [];
        $eventList = [];
        foreach ($events as $e)
        {
            $eventList[$e->packlist->event_id] = $e->packlist->event->name;
        }
        $sstart = new \DateTime($start);
        while ($sstart->format('Y-m-d') <=$end)
        {
            $data = $sstart->format('Y-m-d');
            $sstart->add(new \DateInterval('P1D'));
            $data2 = $sstart->format('Y-m-d');
            $eventArray[$data] = [];
            foreach ($events as $e)
            {
                if (!isset($eventArray[$e->packlist->event_id][$data]))
                {
                    $eventArray[$e->packlist->event_id][$data]['quantity'] = 0;
                    $eventArray[$e->packlist->event_id][$data]['conflict'] = 0;

                }
                if (($e->start_time<$data2)&&($e->end_time>$data))
                {
                    $eventArray[$e->packlist->event_id][$data]['quantity'] += $e->quantity;
                    $conflict = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$e->id, 'resolved' =>0])->one();
                    if ($conflict)
                    {
                        $eventArray[$e->packlist->event_id][$data]['quantity'] += $conflict->quantity;
                        $eventArray[$e->packlist->event_id][$data]['conflict'] += $conflict->quantity;
                    }
                }
                
                
            }
        }
        $saveArray = [];
        foreach ($savedData as $d)
        {
            if (!isset($saveArray[$d->id]))
            {
                $saveArray[$d->id] = $eventArray[$d->id];
            }
            $saveArray[$d->id][$d->day]['quantity'] = $d->value;
            
        }
        foreach ($saveArray as $event_id => $val)
        {
            //kasujemy wszystkie rezerwacje tego sprzętu w tych eventach
            $packlists = Packlist::find()->where(['event_id'=>$event_id])->all();
            foreach ($packlists as $p)
            {
                $g = \common\models\PacklistGear::findOne(['gear_id'=>$id, 'packlist_id'=>$p->id]);
                if ($g)
                {
                    if ($g->start_time<=$start)
                    {
                        //jeśli zaczyna się wcześniej to skracamy
                        $g->end_time = date("Y-m-d", time() - 7*24*60*60)." 23:59:00";
                        $g->save();
                    }else{
                        $g->delete();
                    }
                }
            }
            \common\models\EventConflict::deleteAll(['gear_id'=>$id, 'event_id'=>$event_id]);
        }
        foreach ($saveArray as $event_id => $val)
        {
            //rezerwujemy sprzęty na nowo
            $packlists = [];
            $toSave = [];
            $first = false;
            foreach ($val as $day=>$q)
            {
                if ($q['quantity']>0)
                {
                    if (!$first)
                    {
                        $first = $day;
                    }
                    $last = $day;
                }
            }
            $packlists = [];
            $packlists[] = ['start'=>$first, 'quantity'=>$val[$first]['quantity'], 'last'=>$first];
            
            $lastDay = $val[$first]['quantity'];
            foreach ($val as $day=>$q)
            {
                if (($day>$first)&&($day<=$last))
                {
                    
                        if ($q['quantity']==$lastDay)
                        {
                            foreach ($packlists as $i => $p)
                            {
                                $packlists[$i]['last'] = $day;
                            }
                            
                        }else{
                            if ($q['quantity']>$lastDay)
                            {
                                foreach ($packlists as $i => $p)
                                {
                                    $packlists[$i]['last'] = $day;
                                }
                                $packlists[] = ['start'=>$day, 'quantity'=>$q['quantity']-$lastDay, 'last'=>$day];
                            }else{
                                $total = 0;
                                foreach ($packlists as $i => $p)
                                {
                                    $total+=$p['quantity'];
                                    if ($total<=$q['quantity'])
                                    {
                                        $packlists[$i]['last'] = $day;
                                    }else{
                                        $save = min($total - $q['quantity'], $p['quantity']) ;
                                        $toSave[] = ['start'=>$p['start'], 'quantity'=>$save, 'last'=>$p['last']];
                                        if ($save==$p['quantity'])
                                        {
                                            unset($packlists[$i]);
                                        }else{
                                            $packlists[$i]['quantity'] = $p['quantity']-$save;
                                            $packlists[$i]['last'] = $day;
                                        }
                                    }
                                    
                                }
                            }
                        }
                        $lastDay = $q['quantity'];
                    }
                }
                                foreach ($packlists as $i => $p)
                                {
                                    $toSave[] = $p;
                                }
            if (count($toSave)==1)
            {
                $packlist = Packlist::find()->where(['event_id'=>$event_id, 'main'=>1])->one();
                Event::assignGearToPacklistMax($packlist->id, $id, $toSave[0]['quantity'], $toSave[0]['start']." 00:00:00", $toSave[0]['last']." 23:59:00");
            }else{
                    foreach ($toSave as $s)
                    {
                        $packlist = Packlist::find()->where(['event_id'=>$event_id, 'start_time'=>$s['start']." 00:00:00", 'end_time'=>$s['last']." 23:59:00"])->one();
                        if (!$packlist)
                        {
                            //tworzymy packlistę na te dni
                            $packlist = new Packlist();
                            $packlist->event_id = $event_id;
                            $packlist->start_time = $s['start']." 00:00:00";
                            $packlist->end_time =$s['last']." 23:59:00";
                            if ($s['start']!=$s['last'])
                                $packlist->name = substr($s['start'], 8, 2).".".substr($s['start'], 5, 2)." - ".substr($s['last'], 8, 2).".".substr($s['last'], 5, 2);
                            else
                                $packlist->name = substr($s['start'], 8, 2).".".substr($s['start'], 5, 2);
                            $packlist->color = "#555555";
                            $packlist->save();
                            //echo var_dump($packlist);
                        }
                        Event::assignGearToPacklistMax($packlist->id, $id, $s['quantity'], $s['start']." 00:00:00", $s['last']." 23:59:00");

                    } 
            }
            $event = Event::findOne($event_id);
            $event->deleteEmptyPacklists();
      

    }
        exit();
    }

    public function actionWizja($id)
    {
        //szukamy wydarzeń dla tego sprzętu
        if ((\Yii::$app->params['companyID']!="wizja")&&(\Yii::$app->params['companyID']!="tse")&&(\Yii::$app->params['companyID']!="wizjatest")&&(\Yii::$app->params['companyID']!="kopia1"))
        {
            throw new NotFoundHttpException('Brak dostępu! Funkcja dodatkowo płatna.');
        }
        $gear = Gear::findOne($id);
        $start = date("Y-m-d");

        $end = new \DateTime($start);
        $end->add(new \DateInterval('P12M'));
        $start = new \DateTime($start);
        $start->sub(new \DateInterval('P6D'));
        $end = $end->format('Y-m-d');
        $start = $start->format('Y-m-d');
        $events = $gear->getEvents($start, $end)['events'];
        $eventArray = [];
        $eventList = [];
        $eventDates = [];
        foreach ($events as $e)
        {
            $eventList[$e->packlist->event_id] = $e->packlist->event->name;
            $eventDates[$e->packlist->event_id]['start'] = $e->packlist->event->event_start;
            $eventDates[$e->packlist->event_id]['end'] = $e->packlist->event->event_end;
        }
        $sstart = new \DateTime($start);
        while ($sstart->format('Y-m-d') <=$end)
        {
            $data = $sstart->format('Y-m-d');
            $sstart->add(new \DateInterval('P1D'));
            $data2 = $sstart->format('Y-m-d');
            $eventArray[$data] = [];
            foreach ($events as $e)
            {
                if (!isset($eventArray[$data][$e->packlist->event_id]))
                {
                    $eventArray[$data][$e->packlist->event_id]['quantity'] = 0;
                    $eventArray[$data][$e->packlist->event_id]['conflict'] = 0;

                }
                if (($e->start_time<$data2)&&($e->end_time>$data))
                {
                    $eventArray[$data][$e->packlist->event_id]['quantity'] += $e->quantity;
                    $conflict = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$e->id, 'resolved' =>0])->one();
                    if ($conflict)
                    {
                        $eventArray[$data][$e->packlist->event_id]['quantity'] += $conflict->quantity;
                        $eventArray[$data][$e->packlist->event_id]['conflict'] += $conflict->quantity;
                    }
                }
                
                
            }
        }
        return $this->render('wizja', ['model'=>$gear, 'events'=>$eventArray, 'eventList'=>$eventList, 'eventDates'=>$eventDates]);
    }

    public function actionWizjaExport($id)
    {
        //szukamy wydarzeń dla tego sprzętu
        if ((\Yii::$app->params['companyID']!="wizja")&&(\Yii::$app->params['companyID']!="tse")&&(\Yii::$app->params['companyID']!="wizjatest"))
        {
            throw new NotFoundHttpException('Brak dostępu! Funkcja dodatkowo płatna.');
        }
        $gear = Gear::findOne($id);
        $start = date("Y-m-d");

        $end = new \DateTime($start);
        $end->add(new \DateInterval('P12M'));
        $start = new \DateTime($start);
        $start->sub(new \DateInterval('P6D'));
        $end = $end->format('Y-m-d');
        $start = $start->format('Y-m-d');
        $events = $gear->getEvents($start, $end)['events'];
        $eventArray = [];
        $eventList = [];
        $eventDates = [];
        foreach ($events as $e)
        {
            $eventList[$e->packlist->event_id] = $e->packlist->event->name;
            $eventDates[$e->packlist->event_id]['start'] = $e->packlist->event->event_start;
            $eventDates[$e->packlist->event_id]['end'] = $e->packlist->event->event_end;
        }
        $sstart = new \DateTime($start);
        while ($sstart->format('Y-m-d') <=$end)
        {
            $data = $sstart->format('Y-m-d');
            $sstart->add(new \DateInterval('P1D'));
            $data2 = $sstart->format('Y-m-d');
            $eventArray[$data] = [];
            foreach ($events as $e)
            {
                if (!isset($eventArray[$data][$e->packlist->event_id]))
                {
                    $eventArray[$data][$e->packlist->event_id]['quantity'] = 0;
                    $eventArray[$data][$e->packlist->event_id]['conflict'] = 0;

                }
                if (($e->start_time<$data2)&&($e->end_time>$data))
                {
                    $eventArray[$data][$e->packlist->event_id]['quantity'] += $e->quantity;
                    $conflict = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$e->id, 'resolved' =>0])->one();
                    if ($conflict)
                    {
                        $eventArray[$data][$e->packlist->event_id]['quantity'] += $conflict->quantity;
                        $eventArray[$data][$e->packlist->event_id]['conflict'] += $conflict->quantity;
                    }
                }
                
                
            }
        }
        $model = $gear;
        $service = $model->getInService();
        if ($model->no_items)
                    {
                        $gear_q = $model->quantity-$model->getInService();
                        
                    }
                    else
                    {
                        $gear_q = $model->getGearItems()->andWhere(['active'=>1])->count()-$model->getInService();
                    }
        $head = ['Data', 'Suma', 'Serwis'];
        foreach ($eventList as $id=> $name){
            $head[] = $name;
        }
        $data = [];
        $data[] = $head;

        foreach ($eventArray as $day => $e) {
            $total = 0;
            foreach ($e as $id =>$q){
                   $total += $q['quantity'];
                }
            $r = [$day, $gear_q-$total, $service];
            foreach ($e as $id =>$q){
                   $r[] = $q['quantity'];
                }
                $data[] = $r;
        }

        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [

                mb_substr($model->name, 0, 31) => [   // Name of the excel sheet
                    'data' => $data,

                    // Set to `false` to suppress the title row
                    'titles' => false
                ],
            ]
        ]);

        foreach(range('A','F') as $columnID) {
            $file->getWorkbook()->getSheet(0)->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $file->send($model->name.'.xlsx');
    }

    public function actionWizjaAll($keys)
    {
        if ($keys)
        {
            $model = Gear::find()->where(['id'=>json_decode($keys)])->all();
            $start = date("Y-m-d");
            $end = new \DateTime($start);
            $end->add(new \DateInterval('P6M'));
            $end = $end->format('Y-m-d');
            $eventArray = [];
            $sstart = new \DateTime($start);
            while ($sstart->format('Y-m-d') <=$end)
            {
                $data = $sstart->format('Y-m-d');
                $sstart->add(new \DateInterval('P1D'));
                foreach ($model as $gear)
                {
                    if ($gear->no_items)
                    {
                        $eventArray[$data][$gear->id]['quantity'] = $gear->quantity;
                        
                    }
                    else
                    {
                        $eventArray[$data][$gear->id]['quantity'] = $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                }
            }
            foreach ($model as $gear)
            {
                $events[$gear->id] = $gear->getEvents($start, $end)['events'];
            }
            
            $sstart = new \DateTime($start);
            while ($sstart->format('Y-m-d') <=$end)
            {
                $data = $sstart->format('Y-m-d');
                $sstart->add(new \DateInterval('P1D'));
                $data2 = $sstart->format('Y-m-d');
                foreach ($model as $gear)
                {
                    foreach ($events[$gear->id] as $e)
                    {
                            if (($e->start_time<$data2)&&($e->end_time>$data))
                            {
                                $eventArray[$data][$gear->id]['quantity'] -= $e->quantity;
                                $conflict = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$e->id, 'resolved' =>0])->one();
                                if ($conflict)
                                {
                                    $eventArray[$data][$gear->id]['quantity'] -= $conflict->quantity;
                                }
                            }
                    }
                    
                }
            }
            return $this->render('wizja-all', ['gears'=>$model, 'events'=>$eventArray]);
        }else{
            exit;
        }      
    }

    public function actionFavorite($id)
    {
        $gear = \common\models\GearFavorite::findOne(['gear_id'=>$id, 'user_id'=>Yii::$app->user->id]);
        if ($gear)
        {
            $gear->delete();
        }else{
            $gear = new \common\models\GearFavorite();
            $gear->user_id = Yii::$app->user->id;
            $gear->gear_id = $id;
            $gear->position = \common\models\GearFavorite::find()->where(['user_id'=>Yii::$app->user->id])->count();
            $gear->save();
        }
        exit;
    }


    public function actionPrices($c=null, $s=null, $s2=null, $priceGroup=null)
    {
        Url::remember();

        if (!$c){
            $cat = \common\models\GearCategory::find()->where(['active'=>1])->andWhere(['lvl'=>1])->one();
            $c=$cat->id;
        }
        $search = new WarehouseSearch();
        $search->attributes = Yii::$app->request->get();
        $search->categories = [$c, $s, $s2];
        $category = $this->_getCategory([$c, $s, $s2]);
        
        if (!$priceGroup){
            $priceGroup = \common\models\PriceGroup::find()->where(['active'=>1])->asArray()->one()['id'];
        }
        $model = new PriceForm(['gears'=> $search->getGearDataProvider(false)->getModels(), 'group'=>$priceGroup]);
            $ids = ArrayHelper::map(\common\models\GearsPriceGroup::find()->where(['price_group_id'=>$priceGroup])->asArray()->all(), 'gears_price_id', 'gears_price_id');
            $groups = \common\models\GearsPrice::find()->where(['type'=>1])->orWhere(['type'=>2, 'gear_category_id'=>$c])->orWhere(['type'=>2, 'gear_category_id'=>$s])->all();
            $groupsArr = [];
            foreach ($groups as $group)
            {
                if (in_array($group->id, $ids))
                    $groupsArr[] = $group;
            }
            $groups = $groupsArr;
        if (Yii::$app->request->isPost)
        {
            $model->loadAndSave();
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Zapisano'));
            return $this->refresh();
        }
        $priceGroups = \common\models\PriceGroup::find()->where(['active'=>1])->all();
        return $this->render('prices', [
            'model' => $model,
            'warehouse'=>$search,
            'category'=>$category,
            'groups'=>$groups,
            'priceGroups'=>$priceGroups,
            'priceGroup'=>$priceGroup
        ]);
    }

    /**
     * Lists all Gear models.
     * @return mixed
     */
    public function actionIndex()
    {
        $gears = Gear::find()->where(['no_items'=>0])->all();
        $warehouses = \common\models\Warehouse::find()->all();
        foreach ($gears as $g)
        {
            foreach ($warehouses as $w)
            {
                $wq = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$w->id, 'gear_id'=>$g->id])->one();
                if (!$wq)
                {
                    $wq = new \common\models\WarehouseQuantity();
                    $wq->gear_id = $g->id;
                    $wq->warehouse_id = $w->id;
                    $wq->quantity = 0;
                    $wq->save();
                }
            }
        }
        $searchModel = new GearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCalendar($id=null, $start=null, $end=null, $connected=null, $keys=null)
    {
        if ($keys)
        {
            $model = Gear::find()->where(['id'=>json_decode($keys)])->all();
        }else{
            $model = $this->findModel($id);
        }
        
        $this->layout = 'empty';
        return $this->render('calendar', [
            'model' => $model,
            'start'=>$start,
            'end'=>$end,
            'connected'=>$connected,
            'keys'=>$keys
        ]);
    }

    public function actionCalendarArray($id, $start, $end)
    {
        $model = $this->findModel($id);
        $r = $model->getGearCalendarArray(substr($start, 0, 10), substr($end, 0, 10));
        return json_encode($r['events']);
    }

    public function actionDeleted()
    {
        $searchModel = new GearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>0]);
        Url::remember();
        return $this->render('deleted', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gear model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();
        $model = $this->findModel($id);
        $priceForm = new PriceForm(['gears'=> [$model]]);
        $groups = \common\models\GearsPrice::find()->where(['type'=>1])->orWhere(['type'=>3, 'gear_id'=>$id])->orWhere(['type'=>2, 'gear_category_id'=>[$model->category_id, $model->category->getMainCategory()]])->all();
        $rfids = [];
        if ($model->no_items) {
            /** @var \common\models\GearItem $item */
            /*item = $model->gearItems[0];
            $rfids = $item->gearItemsNoItemsRfid;
            for ($i = count($item->gearItemsNoItemsRfid); $i < $model->quantity; $i++) {
                $newRfid = new GearItemsNoItemsRfid();
                $newRfid->gear_item_id = $item->id;
                $rfids[] = $newRfid;
            }
            */
        }
        if (Yii::$app->request->post()){
            if (Model::loadMultiple($rfids, Yii::$app->request->post()) &&
                Model::validateMultiple($rfids)) {
                foreach ($rfids as $rfid) {
                    $rfid->save();
                }
            }
            $priceForm->loadAndSave();

        }
        $query = \common\models\Event::find();
        $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
                'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]],
                'pagination'=>false,
            ]);
        $notedataProvider = new \yii\data\ActiveDataProvider([
                'query' => \common\models\Note::find()->where(['gear_id'=>$model->id]),
                'pagination'=>false,
            ]);
        $ids = ArrayHelper::map(EventGear::find()->where(['gear_id'=>$model->id])->asArray()->all(),'event_id', 'event_id');
        $dataProvider->query->andWhere(['IN', 'id', $ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];
        return $this->render('view', [
            'model' => $model,
            'rfids' => $rfids,
            'dataProvider'=>$dataProvider,
            'noteDataProvider'=>$notedataProvider,
            'priceForm'=>$priceForm,
            'groups'=>$groups
        ]);
        }

    public function actionCount($id)
    {
        $model = GearItem::find()->where(['active'=>1, 'gear_id'=>$id])->orderBy('number DESC')->one();
        if ($model)
            echo $model->number+1; 
        else
            echo "1";

        exit;
    }

    /**
     * Creates a new Gear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Gear();
        $model->unit = 'SZT';
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id'=>$model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
                'rfids' => null
            ]);
        }
    }

    public function actionUpdateInfo($id)
    {
        $model = $this->findModel($id);
        $items = [];
        $formModel = new GearInfoForm;
        foreach ($model->gearItems as $item)
        {
            if ($item->active)
                $items[$item->id] = $item->name." [".$item->number."]";
        }
        if ($model->no_items)
        {
            $items = ["0"=>Yii::t('app', 'Brak egzemplarzy')];
            $formModel->info = $model->info2; 
        }
        if (Yii::$app->request->post())
        {
            $formModel->load(Yii::$app->request->post());
            if ($model->no_items)
            {
                $model->info2 = $formModel->info;
                $model->save();
            }else{
                $item = GearItem::findOne($formModel->gear_item_id);
                $item->info = $formModel->info;
                $item->save();
            }
            return $this->goBack();
        }else{
            return $this->render('update-info', [
                'model' => $model,
                'items' => $items,
                'formModel' =>$formModel
            ]);             
        }
      
    }

    /**
     * Updates an existing Gear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $back="list")
    {
        $model = $this->findModel($id);
        $rfids = [];
        if($model->category_id==1)
        {
            $model->category_id = null;
        }
        if ($model->no_items) {
            /** @var \common\models\GearItem $item */
            $item = $model->gearItems[0];
            $rfids = $item->gearItemsNoItemsRfid;
            for ($i = count($item->gearItemsNoItemsRfid); $i < $model->quantity; $i++) {
                $newRfid = new GearItemsNoItemsRfid();
                $newRfid->gear_item_id = $item->id;
                $rfids[] = $newRfid;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if ($model->no_items) {
                /** @var \common\models\GearItem $item */
                $item = $model->gearItems[0];
                $rfids = $item->gearItemsNoItemsRfid;
                for ($i = count($item->gearItemsNoItemsRfid); $i < $model->quantity; $i++) {
                    $newRfid = new GearItemsNoItemsRfid();
                    $newRfid->gear_item_id = $item->id;
                    $rfids[] = $newRfid;
                }
            }

            if ($model->quantity>0){
                if (Model::loadMultiple($rfids, Yii::$app->request->post()) &&
                    Model::validateMultiple($rfids)) {
                    foreach ($rfids as $rfid) {
                        $rfid->save();
                    }
                }
            }

            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if ($back=="list")
            {
                    return $this->redirect(['/warehouse/index', 'c'=>$model->category->id]);
            }
            if ($back=="view")
            {
                return $this->redirect(['view', 'id'=>$model->id]);
            }
            return $this->goBack();
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
                'rfids' => $rfids
            ]);
        }
    }

    /**
     * Deletes an existing Gear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $items = $model->getGearItems()->andWhere(['active' => 1])->count();
        return $this->redirect(['delete-info', 'id'=>$id]);
    }

    public function actionDeleteInfo($id)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->info = "";
        $items = $model->getGearItems()->andWhere(['active' => 1])->count();
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $items = $model->getGearItems()->andWhere(['active' => 1])->all();
            foreach ($items as $item)
            {
                $item->active = 0;
                $item->description = $model->info;
                $item->save();
            }
            \common\models\GearConnected::deleteAll(['gear_id'=>$model->id]);
            \common\models\GearOuterConnected::deleteAll(['gear_id'=>$model->id]);
            \common\models\GearConnected::deleteAll(['connected_id'=>$model->id]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('delete-info', [
                'model' => $model,
                'items' =>$items
                ]);
        }
    }

    public function actionError($id) {
        $model = $this->findModel($id);
        return $this->render('delete-error', ['model'=>$model]);
    }

    /**
     * Finds the Gear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gear::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionList($id=null, $q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $out = ['results' => ['id' => '', 'text' => '']];

        $cat = GearCategory::findOne($id);

        $gears = Gear::find()->joinWith(['category'])->where(['>=','gear_category.lft',$cat->lft])->andWhere(['<=','gear_category.rgt',$cat->rgt])->all();

        $out['results'] = [];
        foreach ($gears as $gear)
        {
            $out['results'][] = [
                'id' => $gear->id,
                'text' => $gear->name,
            ];
        }

        return $out;
    }

    public function actionGetGearAsJson($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return Gear::findOne($id);
    }



    public function actionImport()
    {
        $modelForm = new GearForm;

        if (Yii::$app->request->isPost)
        {
            $modelForm->filename = UploadedFile::getInstance($modelForm, 'filename');
            $models = [];
            $modelsNot = [];
            if ($modelForm->upload()) {
            $fileName = Yii::getAlias('@uploadroot/xls/'.$modelForm->filename);
            $data = \moonland\phpexcel\Excel::widget([
            'mode' => 'import', 
            'fileName' => $fileName, 
            'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
            'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
            ]);
            $models = [];
            $modelsNoCat = [];
            if ($data)
            {
            foreach ($data as $c)
            {
                //Sprawdzamy czy sprzęt o takiej nazwie już istnieje
                $gear = Gear::find()->where(['name'=>$c['Nazwa'], 'active'=>1])->one();
                if (!$gear)
                {
                    //szukamy kategorii
                    $category = GearCategory::find()->where(['name'=>$c['Kategoria'], 'active'=>1])->one();
                    if ($category)
                    {
                        $gear = new Gear;
                        $gear->name = $c['Nazwa'];
                        $gear->category_id = $category->id;
                        $gear->power_consumption = $c['Pobór prądu'];
                        $gear->brightness = $c['Jasność'];
                        $gear->width = $c['Szer. [cm]'];
                        $gear->height = $c['Wys. [cm]'];
                        $gear->depth = $c['Głęb. [cm]'];
                        $gear->weight = $c['Waga [kg]'];
                        $gear->volume = $gear->width*$gear->height*$gear->depth*0.000001;
                        if (isset($c['Cena']))
                            $gear->price = $c['Cena'];
                        if (isset($c['Foto']))
                            $gear->photo = $c['Foto'];
                        if (isset($c['Opis']))
                            $gear->info = $c['Opis'];
                        if (isset($c['UWAGI']))
                            $gear->info2 = $c['UWAGI'];
                        if (isset($c['Oferta']))
                        {
                            $gear->visible_in_offer = $c['Oferta'];
                        }
                        if (isset($c['Magazyn']))
                        {
                            $gear->warehouse = $c['Magazyn'];
                        }
                        if ($c['Liczba sztuk']!="")
                        {
                            //dodajemy model z odpowiednią liczbą sztuk
                            $gear->quantity = $c['Liczba sztuk'];
                            $gear->no_items = 1;
                        }
                        $gear->save();
                        if ($gear->save())
                            $models[] = $gear->name." (model)";
                        else{
                            echo var_dump($gear);
                            $modelsNot[] = $c['Nazwa']." (model) błąd zapisu";
                        }
                    }else{
                        if ($c['Nazwa']!="")
                        $modelsNot[] = $c['Nazwa']." (model) brak kategorii w bazie";
                    }
                }else{
                    $modelsNot[] = $c['Nazwa']." (model) już w bazie";
                    $gear->power_consumption = $c['Pobór prądu'];
                        $gear->brightness = $c['Jasność'];
                        $gear->width = $c['Szer. [cm]'];
                        $gear->height = $c['Wys. [cm]'];
                        $gear->depth = $c['Głęb. [cm]'];
                        $gear->weight = $c['Waga [kg]'];
                        $gear->volume = $gear->width*$gear->height*$gear->depth*0.000001;
                        $gear->save();
                }
                if (($c['Liczba sztuk']=="")&&($gear))
                {
                    //dodajemyegzemplarz
                    $gearItem = GearItem::find()->where(['gear_id'=>$gear->id, 'number'=>$c['Numer'], 'active'=>1])->one();
                    if (!$gearItem)
                    {
                        $gearItem = new GearItem;
                        $gearItem->gear_id = $gear->id;
                        if ($c['Numer seryjny'])
                            $gearItem->serial = $c['Numer seryjny'];
                        else
                            $gearItem->serial = "";
                        $gearItem->number = $c['Numer'];
                        $gearItem->description = $c['Informacje (dla poszczególnego egzemplarza)'];
                        $gearItem->info = $c['Uwagi (dla poszczególnego egzemplarza)'];
                        $gearItem->name = $gear->name;
                        if (isset($c['Magazyn']))
                        {
                            $gearItem->warehouse = $c['Magazyn'];
                        }
                        if ($gearItem->save())
                            $models[] = $gearItem->name." [".$gearItem->number."]";
                        else
                            $modelsNot[] = $c['Nazwa']." [".$c['Numer']."] c";
                    }else{
                        $modelsNot[] = $c['Nazwa']." [".$c['Numer']."] b";
                    }
                }
            }
            }else{
                 echo "Excel może mieć tylko jeden arkusz";
            }


        }else{
            echo "Błąd xls";
        }
                return $this->render('import-report', [
                'models' => $models,
                'modelsNot'=>$modelsNot
            ]);          
        }else{
            return $this->render('import', [
                'model' => $modelForm,
            ]);
        }


    }

        public function actionImport2()
    {
        $modelForm = new GearForm;

        if (Yii::$app->request->isPost)
        {
            $modelForm->filename = UploadedFile::getInstance($modelForm, 'filename');
            $models = [];
            $modelsNot = [];
            if ($modelForm->upload()) {
            $fileName = Yii::getAlias('@uploadroot/xls/'.$modelForm->filename);
            $data = \moonland\phpexcel\Excel::widget([
            'mode' => 'import', 
            'fileName' => $fileName, 
            'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
            'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
            ]);
            $models = [];
            $modelsNoCat = [];
            $gears = [];
            if ($data)
            {
            foreach ($data as $c)
            {
                //Sprawdzamy czy sprzęt o takiej nazwie już istnieje
                $gear = Gear::find()->where(['name'=>$c['Nazwa'], 'active'=>1])->one();
                
                $warehouse = \common\models\Warehouse::find()->where(['name'=>$c['Magazyn']])->orWhere(['short_name'=>$c['Magazyn']])->one();
                if (!$warehouse)
                    $warehouse = \common\models\Warehouse::find()->one();
                if (!$gear)
                {
                    //szukamy kategorii
                    $category = GearCategory::find()->where(['name'=>$c['Kategoria'], 'active'=>1])->one();

                    if ($category)
                    {
                        $gear = new Gear;
                        $gear->name = $c['Nazwa'];
                        $gear->category_id = $category->id;
                        $gear->power_consumption = $c['Pobór prądu'];
                        $gear->width = $c['Szer. [cm]'];
                        $gear->height = $c['Wys. [cm]'];
                        $gear->depth = $c['Głęb. [cm]'];
                        $gear->weight = $c['Waga [kg]'];
                        $gear->volume = $gear->width*$gear->height*$gear->depth*0.000001;
                        if (isset($c['Kod']))
                        {
                            if ($c['Kod']!="")
                                $gear->code = $c['Kod'];
                        }
                        if (isset($c['Cena']))
                            $gear->price = $c['Cena'];     
                        if (isset($c['Oferta']))
                            $gear->visible_in_offer = $c['Oferta'];                     
                        if (isset($c['Wartość']))
                            $gear->value = $c['Wartość'];
                        if (isset($c['Foto']))
                            $gear->photo = $c['Foto'];
                        if (isset($c['Opis']))
                            $gear->info = $c['Opis'];
                        if (isset($c['UWAGI']))
                            $gear->info2 = $c['UWAGI'];
                        
                        if (isset($c['Magazyn']))
                        {
                            $gear->warehouse = $c['Magazyn'];
                        }
                        if ($c['Liczba sztuk']!="")
                        {
                            //dodajemy model z odpowiednią liczbą sztuk
                            $gear->quantity = intval($c['Liczba sztuk']);
                            $gear->no_items = 1;
                        }
                        if ($gear->save())
                            $models[] = $gear->name." (model)";
                        else{
                            $modelsNot[] = $c['Nazwa']." (model) błąd zapisu";
                            echo var_dump($gear->errors);
                        }
                    }else{
                        if ($c['Nazwa']!="")
                        $modelsNot[] = $c['Nazwa']." (model) brak kategorii w bazie";
                    }
                }else{
                    $modelsNot[] = $c['Nazwa']." (model) już w bazie";
                    $gear->power_consumption = $c['Pobór prądu'];
                        $gear->brightness = $c['Jasność'];
                        $gear->width = $c['Szer. [cm]'];
                        $gear->height = $c['Wys. [cm]'];
                        $gear->depth = $c['Głęb. [cm]'];
                        $gear->weight = $c['Waga [kg]'];
                        $gear->volume = $gear->width*$gear->height*$gear->depth*0.000001;
                        $gear->save();
                }
                if (($c['Liczba sztuk']!="")&&($gear))
                {
                    if ($warehouse)
                    {
                        $wq = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$gear->id, 'warehouse_id'=>$warehouse->id])->one();
                        if (!$wq)
                        {
                            $wq = new \common\models\WarehouseQuantity();
                            $wq->gear_id = $gear->id;
                            $wq->warehouse_id = $warehouse->id;
                            $wq->quantity = 0;
                        }
                        $wq->quantity += $c['Liczba sztuk'];
                        if (isset($c['Miejsce']))
                        {
                            $wq->location = $c['Miejsce'];
                        }
                        $wq->save();
                    }
                }
                if (($c['Liczba sztuk']=="")&&($gear))
                {
                    //dodajemyegzemplarz
                    $gearItem = GearItem::find()->where(['gear_id'=>$gear->id, 'number'=>$c['Numer'], 'active'=>1])->one();
                    if (!$gearItem)
                    {
                        $gearItem = new GearItem;
                        $gearItem->gear_id = $gear->id;
                        if (isset($c['Kod']))
                        {
                            if ($c['Kod']!="")
                                $gearItem->code = $c['Kod'];
                        }
                        if ($c['Numer seryjny'])
                            $gearItem->serial = $c['Numer seryjny'];
                        else
                            $gearItem->serial = "";
                        $gearItem->number = $c['Numer'];
                        $gearItem->description = $c['Informacje (dla poszczególnego egzemplarza)'];
                        $gearItem->info = $c['Uwagi (dla poszczególnego egzemplarza)'];
                        $gearItem->name = $gear->name;
                        if (isset($c['Magazyn']))
                        {
                            $gearItem->warehouse = $c['Magazyn'];
                            if ($warehouse)
                            {
                                $gearItem->warehouse_id = $warehouse->id;
                            }
                            if (isset($c['Miejsce']))
                            {
                                $gearItem->location = $c['Miejsce'];
                            }
                        }
                        if ($gearItem->save())
                            $models[] = $gearItem->name." [".$gearItem->number."]";
                        else
                            $modelsNot[] = $c['Nazwa']." [".$c['Numer']."] c";
                    }else{
                        $modelsNot[] = $c['Nazwa']." [".$c['Numer']."] b";
                    }
                }
                if ($gear)
                    $gears[$gear->id]=$gear;
            }
            }else{
                 echo "Excel może mieć tylko jeden arkusz";
            }
            foreach ($gears as $gear)
            {
                //echo var_dump($gear);
                $gear->recalculateWarehouses();
            }
            //exit;


        }else{
            echo "Błąd xls";
        }
                return $this->render('import-report', [
                'models' => $models,
                'modelsNot'=>$modelsNot
            ]);          
        }else{
            return $this->render('import2', [
                'model' => $modelForm,
            ]);
        }


    }

protected function _getCategory($categories)
    {
        $category = 2;
        if ($categories[0])
            $category = $categories[0];
        if ($categories[1])
            $category = $categories[1];
        if ($categories[2])
            $category = $categories[2];
        $tmpCat = GearCategory::findOne($category);
        return $tmpCat->name;
    }

    public function actionExport($category)
    {
            $cat = GearCategory::findOne($category);
            $ids = $cat->children()->column();
            $c_ids = array_merge([$cat->id], $ids);
            $all = Gear::find()->where(['active'=>1])->andWhere(['category_id'=>$c_ids])->orderBy(['category_id'=>SORT_ASC])->all();


        $content = $this->renderPartial('pdf', [
            'gears' =>  $all,
        ]);

        // setup kartik\mpdf\Pdf component

        $pdf = new Pdf([
            // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
            // A4 paper format
                'format' => Pdf::FORMAT_A4,
            // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
                'destination' => Pdf::DEST_BROWSER,
            // your html content input
                'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
                'marginTop' => 0,
                'marginBottom' => 0,
                'marginLeft'=>0,
                'marginRight'=>0,
                'cssFile' => '@webroot/themes/e4e/css/pdf_offer.css',
            // any css to be embedded if required
                'cssInline' => '.pdf_box .offer_table {
                    border: 2px solid #000;
                    width: 100%;
                }

                .pdf_box .logo img {
                    max-width: 100%;
                    max-height: 200px;
                    height: 200px;
                    width: auto;
                }',
            // set mPDF properties on the fly
                'options' => ['title' => 'Etykiety'],
                'filename' => Yii::getAlias('@uploadroot').'/offer/etykiety.pdf',

        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0        ];
       // return $pdf->render();

        return $this->render('pdf', [
            'gears' =>  $all,
        ]);
    }
}
