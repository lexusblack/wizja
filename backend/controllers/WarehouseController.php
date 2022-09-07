<?php

namespace backend\controllers;

use backend\components\Controller;
use common\components\filters\AccessControl;
use common\models\Event;
use common\models\Stocktaking;
use common\models\StocktakingItem;
use common\models\EventConflict;
use common\models\EventLog;
use common\models\EventGearItem;
use common\models\EventGear;
use common\models\form\GearAssignment;
use common\models\form\WarehouseSearch;
use common\models\Warehouse;
use common\models\Gear;
use common\models\GearSet;
use common\models\GearSimilar;
use common\models\GearCategory;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\OuterGearModel;
use common\models\Offer;
use backend\modules\offers\models\OfferExtraItem;
use common\models\OfferStatut;
use common\models\OfferGear;
use common\models\Rent;
use common\models\RentGearItem;
use common\models\RentGear;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use kartik\mpdf\Pdf;
use yii\helpers\Inflector;
use yii\data\ActiveDataProvider;
use common\models\BarCode;
use yii\bootstrap\Html;

class WarehouseController extends Controller
{

    public $title;
    public $returnRoute;
    public $showGroups = false;

    public $targetClassName;
    public $enableCsrfValidation = false;

    protected $_model;
    protected $_warehouse;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index', 'count-gears', 'count-rent-gears', 'update-count-gears', 'get-assigned-gear', 'excel', 'pdf', 'stocktaking', 'stocktakings', 'get-gear-by-code', 'stocktaking-report', 'stocktaking-lost', 'make-stocktaking', 'reload-bookings', 'reload-quantity', 'get-conflicted', 'active-model', 'active-modelw', 'active-group', 'warehouse', 'edit-location', 'get-codes'],
                    'roles' => ['gearOurWarehouse'],
                ],
                [
                    'allow' => true,
                    'actions' => ['store-order', 'sort-order'],
                    'roles' => ['gearOurWarehouseMoveGear']
                ],
                [
                    'allow' => true,
                    'actions' => ['assign'],
                    'roles' => ['eventEventEditEyeGearManage', 'menuOffersEdit', 'eventRentsEdit']
                ],
                [
                    'allow' => true,
                    'actions' => ['group-create'],
                    'roles' => ['gearOurWarehouseCreateCase']
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'update', 'indexw', 'view', 'manage-warehouse', 'delete'],
                    'roles' => ['gearOurWarehouseCreateCase']
                ],
                [
                    'allow' => true,
                    'actions' => ['assign-offer-gear', 'assign-gear-item-to-offer'],
                    'roles' => ['menuOffersViewEdit', 'eventsEventEditEyeOfferGear']
                ],
                [
                    'allow' => true,
                    'actions' => ['copy-to-outer-gear'],
                    'roles' => ['outerGearCreate']
                ],
                [
                    'allow' => true,
                    'actions' => ['assign-gear', 'assign-gear-packlist', 'unassign-gear-group', 'remove-gear', 'conflict', 'assign-gear-set','assign-gear-connected', 'assign-check-gear', 'gear-similar', 'save-similar', 'save-conflict', 'change-dates',  'check-conflict', 'resolve-conflict','resolve-conflict-partial', 'edit-conflict', 'change-booking', 'gear-conflicts', 'gear-conflicts-modal', 'assign-gear-conflicted', 'conflict-partial', 'favorites', 'gear-group-delete', 'group-change-time', 'check-avability', 'save-gear-hours', 'save-gear-hours-max'],
                    'roles' => ['menuOffersViewEdit', 'eventEventEditEyeGearManage', 'eventEventEditEyeGearDelete', 'eventRentsEdit']
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionActiveModel($c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null, $photos=null, $w=null)
    {
        $search = new WarehouseSearch();
        $search->attributes = Yii::$app->request->get();
        
        if ($c=='favorite')
        {
            $search->favorite = 1;
            $search->categories = [1, $s, $s2];
            $c = 1;
        }else{
            $search->categories = [$c, $s, $s2];
        }
        return $this->renderAjax('active_model', ['warehouse'=>$search, $w=>$w]);
    }

    public function actionActiveModelw($activeModel, $w=null)
    {
        $query = \common\models\GearItem::find()->where(['warehouse_id'=>$w, 'gear_id'=>$activeModel])->andWhere(['active'=>1])->orderBy(['number'=>SORT_ASC]);
        $search = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>false,
            'sort'=>false,
        ]);
        return $this->renderAjax('active_modelw', ['dataProvider'=>$search, 'w'=>$w]);
    }

    public function actionActiveGroup($id)
    {
        $gearItemQuery = GearItem::find()
            ->andWhere([
                'gear_item.active' => 1,
                'gear_item.group_id'=>$id
            ])->orderBy(['number'=>SORT_ASC]);
            $dataProvider = new ActiveDataProvider([
            'query'=>$gearItemQuery,
            'pagination'=>false,
            'sort'=>false,
        ]);
        return $this->renderAjax('active_group', ['dataProvider'=>$dataProvider]);
    }
    public function actionSaveGearHours($id, $start, $end, $gear_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $gear = \common\models\PacklistGear::findOne($gear_id);
        $success = 1;
        $missing = 0;
        $start .=":00";
        $end .=":00";
        if ($start<$gear->start_time )
        {
            //sprawdzamy dostępność
            if ($end>$gear->start_time)
            {
                $available = $gear->gear->getAvailabe($start, $gear->start_time);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        }
        if ($end>$gear->end_time )
        {
            //sprawdzamy dostępność
            if ($start<$gear->end_time)
            {
                $available = $gear->gear->getAvailabe($gear->end_time, $end);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        } 
        if ($success)
        {
            $gear->start_time = $start;
            $gear->end_time = $end;
            $gear->save();
        }

        return ['success'=>$success, 'missing'=>$missing];
    }
    public function actionSaveGearHoursMax($id, $start, $end, $gear_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $gear = \common\models\PacklistGear::findOne($gear_id);
        $success = 1;
        $missing = 0;
        $start .=":00";
        $end .=":00";
        if ($start<$gear->start_time )
        {
            //sprawdzamy dostępność
            if ($end>$gear->start_time)
            {
                $available = $gear->gear->getAvailabe($start, $gear->start_time);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        }
        if ($end>$gear->end_time )
        {
            //sprawdzamy dostępność
            if ($start<$gear->end_time)
            {
                $available = $gear->gear->getAvailabe($gear->end_time, $end);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        } 
        if ($success)
        {
            $gear->start_time = $start;
            $gear->end_time = $end;
            $gear->save();
        }else{
            $gear->start_time = $start;
            $gear->end_time = $end;
            $gear->quantity = $gear->quantity-$missing;
            $gear->save();            
            $model2 = new EventConflict();
            $model2->event_id = $gear->packlist->event_id;
            $model2->gear_id = $gear->gear_id;
            $model2->packlist_gear_id = $gear->id;
            $model2->quantity = $missing;
            $model2->added = $gear->quantity;
            $model2->save();
            $success = 1;
        }

        return ['success'=>$success, 'missing'=>$missing];
    }
    public function actionCheckAvability($id, $start, $end, $gear_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $gear = \common\models\PacklistGear::findOne($gear_id);
        $success = 1;
        $missing = 0;
        $start .=":00";
        $end .=":00";
        if ($start<$gear->start_time )
        {
            //sprawdzamy dostępność
            if ($end>$gear->start_time)
            {
                $available = $gear->gear->getAvailabe($start, $gear->start_time);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        }
        if ($end>$gear->end_time )
        {
            //sprawdzamy dostępność
            if ($start<$gear->end_time)
            {
                $available = $gear->gear->getAvailabe($gear->end_time, $end);
            }else{
                $available = $gear->gear->getAvailabe($start, $end);
            }
            
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                $success = 0;
                $m = $gear->quantity-$available;
                if ($missing<$m)
                    $missing = $m;
            }
        } 

        return ['success'=>$success, 'missing'=>$missing];
    }

    public function actionReloadQuantity($packlist=null, $gear_id, $start, $end)
    {
                    $gear = Gear::findOne($gear_id);
                    if ($gear->type!=1)
                    {
                        return $gear->quantity;
                    }
                    $assigned = 0;
                    if ($gear->no_items)
                    {
                        
                        $serwisNumber = $gear->getNoItemSerwis();
                        $serwis = null;
                        if ($serwisNumber > 0) {
                            $serwis = "<span class='label label-danger'>".Yii::t('app', 'W serwisie').': ' . $serwisNumber."</div>";
                        }
                        $total = ($gear->getAvailabe($start, $end)-$serwisNumber);
                        echo $total. " " . $serwis;
                    }
                    else
                    {
                        $serwisNumber = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_SERVICE])->count();
                        $needSerwis = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_NEED_SERVICE])->count();

                        $serwis = null;
                        $need = null;
                        if ($serwisNumber > 0) {
                            $serwis = "<span class='label label-danger'>".Yii::t('app', 'W serwisie').': ' . $serwisNumber."</span>";
                        }
                        if ($needSerwis > 0) {
                            $need = "<span class='label label-warning'>".Yii::t('app', 'Wymaga serwisu').': ' . $needSerwis."</span>";
                        }
                        $total = ($gear->getAvailabe($start, $end)-$serwisNumber);
                        echo $total. " " . $serwis." ".$need;
                    }
            exit;
    }
    public function actionReloadBookings($packlist=null, $gear_id, $start, $end)
    {
                    $model = Gear::findOne($gear_id);
                    $working = $model->getEvents($start, $end);
                    $workingNear = $model->getEventsNear($start, $end);
                    $result = "";
                    foreach ($working['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " <span class='label label-primary' style='background-color:".$eventGear->packlist->color."'>".$eventGear->quantity."</span><br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($working['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " <span class='label label-primary' style='background-color:".$eventGear->packlist->color."'>".$eventGear->quantity."</span><br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                        
                    }
                    foreach ($workingNear['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    return $result;
        exit;
    }
    public function actionGroupChangeTime($id, $packlist_id)
    {
        $post = Yii::$app->request->post();
        $event = Event::findOne($id);
        $packlist = \common\models\Packlist::findOne($packlist_id);
            if (isset($post['gears']))
            $gears = \common\models\PacklistGear::find()->where(['id'=>$post['gears']])->all();
            else
            $gears = [];
        return $this->renderAjax('group-change-time', ['gears' => $gears, 'event'=>$event, 'packlist'=>$packlist]);
    }
    public function actionGearGroupDelete($id)
    {
        $post = Yii::$app->request->post();
            if (isset($post['gears']))
            $gears = \common\models\PacklistGear::find()->where(['id'=>$post['gears']])->all();
            else
            $gears = [];
            if (isset($post['ogears']))
                $ogears = \common\models\PacklistOuterGear::find()->where(['id'=>$post['ogears']])->all();
            else
                $ogears = [];
            if (isset($post['extra']))
                $extras = EventExtraItem::find()->where(['id'=>$post['extra']])->all();
            else
                $extras = [];
        foreach ($gears as $gear)
        {
            $gear->delete();
        }
        foreach ($ogears as $gear)
        {
            $gear->delete();
        }
    }

    public function actionFavorites($id)
    {
        $gears = Gear::find()->joinWith('gearFavorite')->where(['active'=>1])->orderBy(['gear_favorite.position'=>SORT_ASC])->all();
        $outerGears = OuterGearModel::find()->joinWith('outerGearFavorite')->where(['active'=>1])->orderBy(['outer_gear_favorite.position'=>SORT_ASC])->all();
        $offer = Offer::findOne($id);
        $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->andWhere(['type'=>1])->all();
        return $this->renderAjax('favorites', ['offer'=>$offer, 'gears' => $gears, 'outerGears'=>$outerGears, 'eventRelation'=>$eventRelation]);
    }


    public function actionStocktakingLost()
    {
        $date = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );

        $gears = ArrayHelper::map(GearItem::find()->where(['active'=>1])->andWhere(['or', ['<', 'test_date', $date], ['test_date'=>null]])->asArray()->all(), 'gear_id', 'gear_id');
        return $this->render('stocktaking_lost', ['gears'=>$gears, 'mainCategories' => GearCategory::getMainList(true), 'date'=>$date]);
    }

    public function actionStocktakingReport($id, $pdf=false)
    {
        $model = Stocktaking::findOne($id);
        $items = StocktakingItem::find()->where(['stocktaking_id'=>$id])->asArray()->all();
        $gears = ArrayHelper::map(GearItem::find()->where(['id'=>ArrayHelper::map($items, 'gear_item_id', 'gear_item_id')])->asArray()->all(), 'gear_id', 'gear_id');
        if (!$pdf)
                return $this->render('stocktaking_report', ['model'=>$model, 'items'=>$items, 'gears'=>$gears, 'mainCategories' => GearCategory::getMainList(true)]);
        else{
            $dist = Pdf::DEST_BROWSER;
            $content = $this->renderPartial('stocktaking_report', ['model'=>$model, 'items'=>$items, 'gears'=>$gears, 'mainCategories' => GearCategory::getMainList(true)]);

        // setup kartik\mpdf\Pdf component

        $pdf = new Pdf([
            // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
            // A4 paper format
                'format' => Pdf::FORMAT_A4,
            // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
                'destination' => $dist,
            // your html content input
                'content' => $content,
                'cssFile' => '@webroot/themes/e4e/css/pdf_offer.css',
            // any css to be embedded if required
                'options' => ['title' => Yii::t('app', 'Inwentaryzacja nr ').$model->id],
                'filename' =>'inwentaryzacja_'.$model->id.'.pdf',

        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0        ];
        return $pdf->render();
        }
    }


    public function actionStocktaking()
    {
        $model = new \backend\models\StocktakingForm();
        return $this->render('stocktaking', ['model'=>$model]);
    }

    public function actionStocktakings()
    {
        $models = Stocktaking::find()->orderBy(['datetime'=>SORT_DESC])->all();
        return $this->render('stocktakings', ['models'=>$models]);
    }

    public function actionMakeStocktaking()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            $items = json_decode(Yii::$app->request->post('StocktakingForm')['items']);
            $model = new Stocktaking();
            $model->user_id = Yii::$app->user->id;
            $model->datetime = date('Y-m-d H:i:s');
            $model->save();
            foreach ($items as $id => $value)
            {
                if ($value)
                {
                    $item = new StocktakingItem();
                    $item->gear_item_id = $id;
                    $item->quantity = $value;
                    $item->datetime = $model->datetime;
                    $item->user_id = Yii::$app->user->id;
                    $item->stocktaking_id = $model->id;
                    $item->save();
                    $gearItem = GearItem::findOne($id);
                    $gearItem->test_date = $model->datetime;
                    $gearItem->tester = Yii::$app->user->identity->displayLabel;
                    $gearItem->test_status = strval($value);
                    $gearItem->save();
                }
                
            }
            return ['ok'=>1, 'id'=>$model->id];
        }
        return ['error' => Yii::t('app', 'Wystąpił błąd.')];
    }

    public function actionGetGearByCode($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // rozszyfrowujemy barcody i qrcody
        if (strlen($q) == 13) {
            $id = (int)substr($q, 4, 9);

            // mamy do czynienia z casem (gear_group)
            if (substr($q, 0, 2) == BarCode::ITEMS_GROUP) {
                $gear = GearGroup::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                $items = GearItem::find()->where(['group_id'=>$gear->id])->andWhere(['active'=>1])->all();
                //zwracamy wszystkie itemy
                $total = $items[0]->gear->getGearItems()->andWhere(['active'=>1])->count();
                return ['ok' => true, 'gear' => $items[0]->gear, 'no_items'=>0, 'items'=>$items, 'total'=>$total];

            }

            // mamy do czynienia ze sprzetem z naszego magazynu (gear)
            else if (substr($q, 0, 2) == BarCode::SINGEL_PRODUCT) {
                if (substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                    $gear = GearItem::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                    if ($gear->gear->no_items)
                    {
                        $total= $gear->gear->quantity;
                    }
                    else
                    {
                        $total= $gear->gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                        return ['ok' => true, 'gear' => $gear->gear, 'no_items'=>$gear->gear->no_items, 'items'=>[$gear], 'total'=>$total];
                    }
                }
        }
        // koniec barcodow

        return ['error' => Yii::t('app', 'Nie znaleziono sprzętu o tym kodzie')];
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

    /**
     * Lists all Gear models.
     * @return mixed
     */

        public function actionWarehouse($w, $c=null, $s=null, $s2=null, $q=null)
    {
        $session = Yii::$app->session;
        if (Yii::$app->user->identity->gear_category_id)
        {
            $c=Yii::$app->user->identity->gear_category_id;
        }
        $search = new WarehouseSearch();
        $search->attributes = Yii::$app->request->get();
        if ($c=='favorite')
        {
            $search->favorite = 1;
            $search->categories = [1, $s, $s2];
            $c = 1;
        }else{
            $search->categories = [$c, $s, $s2];
        }
        $category = $this->_getCategory([$c, $s, $s2]);
        $warehouse = Warehouse::findOne($w);
        $dataProvider = $search->searchInWarehouse($params, $warehouse);
        return $this->render('warehouse', [
            'warehouse'=>$search,
            'gearSets'=>$gear_sets,
            's' => $s,
            'category'=>$category,
            'wwarehouse'=>$warehouse,
            'provider'=>$dataProvider
        ]);
    }

    public function actionIndex($c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null, $photos=null)
    {
        $session = Yii::$app->session;
        if (Yii::$app->user->identity->gear_category_id)
        {
            $c=Yii::$app->user->identity->gear_category_id;
        }
        if (!$photos)
        {
            if ($session->get('gear-photos'))
            {
                $photos = $session->get('gear-photos');
            }else{
                $photos = 1;
            }
        }
        $session->set('gear-photos', $photos);
        Url::remember();
        $search = new WarehouseSearch();
        $search->attributes = Yii::$app->request->get();
        
        if ($c=='favorite')
        {
            $search->favorite = 1;
            $search->categories = [1, $s, $s2];
            $c = 1;
        }else{
            $search->categories = [$c, $s, $s2];
        }
        if ($c>1)
        $gear_sets = $this->_loadSets([$c, $s, $s2]);
        else
            $gear_sets = [];
        $category = $this->_getCategory([$c, $s, $s2]);
        return $this->render('index', [
            'warehouse'=>$search,
            'gearSets'=>$gear_sets,
            's' => $s,
            'category'=>$category,
        ]);
    }

    public function actionGetCodes($c, $s=null, $type=null, $photos=0)
    {
            if ($type)
            {
                    $this->layout = false;
                $search = new WarehouseSearch();
                $search->attributes = Yii::$app->request->get();
                $search->categories = [$c, $s, $s2];
                return $this->renderAjax('get-codes', [
                'warehouse'=>$search,
                'type'=>$type,
                'photos'=>$photos
            ]);
            }else{
                return $this->renderAjax('get-codes-modal', [
                'c'=>$c,
                's'=>$s]);
            }

    }

    public function actionGroupCreate()
    {
        $ids = Yii::$app->request->get('id');
        $models = GearItem::findAll($ids);
        $transaction = Yii::$app->db->beginTransaction();
        if ($models)
        {
             try {
                $name = "";
                foreach ($models as $model) {
                    if ($name=="")
                        $name = $model->name." [";
                    else
                        $name .= ", ";
                    $name .= $model->number;
                }
                $name = substr($name, 0, 100);
                $name.="]";
                $group = new GearGroup();
                $group->name = $name;
                $group->save();

                foreach ($models as $model) {
                    if ($model->group_id == null) {
                        $model->group_id = $group->id;
                        $model->save();
                    }
                }

                $transaction->commit();
            }
            catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            if (Yii::$app->request->isAjax == false) {
                return $this->redirect(Url::previous('warehouse'));
            } else{
                echo $group->id;
            }         
        }else{
            if (Yii::$app->request->isAjax == false) {
                return $this->redirect(Url::previous('warehouse'));
            }            
        }



    }

    public function actionAssign($id, $type, $c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null, $conflict=null, $type2=null, $item=null, $packlist=null)
    {
        if (Yii::$app->user->identity->gear_category_id)
        {
            $c=Yii::$app->user->identity->gear_category_id;
        }
        $model = $this->_loadModel($id, $type);
        if ($q)
            $c=1;
        $className = $this->_getClassName($type);
        $eventRelation = [];

        if($className !== Offer::className()){
            if ($className!==Event::className())
            {
                    $view_str = "assign";
            }else{
                    if ($conflict)
                    {
                        $conflictModel = \common\models\EventConflict::findOne($conflict);
                        $packlist = $conflictModel->packlistGear->packlist_id;
                    }
                    $view_str = "assign_event";
            }
            
            $assignedItems = $className::getAssignedQuantities($id);
            $assignedModels = $className::getAssignedGearQuantities($id, $packlist);
        } else {
            $view_str = "offer-assign";
            $assignedItems = $className::getAssignedGearQuantities($id, $type2, $item);
            $assignedModels = $assignedItems;
            if (!$type2)
            {
                $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->andWhere(['type'=>1])->all();
            }else{
                if ($type2 == 'gear')
                {
                    $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->andWhere(['offer_gear_id'=>$item])->all();
                }
                if ($type2 == 'outerGear')
                {
                    $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->andWhere(['offer_outer_gear_id'=>$item])->all();
                }
                if ($type2 == 'extraGear')
                {
                    $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->andWhere(['offer_group_id'=>$item])->all();
                }
            }
            
        }

        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $search = $this->_loadWarehouse($id, $type, $packlist);
            if ($c=='favorite')
        {
            $search->favorite = 1;
            $search->categories = [1, $s, $s2];
            $c = 1;
        }else{
            $search->categories = [$c, $s, $s2];
        }
        
        $search->getDataProviders();
        if ($c>1)
        $gear_sets = $this->_loadSets([$c, $s, $s2]);
        else
            $gear_sets = [];        $category = $this->_getCategory([$c, $s, $s2]);


        $title = $this->title.' - '.Yii::t('app', 'Przypisz sprzęt');
        
//        $this->_setDataProviders($activeModel, $c, $s,$s2,$activeGroup,$from_date, $to_date,$q);
        $viewData = [
            'event'=>$model,
            'type'=>$type,
            'title' => $title,
            'assignedItems'=>$assignedItems,
            'assignedModels'=>$assignedModels,
            'warehouse'=>$search,
            'eventRelation'=>$eventRelation,
            'conflict'=>$conflict,
            'gearSets'=>$gear_sets,
            'category'=>$category,
            'type2'=>$type2,
            'item'=>$item,
            'packlist'=>$packlist
        ];

        if (Yii::$app->request->isAjax)
        {
            return $this->renderAjax($view_str, $viewData);
        }
        return $this->render($view_str, $viewData);
    }


    public function actionAssignOfferGear($id,$type)
    {
        $className = $this->_getClassName($type);

        $params = Yii::$app->request->post();
        $items = [$params['itemId']];

        foreach ($items as $itemId)
        {
            if ($params['add'] == 1)
            {
                $className::assignGear($id, $itemId);
            }
            else
            {
                $className::removeGear($id, $itemId);
            }
        }


    }

    public function actionCopyToOuterGear($c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null)
    {
        if (Yii::$app->user->identity->gear_category_id)
        {
            $c=Yii::$app->user->identity->gear_category_id;
        }
        $search = new WarehouseSearch();
        $search->attributes = Yii::$app->request->get();
        $this->_warehouse = $search;

        $search->categories = [$c, $s, $s2];
        $search->getDataProviders();

        $title = $this->title.' - '.Yii::t('app', 'importuj dane ze sprzętu');

        $viewData = [
            'title' => $title,
            'warehouse'=>$search,
        ];

        return $this->render('copy-to-outer-gear', $viewData);
    }


    protected function _loadWarehouse($id, $type, $packlist=null)
    {
        if ($this->_warehouse === null)
        {
            $model = $this->_loadModel($id, $type);
            $search = new WarehouseSearch();
            $search->attributes = Yii::$app->request->get();
            if (!$packlist){
                    $search->from_date = $model->getTimeStart();
                    $search->to_date = $model->getTimeEnd();               
            }else{
                $pack = \common\models\Packlist::findOne($packlist);
                $search->from_date = $pack->start_time;
                $search->to_date = $pack->end_time;
            }

            $search->type = $type;
            $this->_warehouse = $search;
        }

        return $this->_warehouse;
    }

    protected function _loadSets($categories)
    {
        $category = 2;
        if ($categories[0])
            $category = $categories[0];
        if ($categories[1])
            $category = $categories[1];
        if ($categories[2])
            $category = $categories[2];
        $tmpCat = GearCategory::findOne($category);
        $ids = [];
        if ($tmpCat !== null)
        {
                $ids = $tmpCat->children()->column();
        }
        $c_ids = array_merge([$category], $ids);
        $sets = GearSet::find()->where(['IN', 'category_id', $c_ids])->all();
        return $sets;
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


    protected function _loadModel($id,$type)
    {
        if ($this->_model === null)
        {
            $className = $this->_getClassName($type);
            $model = $className::findOne($id);
            if ($model===null)
            {
                throw new NotFoundHttpException(Yii::t('app', 'Brak modelu'));
            }
            $this->_model = $model;
        }
        return $this->_model;
    }

    protected function _getClassName($type)
    {
        if ($this->targetClassName == null)
        {
            switch ($type)
            {
                case 'event':
                    $this->targetClassName = Event::className();
                    $this->title .= Yii::t('app', 'Wydarzenie').' ';
                    $this->returnRoute = '/event/view';
                    break;
                case 'rent':
                    $this->targetClassName = Rent::className();
                    $this->title .= Yii::t('app', 'Wypożyczenie').' ';
                    $this->returnRoute = '/rent/view';
                    break;
                case 'offer':
                    $this->targetClassName = Offer::className();
                    $this->title .= Yii::t('app', 'Oferta').' ';
                    $this->returnRoute = '/offer/default/view';
                    break;
                default:
                    throw new BadRequestHttpException(Yii::t('app', 'Błędne żadanie'));
                    break;
            }
        }

        return $this->targetClassName;
    }

    public function actionUnassignGearGroup($event_id, $case_id, $type='event') {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $case = GearGroup::find()->where(['id' => $case_id])->one();
        if ($case == null) {
            throw new Exception(Yii::t('app', 'Nie ma takiej grupy sprzętów'));
        }
        $gears = $case->gearItems;

        foreach ($gears as $gear) {
            $event_gear = null;
            if ($type == 'event') {
                $event_gear = EventGearItem::find()->where(['gear_item_id' => $gear->id])->andWhere(['event_id' => $event_id])->one();
            }
            if ($type == 'rent') {
                $event_gear = RentGearItem::find()->where(['gear_item_id' => $gear->id])->andWhere(['rent_id' => $event_id])->one();
            }
            if ($event_gear != null) {
                $event_gear->delete();
            }

        }

        $response = [
            'success'=>1,
            'error'=>''
        ];

        return $response;
    }

    public function actionRemoveGear($id, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $className = $this->_getClassName($type);
        $request = Yii::$app->request;
        $gear_id = $request->post('itemid'); 
        if ($type!="event")
                $className::removeGear($id, $gear_id);
        else{
            $packlistGear = \common\models\PacklistGear::findOne($gear_id);
            $packlistGear->delete();
        }                       
        return $response;
    }

    /**
     * @param $id integer Event id
     * @param $type string event/rent
     * @param $noItem integer Czy ilość
     */

    public function actionAssignGearSet($id, $type, $item=null, $type2=null, $packlist=null, $start=null, $end=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>'',
            'responses'=>[]
        ];
        $gearSet = GearSet::findOne($request->post('set_id'));
        $className = $this->_getClassName($type);
        $event = $className::find()->where(['id'=>$id])->one();
        foreach ($gearSet->gearSetItems as $gsi)
        {
                $oldQuantity=0;
                if ($type=='event')
                {
                    $egm = \common\models\PacklistGear::findOne(['gear_id'=>$gsi->gear_id, 'packlist_id'=>$packlist]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                }
                if ($type=='rent')
                {
                    $egm = RentGear::findOne(['gear_id'=>$gsi->gear_id, 'rent_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                }
                if ($type=='offer')
                {
                    $params = [];
                    $params['gear_id'] = $gsi->gear_id;
                    $params['offer_id'] = $id;
                    $params['type'] = 1;
                    if ($type2=='gear'){
                        $params['type'] = 2;
                        $params['offer_gear_id'] = $item;
                    }
                    if ($type2=='outerGear'){
                        $params['type'] = 2;
                        $params['offer_outer_gear_id'] = $item;
                    }
                    if ($type2=='extraGear'){
                        $params['type'] = 2;
                        $params['offer_group_id'] = $item;
                    }
                    //echo var_dump($params);
                    $egm = OfferGear::findOne($params);
                    if ($egm){
                        $egm->quantity+= $gsi->quantity;
                        $egm->save();
                    }else{
                        $r['OfferGear'] = $params;
                        $egm = new OfferGear();
                        $egm->load($r);
                        $egm->loadOfferSettings();
                        $egm->type = $params['type'];
                        $egm->quantity = $gsi->quantity;
                        $price = $egm->gear->getDefaultPrice($id);
                        if ($price){
                            $egm->price = $price->price;
                            $egm->gears_price_id = $price->gears_price_id;
                        }else
                            $egm->price = $egm->gear->price;
                        
                        $egm->save(false);
                        //echo var_dump($egm);
                        }
                    $response['responses'][] = [
                                'success'=>1,
                                'error'=>'',
                                'name' => $gsi->gear->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->gear_id,
                                'total'=>$egm->quantity
                            ];


                }
                if ($type=='rent')
                {
                        $model = new GearAssignment();
                        $model->scenario = GearAssignment::SCENARIO_QUANTITY;
                        $model->warehouse = $this->_loadWarehouse($id, $type);
                        $model->quantity = $oldQuantity+$gsi->quantity;
                        $model->oldQuantity =$oldQuantity;
                        $model->itemId = $gsi->gear_id;
                        $model->targetClass = $className;
                        $model->targetId = $id;
                        if ($model->save() == false)
                        {
                            $error = current($model->getFirstErrors());
                            $response['responses'][] = [
                                'success'=>0,
                                'error'=>$error,
                                'name' => $gsi->gear->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->gear_id,
                                'total'=>$model->quantity
                            ];
                        }else{
                            $response['responses'][] = [
                                'success'=>1,
                                'error'=>'',
                                'name' => $gsi->gear->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->gear_id,
                                'total'=>$model->quantity
                            ];
                        }
                }
                if ($type=='event')
                {
                            $model = new \common\models\form\GearAssignmentPacklist();
                            $model->packlist= $packlist;
                            $model->startTime = $start;
                            $model->endTime = $end;
                            $model->itemId = $gsi->gear_id;
                            $model->quantity = $oldQuantity+$gsi->quantity;
                            $model->oldQuantity =$oldQuantity;
                        if ($model->save() == false)
                        {
                            $error = current($model->getFirstErrors());
                            $response['responses'][] = [
                                'success'=>0,
                                'error'=>$error,
                                'name' => $gsi->gear->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->gear_id,
                                'total'=>$model->quantity
                            ];
                        }else{
                            $response['responses'][] = [
                                'success'=>1,
                                'error'=>'',
                                'name' => $gsi->gear->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->gear_id,
                                'total'=>$model->quantity
                            ];
                        }
                }

        }
        foreach ($gearSet->gearSetOuterItems as $gsi)
        {
                $oldQuantity=0;
                $itemId = $gsi->outer_gear_model_id;
                if ($type=='event')
                {
                    $egm = \common\models\EventOuterGearModel::findOne(['outer_gear_model_id'=>$gsi->outer_gear_model_id, 'event_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                    Event::assignOuterGearModel($id, $gsi->outer_gear_model_id, $gsi->quantity+$oldQuantity);
                    
                    $eogm = \common\models\EventOuterGearModel::findOne(['event_id'=>$id, 'outer_gear_model_id'=>$itemId]);
                            $ids = ArrayHelper::map(\common\models\OuterGear::find()->where(['outer_gear_model_id'=>$itemId])->asArray()->all(), 'id', 'id');
                            $eogs = \common\models\EventouterGear::find()->where(['event_id'=>$id, 'outer_gear_id'=>$ids])->all();
                            $sum = 0;
                            foreach ($eogs as $eog)
                            {
                                $sum+=$eog->quantity;
                            }
                            if ($sum >= $eogm->quantity)
                            {
                                $eogm->resolved = 1;
                            }else{
                                $eogm->resolved = 0;
                            }
                            $eogm->save();
                                                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->outerGearModel->name,
                        'quantity'=>$gsi->quantity,
                        'id'=>$gsi->outer_gear_model_id,
                        'total'=>$model->quantity
                    ];
                }
                if ($type=='rent')
                {
                    $egm = \common\models\RentOuterGearModel::findOne(['outer_gear_model_id'=>$gsi->outer_gear_model_id,'rent_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                     Rent::assignOuterGearModel($id, $gsi->outer_gear_model_id, $gsi->quantity+$oldQuantity);
                            $eogm = \common\models\RentOuterGearModel::findOne(['rent_id'=>$id, 'outer_gear_model_id'=>$itemId]);
                            $ids = ArrayHelper::map(\common\models\OuterGear::find()->where(['outer_gear_model_id'=>$itemId])->asArray()->all(), 'id', 'id');
                            $eogs = \common\models\RentOuterGear::find()->where(['rent_id'=>$id, 'outer_gear_id'=>$ids])->all();
                            $sum = 0;
                            foreach ($eogs as $eog)
                            {
                                $sum+=$eog->quantity;
                            }
                            if ($sum >= $eogm->quantity)
                            {
                                $eogm->resolved = 1;
                            }else{
                                $eogm->resolved = 0;
                            }
                            $eogm->save();
                        $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->outerGearModel->name,
                        'quantity'=>$gsi->quantity,
                        'id'=>$gsi->outer_gear_model_id,
                    ];
                }
                if ($type=='offer')
                {
                    $params = [];
                    $params['outer_gear_model_id'] = $gsi->outer_gear_model_id;
                    $params['offer_id'] = $id;
                    $params['type'] = 1;
                    if ($type2=='gear'){
                        $params['type'] = 2;
                        $params['offer_gear_id'] = $item;
                    }
                    if ($type2=='outerGear'){
                        $params['type'] = 2;
                        $params['offer_outer_gear_id'] = $item;
                    }
                    if ($type2=='extraGear'){
                        $params['type'] = 2;
                        $params['offer_group_id'] = $item;
                    }
                    $egm = \common\models\OfferOuterGear::findOne($params);
                    if ($egm){
                        $egm->quantity+= $gsi->quantity;
                        $egm->save();
                        $response['responses'][] = [
                                'success'=>1,
                                'error'=>'',
                                'name' => $gsi->outerGearModel->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->outer_gear_model_id,
                                'total'=>$egm->quantity
                            ];
                    }else{
                        Offer::assignOuterGearModel($id, $gsi->outer_gear_model_id, $gsi->quantity, null, $type2, $item);
                        $response['responses'][] = [
                                'success'=>1,
                                'error'=>'',
                                'name' => $gsi->outerGearModel->name,
                                'quantity'=>$gsi->quantity,
                                'id'=>$gsi->outer_gear_model_id,
                                'total'=>$gsi->quantity
                            ];
                        }
                        
                }
        }
        return $response;
    }
    public function actionAssignGearConflicted($id, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $gsi= Gear::findOne($request->post('gear_id'));
        $quantity = $request->post('quantity');
        $q = $quantity;
        $className = $this->_getClassName($type);
        $event = $className::find()->where(['id'=>$id])->one();
                $oldQuantity=0;
                $currentConlict = 0;
                    $egm = EventGear::findOne(['gear_id'=>$gsi->id, 'event_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;

                    $model2 = EventConflict::findOne(['event_id'=>$event->id, 'gear_id'=>$gsi->id, 'resolved'=>0]);
                        if ($model2)
                            $currentConlict= $model2->quantity;
                        $quantity = $oldQuantity+$quantity;
                        $start = $event->getTimeStart();
                        $end = $event->getTimeEnd();
                        $available = $gsi->getAvailabe($start, $end)+$oldQuantity;
                        $available = $available-$gsi->getInService();
                        if ($available<$quantity)
                        {
                            $response['responses'][] = [
                                'success'=>0,
                                'error'=>Yii::t('app', 'Brak dostępnego sprzętu.'),
                                'name' => $gsi->name,
                                'quantity'=>$quantity,
                                'id'=>$gsi->id,
                                'total'=>$model->quantity
                            ];
                            return $response;
                        }
                        Event::assignGear($event->id, $gsi->id, $quantity);

                        if ($currentConlict>$q)
                        {
                            $conflictQuantity = $currentConlict-$q;
                                $model2 = new EventConflict();
                                $model2->event_id = $event->id;
                                $model2->gear_id = $gsi->id;
                            $model2->quantity = $conflictQuantity;
                            $model2->added = $quantity;
                            $model2->save();
                        }

                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->name,
                        'quantity'=>$q,
                        'id'=>$gsi->id,
                        'total'=>$quantity
                    ];
                
        return $response;
    }



    public function actionAssignGearConnected($id, $type, $packlist=null, $start=null, $end=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $gsi= Gear::findOne($request->post('gear_id'));
        $quantity = $request->post('quantity');
        $className = $this->_getClassName($type);
        $event = $className::find()->where(['id'=>$id])->one();
                $oldQuantity=0;
                $currentConlict = 0;
                if ($type=='event')
                {
                    $egm = \common\models\PacklistGear::findOne(['gear_id'=>$gsi->id, 'packlist_id'=>$packlist]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;

                    $model2 = EventConflict::findOne(['event_id'=>$event->id, 'gear_id'=>$gsi->id, 'resolved'=>0]);
                        if ($model2)
                            $currentConlict= $model2->quantity;
                }
                if ($type=='rent')
                {
                    $egm = RentGear::findOne(['gear_id'=>$gsi->id, 'rent_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                }
                if ($type=='offer')
                {
                    $egm = OfferGear::findOne(['gear_id'=>$gsi->id, 'offer_id'=>$event->id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                }
                if ($type=='event')
                {
                     $model = new \common\models\form\GearAssignmentPacklist();
                    $model->packlist= $packlist;
                    $model->startTime = $start;
                    $model->endTime = $end;
                    $model->itemId = $gsi->id;
                    $model->quantity = $oldQuantity+$quantity;
                    $model->oldQuantity =$oldQuantity;
                }else{
                    $model = new GearAssignment();
                    $model->scenario = GearAssignment::SCENARIO_QUANTITY;
                    $model->warehouse = $this->_loadWarehouse($id, $type);
                    $model->quantity = $oldQuantity+$quantity;
                    $model->oldQuantity =$oldQuantity;
                    $model->itemId = $gsi->id;
                    $model->targetClass = $className;
                    $model->targetId = $id; 
                }

                if ($model->save() == false)
                {
                    $error = current($model->getFirstErrors());

                    if ($type=='event')
                    {
                       /*
                        $quantity = $oldQuantity+$quantity;
                        $start = $event->getTimeStart();
                        $end = $event->getTimeEnd();
                        $available = $gsi->getAvailabe($start, $end)+$oldQuantity;
                        $available = $available-$gsi->getInService();
                        Event::assignGear($event->id, $gsi->id, $available);
                        if ($available<$quantity)
                        {
                            $conflictQuantity = $quantity-$available+$currentConlict;
                                $model2 = new EventConflict();
                                $model2->event_id = $event->id;
                                $model2->gear_id = $gsi->id;
                            $model2->quantity = $conflictQuantity;
                            $model2->added = $available;
                            $model2->save();
                        }
                        */

                    }
                    $response['responses'][] = [
                        'success'=>0,
                        'error'=>Yii::t('app', 'Sprzęt zarezerwowany częściowo. Rozwiąż konflikt w odpowiedniej zakładce'),
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$model->quantity
                    ];
                }else{
                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$model->quantity
                    ];


                }
        return $response;
    }


    public function actionAssignCheckGear($id, $type, $group=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];    
        $request = Yii::$app->request;
        $params = $request->post();
        if ($params['add']==0)
            return $response;
        $className = $this->_getClassName($type);
        $event = $className::find()->where(['id'=>$id])->one();
        if ($group)
        {
            $items = GearItem::find()->where(['group_id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->all();
            $event_quantity = 0;
            
            if ($items)
            {
                $item = $items[0];
                if ($type=="event")
                {
                    $model = EventGear::findOne(['event_id'=>$id, 'gear_id'=>$item->gear_id]);
                    if ($model)
                        $event_quantity = $model->quantity;
                    $available = $item->gear->getAvailabe($event->getTimeStart(), $event->getTimeEnd())+$event_quantity;
                } else{
                    $model = RentGear::findOne(['rent_id'=>$id, 'gear_id'=>$item->gear_id]);
                    if ($model)
                        $event_quantity = $model->quantity;
                    $available = $item->gear->getAvailabe($event->getTimeStart(), $event->getTimeEnd())+$event_quantity;
                }               
            }
            $q = count($items);
            if ($available>=$q){
                $response = [
                'success'=>1,
                'error'=>''
            ]; 
            }else{
                $response = [
                'success'=>0,
                'error'=>''
            ];  
            }          
        }else{
            $item = GearItem::find()->where(['id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->one();
            $event_quantity = 0;
            
            if ($type=="event")
            {
                $model = EventGear::findOne(['event_id'=>$id, 'gear_id'=>$item->gear_id]);
                if ($model)
                    $event_quantity = $model->quantity;
                $available = $item->gear->getAvailabe($event->getTimeStart(), $event->getTimeEnd())+$event_quantity;
            } else{
                $model = RentGear::findOne(['rent_id'=>$id, 'gear_id'=>$item->gear_id]);
                if ($model)
                    $event_quantity = $model->quantity;
                $available = $item->gear->getAvailabe($event->getTimeStart(), $event->getTimeEnd())+$event_quantity;
            }
            if ($available>0){
                $response = [
                'success'=>1,
                'error'=>''
            ]; 
            }else{
                $response = [
                'success'=>0,
                'error'=>''
            ];             
            }           
        }

        return $response;

    }

    public function actionAssignGearPacklist($id,$type, $packlist, $start, $end, $offer=null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>'',
            'connected'=>[],
            'outerconnected'=>[]
        ];
        $error = "";
        $event = Event::find()->where(['id'=>$id])->one();
        

            $model = new \common\models\form\GearAssignmentPacklist();
            $model->packlist= $packlist;
            $model->startTime = $start;
            $model->endTime = $end;
            $model->load(Yii::$app->request->post());
            if ($offer)
            {
                $model->addOld = 1;
            }else{
                $model->addOld = 0;
            }
            $packlist = \common\models\Packlist::find()->where(['id'=>$packlist])->one();
            if ($model->save() == false)
            {
                $connected = [];
                $connectedOuter = [];

                $error = Yii::t('app', 'Brak dostępnych egzemplarzy.');
                $response = [
                    'success'=>0,
                    'error'=>$error,
                    'connected'=>$connected,
                    'outerconnected'=>$connectedOuter
                ];
            }else{
                        
                $connected = [];
                $connectedOuter = [];
                $gi = Gear::find()->where(['id'=>$model->itemId])->one();
                if ($model->oldQuantity<$model->quantity)
                {
                    
                    if (count($gi->gearConnecteds)>0)
                    {
                        $count = $model->quantity-$model->oldQuantity;
                        foreach($gi->gearConnecteds as $gc)
                        {

                            $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }
                    if (count($gi->gearOuterConnecteds)>0)
                    {
                        $count = $model->quantity-$model->oldQuantity;
                        foreach($gi->gearOuterConnecteds as $gc)
                        {

                            $connectedOuter[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }                   
                }
                        $response = [
                            'success'=>1,
                            'error'=>'',
                            'connected'=>$connected,
                    'outerconnected'=>$connectedOuter
                        ];


                }

        return $response;



    }

    public function actionAssignGear($id,$type, $noItem=0, $group=0, $model=0) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>'',
            'connected'=>[],
            'outerconnected'=>[]
        ];

        $className = $this->_getClassName($type);
        $event = $className::find()->where(['id'=>$id])->one();

        $request = Yii::$app->request;
        if ($noItem == 0)
        {
            if ($request->post('model', false) != false)
            {
                $model = new GearAssignment();
                $model->scenario = GearAssignment::SCENARIO_DEFAULT;
                $model->warehouse = $this->_loadWarehouse($id, $type);
                $model->load(Yii::$app->request->post());
                $model->targetClass = $className;
                $model->targetId = $id;
                $gi = GearItem::find()->where(['id'=>$model->itemId])->one();

                if ($model->save() == false)
                {
                    $error = current($model->getFirstErrors());
                    $response = [
                        'success'=>0,
                        'error'=>$error,
                        'connected'=>$connected
                    ];
                }else{



                }
            }
            else
            {
                $params = $request->post();
                $params['itemId'] = ArrayHelper::getValue($_POST, 'itemId', ArrayHelper::getValue($_POST, 'itemid'), 0);

                if ($model == 1) {
                    $items = GearItem::find()->where(['gear_id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->select('id')->column();
                    $isAtLeastOne =  false;
                    $done_groups = [];
                    foreach ($items as $item) {
                        $gear =  GearItem::find()->where(['id'=>$item])->andWhere(['active'=>1])->andWhere(['status'=>1])->asArray()->one();
                        $gear_item = GearItem::find()->where(['id'=>$gear['id']])->andWhere(['active'=>1])->andWhere(['status'=>1])->one();
                        if ($gear_item->isAvailable($event)) {
                            if (EventGearItem::find()->where(['event_id'=>$event->id])->andWhere(['gear_item_id'=>$gear_item->id])->count() == 0) {
                                $isAtLeastOne = true;
                                $gear['qrcode'] = $gear_item->getBarCodeValue();
                                if ($gear['group_id'] == null) {
                                    $response['gear_items'][] = $gear;
                                }
                                else {
                                    if (!in_array($gear['group_id'], $done_groups)) {
                                        $done_groups[] = $gear['group_id'];
                                        $group_element = [];
                                        foreach (GearItem::find()->where(['group_id' => $gear['group_id']])->andWhere(['active'=>1])->andWhere(['status'=>1])->all() as $gearItem) {
                                            $gear = ArrayHelper::toArray($gearItem);
                                            $gear['qrcode'] = $gearItem->getBarCodeValue();
                                            $group_element['id'] = $gear['group_id'];
                                            $group_element['items'][] = $gear;
                                        }
                                        $response['gear_groups'][] = $group_element;
                                    }
                                }
                            }
                        }
                    }
                    if ($isAtLeastOne) {
                        $response['gear'] = Gear::find()->where(['id'=>$params['itemId']])->andWhere(['active'=>1])->asArray()->one();
                    }

                }
                else if ($group == 1) {
                    $items = GearItem::find()->where(['group_id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->select('id')->column();

                    $group_element = [];
                    $i = 1;
                    foreach (GearItem::find()->where(['group_id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->all() as $gearItem) {
                        if ($gearItem->isAvailable($event)) {
                            if (EventGearItem::find()->where(['event_id'=>$event->id])->andWhere(['gear_item_id'=>$gearItem->id])->count() == 0) {
                                if ($i == 1) {
                                    $response['gear'] = Gear::find()->where(['id' => $gearItem->gear_id])->asArray()->one();
                                }
                                $i++;
                                $gear = ArrayHelper::toArray($gearItem);
                                $gear['qrcode'] = $gearItem->getBarCodeValue();
                                $group_element['id'] = $gear['group_id'];
                                $group_element['items'][] = $gear;
                            }
                        }
                    }
                    $response['gear_group'] = $group_element;

                }
                else {
                    $items = [$params['itemId']];
                    $gear_item = GearItem::find()->where(['id'=>$params['itemId']])->andWhere(['active'=>1])->andWhere(['status'=>1])->one();
                    if ($gear_item && $gear_item->isAvailable($event)) {
                        if (EventGearItem::find()->where(['event_id'=>$event->id])->andWhere(['gear_item_id'=>$params['itemId']])->count() == 0) {
                            $response['gear_item'] = ArrayHelper::toArray($gear_item);
                            $response['gear_item']['qrcode'] = GearItem::find()->where(['id' => $response['gear_item']['id']])->one()->getBarCodeValue();
                            $response['gear'] = Gear::find()->where(['id' => $response['gear_item']['gear_id']])->andWhere(['active'=>1])->asArray()->one();
                        }
                    }
                }
                $i=0;
                
                foreach ($items as $itemId)
                {
                    $gi = GearItem::find()->where(['id'=>$itemId])->one();
                    if ($params['add'] == 1)
                    {

                        $className::assignGearItem($id, $itemId);
                    }
                    else
                    {
                        $className::removeGearItem($id, $itemId);
                    }

                    $i++;

                }
                $connected = [];
                if ($type == 'event')
                {
                    $model = EventGear::findOne(['event_id'=>$id, 'gear_id'=>$gi->gear_id]);
                    if ($model)
                    {
                        if ($params['add'] == 1)
                        {
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = EventGearItem::find()->where(['event_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();
                            if ($model->quantity<$count)
                            {
                                $added = $count - $model->quantity;
                                $model->quantity = $count;
                                $model->save();
                                
                                    if (count($gi->gear->gearConnecteds)>0)
                                    {
                                        foreach($gi->gear->gearConnecteds as $gc)
                                        {

                                            $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($added*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                                        }
                                        
                                    } 
                                    $response = [
                                        'success'=>1,
                                        'error'=>'',
                                        'connected'=>$connected
                                    ];
                            }

                        }else{
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = EventGearItem::find()->where(['event_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();

                            if ($count>0)
                            {

                                $model->quantity = $count;
                                $model->save();                               
                            }else{
                                Event::removeGear($id, $gi->gear_id);
                            }
                            
                        }
                        
                    }else{
                        if ($params['add'] == 1)
                        {
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = EventGearItem::find()->where(['event_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();
                            Event::assignGear($id, $gi->gear_id, $count);
                            foreach($gi->gear->gearConnecteds as $gc)
                                        {

                                            $connected[] = ['id'=>$gc->connected->id, 'count'=>$count*$gc->quantity, 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                                        }
                                         
                                    $response = [
                                        'success'=>1,
                                        'error'=>'',
                                        'connected'=>$connected
                                    ];
                        } 
                    }                    
                }
                if ($type == 'rent')
                {
                    $model = RentGear::findOne(['rent_id'=>$id, 'gear_id'=>$gi->gear_id]);
                    if ($model)
                    {
                        if ($params['add'] == 1)
                        {
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = RentGearItem::find()->where(['rent_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();
                            if ($model->quantity<$count)
                            {
                                $model->quantity = $count;
                                $model->save();
                                   if (count($gi->gear->gearConnecteds)>0)
                                    {
                                        foreach($gi->gear->gearConnecteds as $gc)
                                        {

                                            $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($added*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                                        }
                                        
                                    } 
                                    $response = [
                                        'success'=>1,
                                        'error'=>'',
                                        'connected'=>$connected
                                    ];
                            }

                        }else{
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = RentGearItem::find()->where(['rent_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();

                            if ($count>0)
                            {

                                $model->quantity = $count;
                                $model->save();                               
                            }else{
                                Rent::removeGear($id, $gi->gear_id);
                            }
                            
                        }
                        
                    }else{
                        if ($params['add'] == 1)
                        {
                            $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gi->gear_id])->asArray()->all(), 'id', 'id');
                            $count = RentGearItem::find()->where(['rent_id'=>$id])->andWhere(['IN', 'gear_item_id', $item_ids])->count();
                            Rent::assignGear($id, $gi->gear_id, $count);
                        } 
                    }                    
                }

            }

        }
        else
        {
            $model = new GearAssignment();
            $model->scenario = GearAssignment::SCENARIO_QUANTITY;
            $model->warehouse = $this->_loadWarehouse($id, $type);
            $model->load(Yii::$app->request->post());
            $model->targetClass = $className;
            $model->targetId = $id;
            if ($model->save() == false)
            {
                if ($type=='event')
                {
                    $quantity = EventGear::find()->where(['gear_id'=>$model->itemId])->andWhere(['event_id'=>$id])->count();
                }
                if ($type=='rent')
                {
                    $quantity = RentGear::find()->where(['gear_id'=>$model->itemId])->andWhere(['rent_id'=>$id])->count();
                }
                $connected = [];
                $connectedOuter = [];
                $gi = Gear::find()->where(['id'=>$model->itemId])->one();
                if ($model->oldQuantity<$quantity)
                {
                    
                    if (count($gi->gearConnecteds)>0)
                    {
                        $count = $quantity-$model->oldQuantity;
                        foreach($gi->gearConnecteds as $gc)
                        {

                            $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }   
                    if (count($gi->gearOuterConnecteds)>0)
                    {
                        $count = $quantity-$model->oldQuantity;
                        foreach($gi->gearOuterConnecteds as $gc)
                        {

                            $connectedOuter[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }               
                }
                $error = current($model->getFirstErrors());
                $response = [
                    'success'=>0,
                    'error'=>$error,
                    'connected'=>$connected,
                    'outerconnected'=>$connectedOuter
                ];
            }else{
                        
                $connected = [];
                $connectedOuter = [];
                $gi = Gear::find()->where(['id'=>$model->itemId])->one();
                if ($model->oldQuantity<$model->quantity)
                {
                    
                    if (count($gi->gearConnecteds)>0)
                    {
                        $count = $model->quantity-$model->oldQuantity;
                        foreach($gi->gearConnecteds as $gc)
                        {

                            $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }
                    if (count($gi->gearOuterConnecteds)>0)
                    {
                        $count = $model->quantity-$model->oldQuantity;
                        foreach($gi->gearOuterConnecteds as $gc)
                        {

                            $connectedOuter[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                        }
                        
                    }                   
                }
                        $response = [
                            'success'=>1,
                            'error'=>'',
                            'connected'=>$connected,
                    'outerconnected'=>$connectedOuter
                        ];


                }
        }
        return $response;
    }

    public function actionStoreOrder($favorite=false)
    {
        $data = Yii::$app->request->post('data', null);
        if ($favorite)
        {
            if ($data !== null)
                $models = \common\models\GearFavorite::findAll(['gear_id'=>$data]);
                foreach ($models as $model)
            {
                $model->position = array_search($model->gear_id, $data)+1;
                $model->update(false);
            }
        }else{
                if ($data !== null)
                $models = Gear::findAll($data);

            foreach ($models as $model)
            {
                $model->sort_order = array_search($model->id, $data)+1;
                $model->update(false);
            }
        }

    }

    public function actionSortOrder($id, $c)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Gear::findOne($id);
        $post = Yii::$app->request->post();
        $model->sort_order = $post['Gear'][$post['editableIndex']]['sort_order'];
        $model->save();
        if (Yii::$app->params['companyID']!="corse")
        {
        $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($c);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

        $categoryIds = array_merge([$c], $ids);
        $models = Gear::find()->where(['category_id'=>$categoryIds])->andWhere(['<>', 'id', $id])->andWhere(['visible_in_warehouse'=>1])->andWhere(['active'=>1])->orderBy(['sort_order'=>SORT_ASC])->all();
        $i=1;
        foreach ($models as $m)
        {
            if ($i==$post['Gear'][$post['editableIndex']]['sort_order'])
                $i++;
            $m->sort_order = $i;
            $m->save();
            $i++;

        }
        }
        $output = ['output'=>$model->sort_order, 'message'=>''];
        return $output;
        exit;
    }

    public function actionCustomDates($id, $type, $itemId)
    {
        $owner = $this->_loadModel($id, $type);

        $className = $this->_getClassName($type);
        $assignedItems = $className::getAssignedQuantities($id);

        if ($owner === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $warehouse = $this->_loadWarehouse($id, $type);

        $model = new \common\models\form\GearAssignment();
        $model->warehouse = $warehouse;
        $model->itemId = $itemId;
        $model->setOwner($owner);
        $model->initDates();

        return $this->renderAjax('_dialogDateRange', [
            'owner'=>$owner,
            'itemId' => $itemId,
            'warehouse' => $warehouse,
            'model'=>$model,
        ]);
    }


    public function actionAssignGearItemToOffer($id=null, $event_id, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null, $type='event', $packlist=null)
    {
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        if ($type=='event')
        {
            $offers = Offer::find()->where(['event_id'=>$event_id])->andWhere(['status' => $statuts])->all();
            if(!$offers){
                return $this->redirect(['/event/view', 'id' => $event_id]);
            }
            $eventModel =   Event::findOne($event_id);         
        }else{
            $offers = Offer::find()->where(['rent_id'=>$event_id])->andWhere(['status' => $statuts])->all();
            if(!$offers){
                return $this->redirect(['/rent/view', 'id' => $event_id]);
            } 
            $eventModel =   Rent::findOne($event_id);             
        }


        $event = $this->_loadModel($event_id, $type);
        $className = $this->_getClassName($type);
        if ($type=='event')
            $view_str = "assign-gear-item-from-offer";
        else    
            $view_str = "assign-gear-item-to-offer";
        $assignedItems = $className::getAssignedQuantities($event_id);
        $assignedOItems = false;
        $outerGearDataProvider = false;
        $exploGearDataProvider = false;
        $extraItemDataProvider = false;
        $produkcjaDataProvider = false;
        $offerIds = [];
        foreach ($offers as $offer) {
            $offerIds[] = $offer->id;
        }
        $produkcja_ids = ArrayHelper::map(OfferExtraItem::find()->where(['IN', 'offer_id', $offerIds])->andWhere(['type'=>[4]])->asArray()->all(), 'id', 'id');
        if ($type == 'event')
        {
            $assignedOItems = Event::getAssignedOuterModelQuantities($id);
            $gearQuery = OuterGearModel::find()
            ->indexBy('id')->joinWith(['offerOuterGears'])->where(['IN', 'offer_outer_gear.offer_id', $offerIds])->andWhere(['outer_gear_model.type'=>[1,2]])->andWhere(['or', ['NOT IN', 'offer_outer_gear.offer_group_id', $produkcja_ids], ['offer_outer_gear.offer_group_id' => null] ]);

            $outerGearDataProvider = new ActiveDataProvider([
                'query'=>$gearQuery,
                'pagination'=>false,
                'sort'=>[
                    'defaultOrder' => ['sort_order'=>SORT_ASC],
                ]
            ]);
            $gearQuery = OuterGearModel::find()
            ->indexBy('id')->joinWith(['offerOuterGears'])->where(['IN', 'offer_outer_gear.offer_id', $offerIds])->andWhere(['outer_gear_model.type'=>3])->andWhere(['or', ['NOT IN', 'offer_outer_gear.offer_group_id', $produkcja_ids], ['offer_outer_gear.offer_group_id' => null] ]);

            $exploGearDataProvider = new ActiveDataProvider([
                'query'=>$gearQuery,
                'pagination'=>false,
                'sort'=>[
                    'defaultOrder' => ['sort_order'=>SORT_ASC],
                ]
            ]);
            $extraQuery = OfferExtraItem::find()->where(['IN', 'offer_id', $offerIds])->andWhere(['import'=>1])->andWhere(['type'=>[1]]);
            $extraItemDataProvider = new ActiveDataProvider([
                'query'=>$extraQuery,
                'pagination'=>false,
                'sort'=>[
                    'defaultOrder' => ['category_id'=>SORT_ASC],
                ]
            ]);
            $extraQuery = OfferExtraItem::find()->where(['IN', 'offer_id', $offerIds])->andWhere(['import'=>1])->andWhere(['type'=>[4]]);
            $produkcjaDataProvider = new ActiveDataProvider([
                'query'=>$extraQuery,
                'pagination'=>false,
                'sort'=>[
                    'defaultOrder' => ['category_id'=>SORT_ASC],
                ]
            ]);
        }
        $search = $this->_loadWarehouse($event_id, $type);
        $search->gear_query = Gear::find()->indexBy('id')->joinWith(['offerGears'])->where(['IN', 'offer_gear.offer_id', $offerIds]);
        $search->getDataProviders();

        $title = $this->title.' - '.Yii::t('app', 'Przypisz sprzęt');
        $viewData = [
            'event'=>$event,
            'type'=>$type,
            'title' => $title,
            'assignedItems'=>$assignedItems,
            'warehouse'=>$search,
            'offers' => $offers,
            'eventModel' => $eventModel,
            'assignedOItems'=>$assignedOItems,
            'outerGearDataProvider'=>$outerGearDataProvider,
            'exploGearDataProvider'=>$exploGearDataProvider,
            'extraItemDataProvider'=>$extraItemDataProvider,
            'produkcjaDataProvider'=>$produkcjaDataProvider,
            'packlist_id' => $packlist
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view_str, $viewData);
        }
        return $this->render($view_str, $viewData);
    }

    public function actionConflict($id)
    {
        $conflict = EventConflict::findOne($id);
        if (!$conflict)
            return $this->goBack();
        $conflict->resolved = 1;
        $conflict->save();
        //exit;
        return $this->goBack();
        //return $this->redirect(['/event/view', 'id' => $conflict->event_id, "#"=>"tab-outer-gear"]);
    }

    public function actionConflictPartial($id, $old, $quantity)
    {
        $conflict = EventConflict::findOne($id);
        $quantity = $quantity-$old;
        if ($conflict->quantity>$quantity)
        {
            $conflict->quantity = $conflict->quantity-$quantity;
            \common\models\Note::createNote(2, 'eventConflictPartialResolved', $conflict, $conflict->event_id);
        }else{
            $conflict->resolved = 1;
        }
        $conflict->save();
        //exit;
        //return $this->goBack();
        return $this->redirect(['/event/view', 'id' => $conflict->event_id, "#"=>"tab-outer-gear"]);
    }



    public function actionGetAssignedGear($event_id, $type)
    {
        if ($type=='event')
        {
            $gears = EventGear::find()->where(['event_id'=>$event_id])->all();
            $conflicts = EventConflict::find()->where(['event_id'=>$event_id, 'resolved'=>0])->all();
            return $this->renderPartial('get-assigned-gear', [
                'gears' => $gears,
                'conflicts'=>$conflicts,
                'type'=>$type
            ]);            
        }
        if ($type=='rent')
        {
            $gears = RentGear::find()->where(['rent_id'=>$event_id])->all();
            $conflicts = [];
            return $this->renderPartial('get-assigned-gear', [
                'gears' => $gears,
                'conflicts'=>$conflicts,
                'type'=>$type
            ]);            
        }
        exit;

    }

    public function actionExcel($id=null)
    {
        $data = $this->prepareExcelData($id);
        $file = $this->createExcel($data);

        // Save on disk
        $file->send('magazyn.xlsx');
        exit;
    }

    protected function createExcel($data)
    {
         $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [

                mb_substr("Magazyn", 0, 31) => [   // Name of the excel sheet
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

        return $file;       
    }

       protected function prepareExcelData($id)
    {
        
        $data[] = [Yii::t("app", "Nazwa modelu"), Yii::t("app", "Nazwa"), Yii::t("app", "Typ"), Yii::t("app", "ilość"), Yii::t("app", "Nr"), Yii::t("app", "Kod kreskowy"), Yii::t("app", "Mgazyn"), Yii::t("app", "Miejsce"), Yii::t("app", "Szerokość"), Yii::t("app", "Wysokość"), Yii::t("app", "Głębokość"), Yii::t("app", "Objętość"), Yii::t("app", "Waga"), Yii::t("app", "Pobór prądu"), Yii::t("app", "Kategoria"), Yii::t("app", "Numer seryjny")];
        if ($id)
        {
            $categories = GearCategory::find()->where(['active'=>1])->andWhere(['id'=>$id])->all();

        }else{
            $categories = GearCategory::find()->where(['active'=>1])->andWhere(['lvl'=>1])->all();

        }
        foreach ($categories as $cat)
        {
            $ids = [];
            $ids = $cat->children()->column();
            $c_ids = array_merge([$cat->id], $ids);
            $all = Gear::find()->where(['active'=>1])->andWhere(['category_id'=>$c_ids])->orderBy(['category_id'=>SORT_ASC])->asArray()->all();
            foreach ($all as $gear)
            {
                if ($gear['no_items'])
                {
                    $item = GearItem::find()->where(['active'=>1])->andWhere(['gear_id'=>$gear['id']])->one();
                    if ($item)
                        $data[] = [$gear['name'], $gear['name'], Yii::t('app', 'Ilościowe'), $gear['quantity'], "", $item->getBarCodeValue(), $gear['warehouse'], $gear['location'], $gear['width'], $gear['height'], $gear['depth'], $gear['volume'], $gear['weight'], $gear['power_consumption'], $cat->name];
                    
                }else{
                    $ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gear['id']])->andWhere(['active'=>1])->asArray()->all(), 'group_id', 'group_id');
                    $groups = GearGroup::find()->where(['IN', 'id', $ids])->all();
                    foreach ($groups as $group)
                    {
                        $data[] = [$gear['name'], $group->name, Yii::t('app', 'Case'), "", $group->getItemNumbers(), $group->getBarCodeValue(), $group->warehouse, $group->location, $group->width, $group->height, $group->depth, $group->volume, $group->getTotalWeight(), $gear['power_consumption']*count($group->gearItems), $cat->name];
                    }
                    $items = GearItem::find()->where(['gear_id'=> $gear['id']])->andWhere(['active'=>1])->all();
                    foreach ($items as $item)
                    {
                        $data[] = [$gear['name'], $item->name, Yii::t('app', 'Egzemplarz'),"", $item->number, $item->getBarCodeValue(), $item->warehouse, $item->location, $gear['width'], $gear['height'], $gear['depth'], $gear['volume'], $gear['weight'], $gear['power_consumption'], $cat->name, $item->serial];
                    }
                }
            }
        }
        return $data;
    }

    public function actionPdf($type=1, $columns=2, $gear_id=null, $gear_group_id=null, $gear_item_id=null)
    {
        $data = [];
        if ($gear_item_id)
        {
            $model = GearItem::find()->where(['id'=>$gear_item_id])->one();
            $data[] = [$model->name, $model->number, $model->generateBarCode(), $model->generateQrCode('80%')];
        }else{
             if ($gear_group_id)
            {
                        $group = GearGroup::find()->where(['id' => $gear_group_id])->one();
                        $data[] = [$group->name,  $group->getItemNumbers(), $group->getBarCodeValue(), $group->generateQrCode('80%')];
                        $items = GearItem::find()->where(['group_id'=> $group->id])->andWhere(['active'=>1])->all();
                        foreach ($items as $item)
                        {
                            $data[] = [$item->name,  $item->number, $item->generateBarCode(), $item->generateQrCode('80%')];
                        }
            }else{
                if ($gear_id)
                {
                    $gear = Gear::find()->where(['id'=>$gear_id])->asArray()->one();
                    if ($gear['no_items'])
                    {
                        for ($i=0; $i<$gear['quantity']; $i++)
                        {
                            $item = GearItem::find()->where(['gear_id'=> $gear['id']])->andWhere(['name'=>'_ILOSC_SZTUK_'])->one();
                            $data[] = [$gear['name'], "", $item->generateBarCode(), $item->generateQrCode('80%')];
                        }
                    }else{
                        $ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gear['id']])->andWhere(['active'=>1])->asArray()->all(), 'group_id', 'group_id');
                        $groups = GearGroup::find()->where(['IN', 'id', $ids])->all();
                        foreach ($groups as $group)
                        {
                            $data[] = [$group->name,  $group->getItemNumbers(), $group->generateBarCode(), $group->generateQrCode('80%')];
                        }
                        $items = GearItem::find()->where(['gear_id'=> $gear['id']])->andWhere(['active'=>1])->all();
                        foreach ($items as $item)
                        {
                            $data[] = [$item->name,  $item->number, $item->generateBarCode(), $item->generateQrCode('80%')];
                        }
                    }
                }else{
                    $all = Gear::find()->where(['active'=>1])->asArray()->all();
                    foreach ($all as $gear)
                    {
                        if ($gear['no_items'])
                        {
                            /*
                            for ($i=0; $i<$gear['quantity']; $i++)
                            {
                                $item = GearItem::find()->where(['gear_id'=> $gear['id']])->andWhere(['name'=>'_ILOSC_SZTUK_'])->one();
                                $data[] = [$gear['name'], $gear['name'], Yii::t('app', 'Ilościowe'), "", $item->getBarCodeValue()];
                            }
                            */
                        }else{
                            $ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$gear['id']])->andWhere(['active'=>1])->asArray()->all(), 'id', 'id');
                            $groups = GearGroup::find()->where(['IN', 'id', $ids])->all();
                            foreach ($groups as $group)
                            {
                                $data[] = [$group->name,  $group->getItemNumbers(), $group->generateBarCode(), $group->generateQrCode()];
                            }
                            $items = GearItem::find()->where(['gear_id'=> $gear['id']])->andWhere(['active'=>1])->all();
                            foreach ($items as $item)
                            {
                                $data[] = [$item->name, $item->number, $item->generateBarCode(), $item->generateQrCode()];
                            }
                        }
                    }
                } 
            }          
        }
        /*return $this->render('pdf', [
            'data' =>  $data,
            'type' => $type,
            'columns' =>$columns
        ]);*/
        $pdf = $this->preparePDF($data, $type, $columns);
        return $pdf->render();

    }

    protected function preparePDF($data, $type, $columns){
        $dist = Pdf::DEST_BROWSER;

        $content = $this->renderPartial('pdf', [
            'data' =>  $data,
            'type' => $type,
            'columns' =>$columns
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
                'destination' => $dist,
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
        return $pdf;
        
    }

    function actionGearSimilar($id,  $start=null, $end=null, $packlist=null)
    {
        $post = Yii::$app->request->post();
        $event = Event::findOne($id);
        $gear_id = $post['GearAssignmentPacklist']['itemId'];
        $quantity = $post['GearAssignmentPacklist']['quantity'];
        $oldQuantity = $post['GearAssignmentPacklist']['oldQuantity'];
        $similars = GearSimilar::find()->where(['gear_id'=>$gear_id])->all();
        $gear = Gear::findOne($gear_id);
        if (!$start)
            $start = $event->getTimeStart();
        if (!$end)
            $end = $event->getTimeEnd();
        return $this->renderAjax('_gearSimilars', [
            'similars'=>$similars,
            'gear' => $gear,
            'quantity' => $quantity,
            'oldQuantity' => $oldQuantity,
            'start' => $start,
            'end' => $end,
            'packlist'=>$packlist
        ]);
    }

    function actionSaveConflict($id, $packlist_id, $start, $end)
    {
        $post = Yii::$app->request->post();
        $connected = [];
        $connectedOuter = [];
        $className = $this->_getClassName('event');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];   
        $gear = Gear::findOne($post['gear_id']);
        $eg = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist_id])->andWhere(['gear_id'=>$gear->id])->one();
        $packlist = \common\models\Packlist::findOne($packlist_id);
        $oldQuantity = 0;
        if ($eg)
            $oldQuantity = $eg->quantity;
        $model = new \common\models\form\GearAssignmentPacklist();
        $model->packlist= $packlist_id;
        $model->startTime = $start;
        $model->endTime = $end;
        $model->itemId = $gear->id;
        $model->quantity = $post['quantity'];
        $model->oldQuantity =$oldQuantity;

        if ($model->save() == false)
        {
            $error = current($model->getFirstErrors());
            $response['responses'][] = [
                            'success'=>0,
                            'error'=>$error,
                            'name' => $gear->name,
                            'quantity'=>$model->quantity,
                            'id'=>$gear->id,
                            'total'=>$model->quantity
                        ];
            }else{
                    $eg = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist_id])->andWhere(['gear_id'=>$gear->id])->one();
                    $model2 = EventConflict::findOne(['resolved'=>0, 'packlist_gear_id'=>$eg->id]);
                    if (!$model2)
                    {
                        $model2 = new EventConflict();
                        $model2->event_id = $id;
                        $model2->gear_id = $gear->id;
                        $model2->packlist_gear_id = $eg->id;
                    }
                    $model2->quantity = $post['full'] - $model->quantity;
                    $model2->added = $model->quantity;
                    $model2->save();
                        $response['responses'][] = [
                            'success'=>1,
                            'error'=>'',
                            'name' => $gear->name,
                            'quantity'=>$model->quantity,
                            'id'=>$gear->id,
                            'total'=>$model->quantity
                        ];

                        if ($model->oldQuantity<$model->quantity)
                        {
                            
                            if (count($gear->gearConnecteds)>0)
                            {
                                $count = $model->quantity-$model->oldQuantity;
                                foreach($gear->gearConnecteds as $gc)
                                {

                                    $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name];
                                }
                                
                            }
                            if (count($gear->gearOuterConnecteds)>0)
                            {
                                $count = $model->quantity-$model->oldQuantity;
                                foreach($gear->gearOuterConnecteds as $gc)
                                {

                                    $connectedOuter[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'in_offer'=>$gc->in_offer];
                                }
                                
                            }                  
                        }                     

                }
                $response['connected'] = $connected;
                $response['outerconnected'] = $connectedOuter;
                return $response;
    }

    function actionSaveSimilar($id, $packlist_id, $start, $end)
    {
        $post = Yii::$app->request->post();
        $className = $this->_getClassName('event');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>'',
            'connected'=> []
        ];
        $connected = [];

        foreach ($post as $key=> $val)
        {
            if ($key!="gear_id")
            {
                $g_id = substr($key, 5, strlen($key)-5);
                $gear = Gear::findOne($g_id);
                if ($val>0)
                {

                    $eg = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist_id])->andWhere(['gear_id'=>$gear->id])->one();
                    $oldQuantity = 0;
                    if ($eg)
                    {
                        if ($gear->id != $post['gear_id'])
                        {
                            $val = $val+$eg->quantity;
                            $oldQuantity = $eg->quantity;
                        }
                        
                    }
                    $model = new \common\models\form\GearAssignmentPacklist();
                    $model->packlist= $packlist_id;
                    $model->startTime = $start;
                    $model->endTime = $end;
                    $model->itemId = $gear->id;
                    $model->quantity = $val;
                    $model->oldQuantity =$oldQuantity;
                    if ($model->save() == false)
                    {
                    $error = current($model->getFirstErrors());
                        $response['responses'][] = [
                            'success'=>0,
                            'error'=>$error,
                            'name' => $gear->name,
                            'quantity'=>$model->quantity,
                            'id'=>$gear->id,
                            'total'=>$model->quantity
                        ];
                    }else{
                        $response['responses'][] = [
                            'success'=>1,
                            'error'=>'',
                            'name' => $gear->name,
                            'quantity'=>$model->quantity,
                            'id'=>$gear->id,
                            'total'=>$model->quantity
                        ];

                        if ($model->oldQuantity<$model->quantity)
                        {
                            
                            if (count($gi->gearConnecteds)>0)
                            {
                                $count = $model->quantity-$model->oldQuantity;
                                foreach($gi->gearConnecteds as $gc)
                                {

                                    $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name];
                                }
                                
                            }                  
                        }


                }
                }else{
                        /*if ($gear->id != $post['gear_id'])
                        {
                            $eg = \common\models\PacklistGear::find()->where(['packlist_id'=>$packlist_id])->andWhere(['gear_id'=>$gear->id])->one();
                            if ($eg)
                            {
                                $eg->delete();
                            }


                            
                        }*/
                }


            }
        }
        $response['connected'] = $connected;
        $response['outerconnected'] = [];
        return $response;
    }

    public function actionChangeDates($event_id, $gear_id)
    {
        $post = Yii::$app->request->post();        
        if ($post['type']=='event'){
            $eg = \common\models\PacklistGear::findOne($event_id);
        }
        else
            $eg = RentGear::find()->where(['rent_id'=>$event_id, 'gear_id'=>$gear_id])->one();
        Yii::$app->response->format = Response::FORMAT_JSON;

        $start = str_replace("T"," ",$post['start']);
        $end = str_replace("T"," ",$post['end']);
        $count = $eg->gear->getAvailableDateChanged($start, $end, $event_id, $post['type']);
        if ($count>=$eg->quantity)
        {
                                $eg->start_time = $start;
                                $eg->end_time = $end;
                                $eg->save();
                                return ['success'=>true]; 
        }else{
            return ['success'=>false]; 
        }

    } 

    public function actionGearConflicts($event_id, $gear_id)
    {
        $event = Event::findOne($event_id);
        $ids = ArrayHelper::map(EventGear::find()->where(['gear_id'=>$gear_id])->andWhere(['>', 'end_time', $event->getTimeStart()])->andWhere(['<>','event_id', $event_id])->andWhere(['<', 'start_time', $event->getTimeEnd()])->all(), 'event_id', 'event_id');
        $conflicts = EventConflict::find()->where(['event_id'=>$ids])->andWhere(['gear_id'=>$gear_id])->andWhere(['resolved'=>0])->count();
        /*$conflictArray = [];
        foreach ($conflicts as $c)
        {
            $tmp['id'] = $c->id;
            $tmp['event']=$c->event->name;
            $tmp['event_id']=$c->event_id;
            $tmp['gear_id']=$c->gear_id;
            $tmp['quantity']=$c->quantity;
            $tmp['added']=$c->added;
            $conflictArray[] = $tmp;
        }*/
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['conflicts'=>$conflicts];
    }
    public function actionGearConflictsModal($event_id, $gear_id, $number)
    {
        $event = Event::findOne($event_id);
        $ids = ArrayHelper::map(EventGear::find()->where(['gear_id'=>$gear_id])->andWhere(['>', 'end_time', $event->getTimeStart()])->andWhere(['<>','event_id', $event_id])->andWhere(['<', 'start_time', $event->getTimeEnd()])->all(), 'event_id', 'event_id');
        $conflicts = EventConflict::find()->where(['event_id'=>$ids])->andWhere(['gear_id'=>$gear_id])->andWhere(['resolved'=>0])->all();

        
        return $this->renderAjax('_gearConflicts', [
            'conflicts'=>$conflicts,
            'number'=>$number,
            'gear_id' => $gear_id
        ]);
    }
    public function actionGetConflicted($conflict_id)
    {
                Yii::$app->response->format = Response::FORMAT_JSON;

        $conflict = EventConflict::findOne($conflict_id);
        return $conflict->getEventsConflictedArray();
        //exit;
    }

    public function actionCheckConflict($conflict_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $conflict = EventConflict::findOne($conflict_id);
        return ['success'=>$conflict->checkConflict()];
    }

    public function actionResolveConflict($conflict_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $conflict = EventConflict::findOne($conflict_id);
        return ['success'=>$conflict->resolveConflict()];
    }

    public function actionResolveConflictPartial($conflict_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $conflict = EventConflict::findOne($conflict_id);
        return ['success'=>$conflict->resolveConflictPartial()];
    }

    public function actionEditConflict()
    {
        $post = Yii::$app->request->post();
        if ($post['type']=='event')
        {
            $conflict = EventConflict::find()->where(['event_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one();
            $eg = EventGear::find()->where(['event_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one();           
        }else{
            $conflict = null;
            $eg = RentGear::find()->where(['rent_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one(); 
        }

        return $this->renderAjax('_editConflict', [
            'gear_id'=>$post['gear_id'],
            'event_id'=>$post['event_id'],
            'eg'=>$eg,
            'conflict'=>$conflict,
            'conflict_id'=>$post['conflict_id'],
            'type'=>$post['type']
        ]);
    }

    public function actionChangeBooking()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if ($post['type']=='event')
        {
            $conflict = EventConflict::find()->where(['event_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one();
            $eg = EventGear::find()->where(['event_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one();           
        }else{
            $conflict = null;
            $eg = RentGear::find()->where(['rent_id'=>$post['event_id']])->andWhere(['gear_id'=>$post['gear_id']])->one(); 
        }
        if ($post['quantity']<=$eg->quantity)
        {
            $eg->quantity= $post['quantity'];
            $eg->save();
            if ($post['type']=='event')
                $eg->clearConflicts();
            return ['success'=>true, 'noconflict'=>true];
        }else{
            $conflict->quantity = $post['quantity']-$eg->quantity;
            $conflict->added = $eg->quantity;
            $conflict->save();
        }
        
        return ['success'=>true];
    }


 public function actionIndexw() 
    { 
        $models = \common\models\Warehouse::find()->orderBy(['position'=>SORT_ASC])->all();

        return $this->render('indexw', [ 
            'models' => $models 
        ]); 
    } 

    /** 
     * Displays a single Warehouse model. 
     * @param integer $id
     * @return mixed 
     */ 
    public function actionView($id) 
    { 
        $model = $this->findModel($id); 
        return $this->render('view', [ 
            'model' => $this->findModel($id), 
        ]); 
    } 

    public function actionOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = $this->findModel($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    /** 
     * Creates a new Warehouse model. 
     * If creation is successful, the browser will be redirected to the 'view' page. 
     * @return mixed 
     */ 
    public function actionCreate() 
    { 
        $model = new Warehouse(); 
        $model->position = Warehouse::find()->where(['type'=>$type])->count();
        if ($model->load(Yii::$app->request->post()) && $model->save()) { 
            return $this->redirect(['indexw']); 
        } else { 
            return $this->render('create', [ 
                'model' => $model, 
            ]); 
        } 
    } 

    /** 
     * Updates an existing Warehouse model. 
     * If update is successful, the browser will be redirected to the 'view' page. 
     * @param integer $id
     * @return mixed 
     */ 
    public function actionUpdate($id) 
    { 
        $model = $this->findModel($id); 

        if ($model->load(Yii::$app->request->post()) && $model->save()) { 
            return $this->redirect(['indexw']); 
        } else { 
            return $this->render('update', [ 
                'model' => $model, 
            ]); 
        } 
    } 

    /** 
     * Deletes an existing Warehouse model. 
     * If deletion is successful, the browser will be redirected to the 'index' page. 
     * @param integer $id
     * @return mixed 
     */ 
    public function actionDelete($id) 
    { 
        $this->findModel($id)->delete(); 

        return $this->redirect(['indexw']); 
    }

    public function actionEditLocation($id, $w)
    {
         $wq = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$w])->andWhere(['gear_id'=>$id])->one();
        if (Yii::$app->request->post())
        {
            $wq->load(Yii::$app->request->post());
            $wq->save();
            exit;
        }else{
            return $this->renderAjax('edit_location', ['model'=>$wq]);
        }
    } 


    public function actionManageWarehouse($gear_id)
    {
        
        $gear = Gear::findOne($gear_id);
        if (Yii::$app->request->post())
        {
            $warehouses = Yii::$app->request->post('GearWarehouseForm');
            $warehouses = $warehouses['warehouses'];
            foreach ($warehouses as $id=>$val)
            {
                if ($gear->no_items)
                {
                    foreach ($val as $w=>$b)
                    {
                            $wq = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$w])->andWhere(['gear_id'=>$gear->id])->one();

                            if (!$wq)
                            {
                                $wq = new \common\models\WarehouseQuantity();
                                $wq->gear_id = $gear->id;
                                $wq->warehouse_id = $w;
                                
                            }
                            $wq->quantity = $b;
                            $wq->save();
                    }
                }else{
                        $item = GearItem::findOne($id);
                        foreach ($val as $w=>$b)
                        {
                            if ($b){
                                $item->warehouse_id = $w;
                                $item->event_id = null;
                                $item->rent_id = null;
                                $item->packlist_id = null;
                                $item->outcomed = 0;
                                $war = \common\models\Warehouse::findOne($w);
                                if ($war)
                                    $item->warehouse = $war->name;
                            }
                        }
                        $item->save();
                }
            }
            if (!$gear->no_items)
            {
                $warehouses = Warehouse::find()->all();
                foreach ($warehouses as $w)
                {
                    $count = \common\models\GearItem::find()->where(['warehouse_id'=>$w->id, 'gear_id'=>$gear->id, 'active'=>1])->count();
                    $q = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$w->id, 'gear_id'=>$gear->id])->one();
                    if (!$q)
                    {
                                $q = new \common\models\WarehouseQuantity();
                                $q->gear_id = $gear->id;
                                $q->warehouse_id = $w->id;
                    }
                    $q->quantity = $count;
                    $q->save();
                }
            }
            exit;
        }else{
                    $warehouses = Warehouse::find()->all();
                    $form = new \common\models\form\GearWarehouseForm();
                    $form->loadData($gear, $warehouses);
                    return $this->renderAjax('manage-warehouse', ['gear'=>$gear, 'warehouses'=>$warehouses, 'wform'=>$form]);
        }

    }

     
    /** 
     * Finds the Warehouse model based on its primary key value. 
     * If the model is not found, a 404 HTTP exception will be thrown. 
     * @param integer $id
     * @return Warehouse the loaded model 
     * @throws NotFoundHttpException if the model cannot be found 
     */ 
    protected function findModel($id) 
    { 
        if (($model = Warehouse::findOne($id)) !== null) { 
            return $model; 
        } else { 
            throw new NotFoundHttpException('The requested page does not exist.'); 
        } 
    } 
} 
