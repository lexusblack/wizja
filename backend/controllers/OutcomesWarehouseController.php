<?php

namespace backend\controllers;

use backend\components\Controller;
use common\components\filters\AccessControl;
use common\models\EventLog;
use common\models\EventGear;
use common\models\EventOuterGear;
use common\models\form\WarehouseSearch;
use common\models\GearCategory;
use common\models\PacklistGear;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OutcomesForCustomer;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use backend\models\OutcomesGearGeneral;
use common\models\BarCode;
use common\models\EventGearItem;
use common\models\Gear;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\OuterGear;
use common\models\RentGearItem;
use common\models\RentGear;
use common\models\User;
use Yii;
use common\models\OutcomesWarehouse;
use common\models\OutcomesWarehouseSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use kartik\mpdf\Pdf;
use yii\helpers\Inflector;
use common\models\Settings;

/**
 * OutcomesWarehouseController implements the CRUD actions for OutcomesWarehouse model.
 */
class OutcomesWarehouseController extends Controller
{
    protected $_gearDataProvider;
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class'=>AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['gearWarehouseOutcomes'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['create', 'create-start', 'create-outer'],
                        'roles' => ['eventRentsMagazin'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['pdf'],
                        'roles' => ['gearWarehouseOutcomesViewPdf'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['delete'],
                        'roles' => ['gearWarehouseOutcomesDelete'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['view'],
                        'roles' => ['gearWarehouseOutcomesView'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['gear-not-included-when-planning-event', 'get-gear-list', 'gear-list', 'get-gear', 'get-gear-group', 'get-gear-item', 'get-gear-item-outer', 'get-gear-no-items', 'get-gear-pics', 'get-all', 'check-gears', 'check-quantity'],
                        'roles' => ['@']
                    ]
                ]
            ],
        ];
    }

    /**
     * Lists all OutcomesWarehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'main-panel';
        $searchModel = new OutcomesWarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionGetAll()
    {
        $this->layout = 'main-panel';
        $outcomed = [];
        $outcomed1 = \common\helpers\ArrayHelper::map(\common\models\EventGearOutcomed::find()->where(['>', 'quantity', 0])->asArray()->all(), 'gear_id', 'gear_id');
        $outcomed2 = \common\helpers\ArrayHelper::map(\common\models\RentGearOutcomed::find()->where(['>', 'quantity', 0])->asArray()->all(), 'gear_id', 'gear_id');
        $outcomed = Gear::find()->where(['id'=>$outcomed1])->orWhere(['id'=>$outcomed2])->all();

        return $this->render('get-all', [
                'gears'=>$outcomed
                ]);
    }

    /**
     * Displays a single OutcomesWarehouse model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreateOuter($event = null, $rent = null, $c=null, $s=null, $s2=null, $activeModel=null, $q=null, $from_date=null, $to_date=null) {

        $this->_setDataProviders($activeModel, $c, $s,$s2,$q,$from_date, $to_date);

        return $this->render('create-outer', ['gearDataProvider'=>$this->_gearDataProvider, 'activeModel' => $activeModel, 'event'=> $event]);
    }

    protected function _setDataProviders($activeModel,$c, $s, $s2, $q=null, $from_date=null, $to_date=null)
    {
        //FIXME: Rozwiązać razem z menu kategorii
        $sub = $s2==null ? $s : $s2;
        $sub = $sub==null ? $c : $sub;
        $categoryIds = [];
        $ids = [];
        $tmpCat = GearCategory::findOne($sub);

        if ($tmpCat !== null)
        {
            $ids = $tmpCat->children()->column();
        }

        $categoryIds = array_merge([$sub], $ids);

        //Model
        $gearQuery = OuterGear::find()
            ->andWhere(['active'=>1])
            ->andFilterWhere([
                'category_id'=>$categoryIds,
            ])
            ->andFilterWhere(['like', 'name', $q]);

        $this->_gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
    }

    /**
     * Creates a new OutcomesWarehouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

        public function actionCheckGears($w, $type, $event_id)
    {
            $items = json_decode(Yii::$app->request->post('OutcomesWarehouse')['items']);
            $groups = json_decode(Yii::$app->request->post('OutcomesWarehouse')['groups']);
            $return = [];
            $gears = [];
            $return2 = [];
			foreach ($groups as $group => $value) {
				if ($value)
				{
					$g_items = GearItem::find()->where(['group_id'=>$group])->all();
					foreach ($g_items as $g_item)
					{
						$items[$g_item->id] = 1;
					}
				}
			}
            foreach ($items as $item => $value) {
            if ($value) {
                    $gear_item = GearItem::findOne($item);
                if ($gear_item->gear->no_items) {
                            $wq = \common\models\WarehouseQuantity::findOne(['warehouse_id'=>$w, 'gear_id'=>$gear_item->gear_id]);
                            $gears[$gear_item->gear_id] = $value;
                    if (!$wq) {
                                    $return[] = ['gear'=>$gear_item,'name'=>$gear_item->gear->name, 'missing'=>$value];
                            }else{
                                    }
                            
                    }else{
                        if (isset($gears[$gear_item->gear_id]))
                            $gears[$gear_item->gear_id] +=1;
                        else
                            $gears[$gear_item->gear_id] = 1;
                    if ($gear_item->warehouse_id != $w) {
                                $return[] = ['gear'=>$gear_item,'name'=>$gear_item->gear->name, 'missing'=>1];
                        }
                    }
            }
			}

        //sprawdzamy czy dany sprzęt był zarezerwowany i czy osoba może wydać ten sprzęt mimo braku rezerwacji
            foreach ($gears as $gear_id=>$quantity)
            {
                if ($type=='event')
                {
                    $total = \common\models\EventGearOutcomed::findOne(['packlist_id'=>$event_id, 'gear_id'=>$gear_id]);
                    $pg = \common\models\PacklistGear::findOne(['packlist_id'=>$event_id, 'gear_id'=>$gear_id]);
                    $gear = \common\models\Gear::findOne($gear_id);
                    if ($pg)
                    {
                        $booked = $pg->quantity;
                    }else{
                        $booked = 0;
                    }
                    if ($total)
                        $total = $total->quantity + $quantity;
                    else
                        $total = 0;

                    $conflict = \common\models\EventConflict::findOne(['packlist_gear_id'=>$packlist_gear->id, 'resolved'=>0]);
                    if ($conflict)
                        $cq = $conflict->quantity;
                    else
                        $cq = 0;
                    //sprawdzamy czy chcą wydać więccej niż zarezerwowane
                    if ($booked<$total)
                    {
                        //chcemy wydać ponad stan
                        //sprawdzamy uprawnienia
                        if (Yii::$app->user->can('gearWarehouseOutcomesAddUnplannedGear'))
                        {
                            if ($cq>0)
                            {
                                //jest konflikt - sprawdzamy czy ten sprzęt można wydać pomimo konfliktu
                                if (!$gear->conflict_outcome)
                                {
                                    $more = $total-$booked;
                                    $return2[] = ['gear'=>$gear, 'name'=>$gear->name, 'more'=>$more];
                                }else{
								}
                            }
                        }else{
                            $more = $total-$booked;
                            $return2[] = ['gear'=>$gear, 'name'=>$gear->name, 'more'=>$more];
                        }
                    }

                }
                if ($type=='rent')
                {
                    $total = \common\models\RentGearOutcomed::findOne(['rent_id'=>$event_id, 'gear_id'=>$gear_id]);
                    $pg = \common\models\RentGear::findOne(['rent_id'=>$event_id, 'gear_id'=>$gear_id]);
                    $gear = \common\models\Gear::findOne($gear_id);
                    if ($pg)
                    {
                        $booked = $pg->quantity;
                    }else{
                        $booked = 0;
                    }
                    if ($total)
                        $total = $total->quantity + $quantity;
                    else
                        $total = 0;
                    //sprawdzamy czy chcą wydać więccej niż zarezerwowane
                    if ($booked<$total)
                    {
                        //chcemy wydać ponad stan
                        //sprawdzamy uprawnienia
                        if (Yii::$app->user->can('gearWarehouseOutcomesAddUnplannedGear'))
                        {

                        }else{
                            $more = $total-$booked;
                            $return2[] = ['gear'=>$gear, 'name'=>$gear->name, 'more'=>$more];
                        }
                    }

                }                
            }



        if ((count($return))||(count($return2))) {
                    $error = 1;
            }else{
                    $error = 0;
            }
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['error'=>$error, 'gears'=>$return, 'unplanned'=>$return2];


    }
    public function actionCheckQuantity($w, $gear_id, $quantity)
    {
        $item = \common\models\GearItem::findOne($gear_id);
        $wq = \common\models\WarehouseQuantity::findOne(['warehouse_id'=>$w, 'gear_id'=>$item->gear_id]);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
        if ($wq->quantity<$quantity)
        {
            return ['success'=>0, 'quantity'=>$wq->quantity];
        }else{
            return ['success'=>1, 'quantity'=>$wq->quantity];
        }
    } 

    public function actionCreate($event = null, $rent = null, $customer = null, $gear = null, $outer_gear = null, $group_gear = null, $c=null, $s=null, $s2=null, $packlist_id=null, $warehouser_id=null)
    {
        $model = new OutcomesWarehouse();
        $modelsGear = [new OutcomesGearGeneral()];
        $model->customer_id = null;
        if ($event) {
            $model->event_type = 1;
            $model->event_id = $event;


        }
        if ($rent) {
            $model->event_type = 2;
            $model->rent_id = $rent;

        } 

        if (Yii::$app->request->post()) {

            $items = json_decode(Yii::$app->request->post('OutcomesWarehouse')['items']);
            $groups = json_decode(Yii::$app->request->post('OutcomesWarehouse')['groups']);
            
            $model->load(Yii::$app->request->post());

            $warehouse = \common\models\Warehouse::findOne(intval($model->warehouse_id));
            $items = $warehouse->checkItems($items);
            $groups = $warehouse->checkGroups($groups);
            $model->user = Yii::$app->getUser()->id;
            date_default_timezone_set(Yii::$app->params['timeZone']);
            $model->start_datetime = date('Y-m-d H:i:s');
            $model->warehouse_id = intval($model->warehouse_id);
            if ($model->save()) {

                $outcomed = [];
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                    $outcomes_for = new OutcomesForEvent();
                    $outcomes_for->event_id = $model->event_id;
                    $outcomes_for->outcome_id = $model->id;
                    $outcomes_for->packlist_id = $packlist_id;
                    $outcomes_for->save();
                }
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                    $outcomes_for = new OutcomesForRent();
                    $outcomes_for->rent_id = $model->rent_id;
                    $outcomes_for->outcome_id = $model->id;
                    $outcomes_for->save();
                }
                foreach ($groups as $gear_group => $value) {
                        if ($value)
                        {
                            $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>[1, 2]])->andWhere(['active'=>1])->all();
                            foreach ($gear_items as $gi)
                            {
                                $items[$gi->id]=1;
                            }
                        }
                    }
                $outcomed = [];
                $outcomed_gear = [];

                foreach ($items as $gear_item => $value) {
                    //wydajemy egzemplarze
                        if ($value)
                        {
                            $gearItem = GearItem::findOne($gear_item);
                            if ($gearItem)
                            {
                                $outcomed[] = $gearItem;
                                $outcomed_gear[$gearItem->gear_id] =  $gearItem->gear;
                                $event_id = $model->event_id;
                                if ($model->event_type==OutcomesWarehouse::EVENT_TYPE_RENT)
                                    $event_id = $model->rent_id;
                                $gear = new OutcomesGearOur();
                                $gear->outcome_id = $model->id;
                                $gear->gear_id = $gear_item;
                                $gear->gear_quantity = $value;
                                $gear->save();
                                $gearItem->makeOutcome($model->warehouse_id, $event_id, $packlist_id, $model->event_type, $value);
                            }
                        }

                    }

                    if ($packlist_id)
                        $packlist = \common\models\Packlist::findOne($packlist_id);

                    foreach ($outcomed_gear as $gear) {
                        //sprawdzamy rezerwacje sprzętu ilosciowo
                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                            $packlist_gear = \common\models\PacklistGear::findOne(['packlist_id'=>$packlist_id, 'gear_id'=>$gear->id]);
                            $total = \common\models\EventGearOutcomed::findOne(['packlist_id'=>$packlist_id, 'gear_id'=>$gear->id]);
                            if (!$packlist_gear)
                            {
                                $rg = new PacklistGear;
                                $rg->gear_id = $gear->id;
                                $rg->start_time = $model->start_datetime;
                                $rg->end_time = $packlist->end_time;
                                $rg->packlist_id = $packlist_id;
                                $rg->quantity = $total->quantity;
                                $rg->save();
                            }else{
                                if ($total->quantity>$packlist_gear->quantity)
                                {
                                    $packlist_gear->quantity = $total->quantity;
                                }
                                if ($model->start_datetime<$packlist_gear->start_time)
                                {
                                    $packlist_gear->start_time = $model->start_datetime;
                                }
                                $packlist_gear->save();
                            }
                        }
                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                            $rent_gear = \common\models\RentGear::findOne(['rent_id'=>$model->rent_id, 'gear_id'=>$gear->id]);
                            $total = \common\models\RentGearOutcomed::findOne(['rent_id'=>$model->rent_id, 'gear_id'=>$gear->id]);
                            if (!$rent_gear)
                            {
                                $rg = new RentGear;
                                $rg->gear_id = $gear->id;
                                $rg->rent_id = $model->rent_id;
                                $rg->start_time = $model->start_datetime;
                                $rg->end_time = $outcomes_for->rent->end_time;
                                $rg->quantity = $total->quantity;
                                $rg->save();
                            }else{
                                if ($total->quantity>$rent_gear->quantity)
                                {
                                    $rent_gear->quantity = $total->quantity;
                                }
                                if ($model->start_datetime<$rent_gear->start_time)
                                {
                                    $rent_gear->start_time = $model->start_datetime;
                                }
                                $rent_gear->save();
                            }

                        }
                    }
                    foreach ($outcomed as $gear_item) {
                        //rezerwujemy egzemplarze
                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                                    $gear = EventGearItem::findOne(['event_id'=>$model->event_id, 'gear_item_id'=>$gear_item->id]);
                                    if (!$gear)
                                    {
                                        $gear = new EventGearItem();
                                        $gear->event_id = $model->event_id;
                                        $gear->gear_item_id = $gear_item->id;
                                        $gear->packlist_id = $packlist_id;
                                        $gear->save();
                                    }else{
                                        $gear->packlist_id = $packlist_id;
                                        $gear->save();
                                    }
                        }
                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                    $gear = RentGearItem::findOne(['rent_id'=>$model->rent_id, 'gear_item_id'=>$gear_item->id]);
                                    if (!$gear)
                                    {
                                        $gear = new RentGearItem();
                                        $gear->rent_id = $model->rent_id;
                                        $gear->gear_item_id = $gear_item->id;
                                        $gear->save();
                                    }

                        }
                    }
                    return $this->redirect(['view', 'id' => $model->id]);
            }else{
                            echo var_dump($model->errors);
                    exit;
            }

        }
        else {
            if (!$warehouse_id){
                $warehouse = \common\models\Warehouse::find()->where(['type'=>1])->orderBy(['position'=>SORT_ASC])->one();
                if ($warehouse)
                    $model->warehouse_id = $warehouse->id;
            }else{
                $model->warehouse_id = $warehouse_id;
            }

            return $this->render('create', [
                'model' => $model,
                'modelsGear' => (empty($modelsGear)) ? [new OutcomesGearGeneral()] : $modelsGear,
                'event' => $event,
                'rent' => $rent,
                'gear' => $gear,
                'packlist_id'=>$packlist_id,
                'customer' => $customer,
                'outer_gear' => $outer_gear,
                'group_gear' => $group_gear,
                'warehouse'=>$warehouse,
            ]);
        }
    }

        /**
     * Creates a new OutcomesWarehouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
        /*
    public function actionCreate($event = null, $rent = null, $customer = null, $gear = null, $outer_gear = null, $group_gear = null, $c=null, $s=null, $s2=null, $packlist_id=null)
    {
        $model = new OutcomesWarehouse();
        $modelsGear = [new OutcomesGearGeneral()];

        if (Yii::$app->request->post()) {
            $items = json_decode(Yii::$app->request->post('OutcomesWarehouse')['items']);
            $groups = json_decode(Yii::$app->request->post('OutcomesWarehouse')['groups']);

            $model->load(Yii::$app->request->post());
            $model->user = Yii::$app->getUser()->id;
            date_default_timezone_set(Yii::$app->params['timeZone']);
            $model->start_datetime = date('Y-m-d H:i:s');

            if ($model->save()) {

                // zapisujemy rodzaj eventu dla którego jest wydanie z magazynu
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_NONE) {
                    $outcomes_for = new OutcomesForCustomer();
                    $outcomes_for->customer_id = $model->customer_id;
                    $outcomes_for->outcome_id = $model->id;
                    $outcomes_for->save();
                }
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                    $outcomes_for = new OutcomesForEvent();
                    $outcomes_for->event_id = $model->event_id;
                    $outcomes_for->outcome_id = $model->id;
                    $outcomes_for->packlist_id = $packlist_id;
                    $outcomes_for->save();
                }
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                    $outcomes_for = new OutcomesForRent();
                    $outcomes_for->rent_id = $model->rent_id;
                    $outcomes_for->outcome_id = $model->id;
                    $outcomes_for->save();
                }
                    foreach ($items as $gear_item => $value) {
                        if ($value)
                        {
                            $gearItem = GearItem::findOne($gear_item);
                        if ($gearItem){
                        $gear = new OutcomesGearOur();
                        $gear->outcome_id = $model->id;
                        $gear->gear_id = $gear_item;
                        $gear->gear_quantity = 1;
                        
                        if ($gearItem->gear->no_items == 1) {
                            $gear->gear_quantity = $value;
                            $gearItem->outcomed+=$value;
                        }else{
                            $gearItem->outcomed = 1;
                        }
                        $gear->save();
                        $gearItem->save();

                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT || $model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                            if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                $planned = RentGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['rent_id' => $rent])->andWhere(['planned' => 1])->count();
                            }
                            else {
                                $planned = EventGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['event_id' => $event])->andWhere(['planned' => 1])->count();
                            }
                            if ($planned == 0) {
                                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                    $gear = new RentGearItem();
                                    $gear->rent_id = $rent;
                                    $gear->gear_item_id = $gear_item;
                                    $gear->planned = 0;
                                    $gear->save();
                                    $r = \common\models\Rent::findOne($rent);
                                    $rg = RentGear::find()->where(['gear_id'=>$gearItem->gear_id, 'rent_id'=>$rent])->one();
                                    if (!$rg)
                                    {
                                        $rg = new RentGear;
                                        $rg->gear_id = $gearItem->gear_id;
                                        $rg->rent_id = $rent;
                                        $rg->start_time = date("Y-m-d H:i:s");
                                        $rg->end_time = $r->end_time;
                                        if ($gearItem->gear->no_items == 1) {
                                            $rg->quantity = $value;
                                        }else{
                                            $rg->quantity = 1;
                                        }
                                        $rg->save();
                                        if ($gearItem->gear->type==3)
                                        {
                                            //zbieramy ze stanu magazynowego
                                            $gearItem->gear->quantity -=$rg->quantity;
                                            $gearItem->gear->save();
                                        }
                                    }else{
                                                                                if ($gearItem->gear->no_items == 1) {
                                            if ($rg->quantity<$value)
                                            {
                                                $rg->quantity = $value;
                                                $rg->save();
                                            }
                                        }else{
                                            $rg->recalculateQuantity();
                                        }
                                    }
                                }
                                else {
                                    $gear = new EventGearItem();
                                    $gear->event_id = $event;
                                    $gear->gear_item_id = $gear_item;
                                    $gear->planned = 0;
                                    $gear->packlist_id = $packlist_id;
                                    $gear->save();
                                    $p = \common\models\Packlist::findOne($packlist_id);
                                    $rg = PacklistGear::find()->where(['gear_id'=>$gearItem->gear_id, 'packlist_id'=>$packlist_id])->one();
                                    if (!$rg)
                                    {
                                        $rg = new PacklistGear;
                                        $rg->gear_id = $gearItem->gear_id;
                                        $rg->start_time = date("Y-m-d H:i:s");
                                        $rg->edn_time = $p->edn_time;
                                        $rg->packlist_id = $packlist_id;
                                        if ($gearItem->gear->no_items == 1) {
                                            $rg->quantity = $value;
                                        }else{
                                            $rg->quantity = 1;
                                        }
                                        $rg->save();
                                        if ($gearItem->gear->type==3)
                                        {
                                            //zbieramy ze stanu magazynowego
                                            $gearItem->gear->quantity -=$rg->quantity;
                                            $gearItem->gear->save();
                                        } 
                                    }else{
                                        if ($gearItem->gear->no_items == 1) {
                                            if ($rg->quantity<$value)
                                            {
                                                $rg->quantity = $value;
                                                $rg->save();
                                            }
                                        }else{
                                            $rg->recalculateQuantity();
                                        }
                                        
                                    }
                                }
                            }
                        }
                        }                            
                        }
                        

                        
                    }
                
                    foreach ($items as $gear_item => $value) {
                        if ($value)
                        {
                        $gearitem = GearItem::findOne($gear_item);
                        if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                            $rg = RentGear::find()->where(['gear_id' => $gearitem->gear_id])->andWhere(['rent_id' => $rent])->one();
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                                    $rgi = RentGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['rent_id' => $rent])->one();
                                    $rgi->start_time = date('Y-m-d H').":00:00";   
                                    $rgi->save();
                            }

                        }else{
                            $rg = PacklistGear::find()->where(['gear_id' => $gearitem->gear_id])->andWhere(['packlist_id' => $packlist_id])->one();
                            if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                            {
                                    $rg->start_time = date('Y-m-d H').":00:00";
                                    $rg->save();
                                    $rgi = EventGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['event_id' => $event])->one();
                                    $rgi->start_time = date('Y-m-d H').":00:00";   
                                    $rgi->save();
                            }
                        }
                    }
                    }
                

                
                    foreach ($groups as $gear_group => $value) {
                        if ($value)
                        {


                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();
                        if ($gear_items) {
                            foreach ($gear_items as $gear_item) {
                                $gear = new OutcomesGearOur();
                                $gear->outcome_id = $model->id;
                                $gear->gear_id = $gear_item->id;
                                $gear->gear_quantity = 1;
                                $gear->save();
                                $gear_item->outcomed = 1;
                                $gear_item->save();
                            }

                            if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT || $model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                    $planned = RentGearItem::find()->where(['gear_item_id' => $gear_items[0]->id])->andWhere(['rent_id' => $rent])->andWhere(['planned' => 1])->count();
                                }
                                else {
                                    $planned = EventGearItem::find()->where(['gear_item_id' => $gear_items[0]->id])->andWhere(['event_id' => $event])->andWhere(['planned' => 1])->count();
                                }
                                if ($planned == 0) {
                                    if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                        foreach ($gear_items as $gear_item) {
                                            $gear = new RentGearItem();
                                            $gear->rent_id = $rent;
                                            $gear->gear_item_id = $gear_item->id;
                                            $gear->planned = 0;
                                            $gear->save();
                                            $rg = RentGear::find()->where(['gear_id'=>$gear_item->gear_id, 'rent_id'=>$rent])->one();
                                            if (!$rg)
                                            {
                                                $rg = new RentGear;
                                                $rg->gear_id = $gear_item->gear_id;
                                                $rg->rent_id = $rent;
                                                if ($gear_item->gear->no_items) {
                                                    $rg->quantity = $value;
                                                }else{
                                                    $rg->quantity = 1;
                                                }
                                                $rg->save();
                                                 
                                            }
                                        }
                                    }
                                    else {
                                        foreach ($gear_items as $gear_item) {
                                            $gear = new EventGearItem();
                                            $gear->event_id = $event;
                                            $gear->gear_item_id = $gear_item->id;
                                            $gear->planned = 0;
                                            $gear->save();
                                            $rg = PacklistGear::find()->where(['gear_id'=>$gear_item->gear_id, 'packlist_id'=>$packlist_id])->one();
                                            if (!$rg)
                                            {
                                                $rg = new PacklistGear;
                                                $rg->gear_id = $gear_item->gear_id;
                                                $rg->packlist_id = $packlist_id;
                                                if ($gear_item->gear->no_items) {
                                                    $rg->quantity = $value;
                                                }else{
                                                    $rg->quantity = 1;
                                                }
                                                $rg->save();
                                                 
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                        
                    }
                
                
                    foreach ($groups as $gear_group => $value) {
                        if ($value)
                        {


                        $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();
                        foreach ($gear_items as $gearitem)
                        {
                                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                                    $rg = RentGear::find()->where(['gear_id' => $gearitem->gear_id])->andWhere(['rent_id' => $rent])->one();
                                    if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                                    {
                                            $rg->start_time = date('Y-m-d H').":00:00";
                                            $rg->save();
                                            $rgi = RentGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['rent_id' => $rent])->one();
                                            $rgi->start_time = date('Y-m-d H').":00:00";   
                                            $rgi->save();
                                    }

                                }else{
                                    $rg = EventGear::find()->where(['gear_id' => $gearitem->gear_id])->andWhere(['event_id' => $event])->one();
                                    if (date('Y-m-d H:i:s')<substr($rg->start_time, 0, 10)." 00:00:00")
                                    {
                                            $rg->start_time = date('Y-m-d H').":00:00";
                                            $rg->save();
                                            $rgi = EventGearItem::find()->where(['gear_item_id' => $gear_item])->andWhere(['event_id' => $event])->one();
                                            $rgi->start_time = date('Y-m-d H').":00:00";   
                                            $rgi->save();
                                    }
                                }
                        }
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
            else {
                var_dump($model->errors);
                die;
            }
        }
        else {

            $warehouse = \common\models\Warehouse::find()->one();
            if ($warehouse)
                $model->warehouse_id = $warehouse->id;

            return $this->render('create', [
                'model' => $model,
                'modelsGear' => (empty($modelsGear)) ? [new OutcomesGearGeneral()] : $modelsGear,
                'event' => $event,
                'rent' => $rent,
                'gear' => $gear,
                'packlist_id'=>$packlist_id,
                'customer' => $customer,
                'outer_gear' => $outer_gear,
                'group_gear' => $group_gear,
                'warehouse'=>$warehouse,
            ]);
        }
    }
*/
    /**
     * Deletes an existing OutcomesWarehouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //sprawdzamy czy jest możliwość usunięcia wydania (tylko jeśli wszystkie sprzęty są dalej na tym wydarzeniu)
        $model = $this->findModel($id);
        $event = \common\models\OutcomesForEvent::find()->where(['outcome_id' => $model->id])->one();
        $rent = \common\models\OutcomesForRent::find()->where(['outcome_id' => $model->id])->one();
        $possible = true;
        if ($event)
        {
                $gears = \common\models\OutcomesGearOur::find()->where(['outcome_id' => $model->id])->all();
                foreach ($gears as $gear)
                {
                    if ($gear->gear->gear->no_items)
                    {
                            $ego = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'event_id'=>$event->event_id, 'packlist_id'=>$event->packlist_id])->one();
                            if ($ego)
                            {
                                if ($ego->quantity<$gear->gear_quantity){
                                        $possible = false;
                                    }
                            }else{
                                $possible = false;
                            }

                    }else{
                        if ($gear->gear->event_id!=$event->event_id)
                        {
                            $possible = false;
                        }
                    }
                }
        }
        if ($rent)
        {
                $gears = \common\models\OutcomesGearOur::find()->where(['outcome_id' => $model->id])->all();
                foreach ($gears as $gear)
                {
                    if ($gear->gear->gear->no_items)
                    {
                            $ego = \common\models\RentGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'rent_id'=>$rent->rent_id])->one();
                            if ($ego)
                            {
                                if ($ego->quantity<$gear->gear_quantity){
                                        $possible = false;
                                    }
                            }else{
                                $possible = false;
                            }

                    }else{
                        if ($gear->gear->rent_id!=$rent->rent_id)
                        {
                            $possible = false;
                        }
                    }
                }
        }
        if ($possible){
            if ($event)
            {
                    $gears = \common\models\OutcomesGearOur::find()->where(['outcome_id' => $model->id])->all();
                    foreach ($gears as $gear)
                    {
                        if ($gear->gear->gear->no_items)
                        {
                                $ego = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'event_id'=>$event->event_id, 'packlist_id'=>$event->packlist_id])->one();
                                    $q = $gear->gear_quantity;
                                    $wq = \common\models\WarehouseQuantity::findOne(['warehouse_id'=>$model->warehouse_id, 'gear_id'=>$gear->gear->gear_id]);
                                    $wq->quantity +=$gear->gear_quantity;
                                    $wq->save();
                                    $ego->quantity -=$gear->gear_quantity;
                                    $ego->save();
                                    $gear->delete();
                                

                        }else{
                            $gear->gear->event_id = null;
                            $gear->gear->packlist_id = null;
                            $gear->gear->outcomed = 0;
                            $gear->gear->warehouse_id = $model->warehouse_id;
                            $gear->gear->save();
                            $ego = \common\models\EventGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'event_id'=>$event->event_id, 'packlist_id'=>$event->packlist_id])->one();
                            if ($ego)
                                {
                                    $ego->quantity -=1;
                                    $ego->save();
                                    
                                }
                            $gear->delete();
                        }
                    }
            }  
            if ($rent)
            {
                    $gears = \common\models\OutcomesGearOur::find()->where(['outcome_id' => $model->id])->all();
                    foreach ($gears as $gear)
                    {
                        if ($gear->gear->gear->no_items)
                        {
                                $ego = \common\models\RentGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'rent_id'=>$rent->rent_id])->one();
                                    $q = $gear->gear_quantity;
                                    $wq = \common\models\WarehouseQuantity::findOne(['warehouse_id'=>$model->warehouse_id, 'gear_id'=>$gear->gear->gear_id]);
                                    $wq->quantity +=$gear->gear_quantity;
                                    $wq->save();
                                    $ego->quantity -=$gear->gear_quantity;
                                    $ego->save();
                                    $gear->delete();
                                

                        }else{
                            $gear->gear->rent_id = null;
                            $gear->gear->packlist_id = null;
                            $gear->gear->outcomed = 0;
                            $gear->gear->warehouse_id = $model->warehouse_id;
                            $gear->gear->save();
                            $ego = \common\models\RentGearOutcomed::find()->where(['gear_id'=>$gear->gear->gear_id, 'rent_id'=>$rent->rent_id])->one();
                            if ($ego)
                                {
                                    $ego->quantity -=1;
                                    $ego->save();
                                    
                                }
                            $gear->delete();
                        }
                    }
            }
            $model->delete();           
        }
        else{
            echo "Nie można";
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the OutcomesWarehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutcomesWarehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutcomesWarehouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    // @return lista wszystkich sprzętów, które możemy wydać z magazynu - do szukania w przypadku wydawania/przyjmowania sprzętu
    public function actionGearList($q, $w) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $gearItem = false;
        $gearGroup = false;
        // rozszyfrowujemy barcody i qrcody
        $c = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        if ($c->own_ean)
        {
            $gearItem = GearItem::find()->where(['code'=>$q])->andWhere(['active'=>1])->one();
            $gearGroup = GearGroup::find()->where(['code'=>$q])->one();
        }else{
        if (strlen($q) == 13) {
            $id = (int)substr($q, 4, 9);

            // mamy do czynienia z casem (gear_group)
            if (substr($q, 0, 2) == BarCode::ITEMS_GROUP) {
                $gearGroup = GearGroup::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
            }

            // mamy do czynienia ze sprzetem z naszego magazynu (gear)
            else if (substr($q, 0, 2) == BarCode::SINGEL_PRODUCT) {
                if (substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                    $gearItem = GearItem::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();

                } }

             else if (substr($q, 0, 2) == BarCode::MODEL) {
                return ['error' => Yii::t('app', 'Zeskanowano kod modelu sprzętu. Zeskanuj konkretny egzemplarz')];
            }
        }
        }
        if ($gearItem)
        {
            if ($gearItem->gear->no_items){
                                //sprawdzamy czy jest w magazynie
                            $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$gearItem->gear_id, 'warehouse_id'=>$w]);
                            if ($wq->quantity>=1)
                            {
                                return ['ok' => true, 'item' => $gearItem->id, 'name' => $gearItem->gear->name, 'no_items'=>$gearItem->gear->no_items];
                            }else{
                                return ['error' => Yii::t('app', 'Brak sprzętu w tym magazynie: ')];
                            }

                        }else{
                        if ($gearItem->warehouse_id==$w) {
                            return ['ok' => true, 'item' => $gearItem->id, 'name' => $gearItem->gear->name, 'no_items'=>$gearItem->gear->no_items];
                        }
                        else {
                            if ($gearItem->outcomed)
                            {
                                    if ($gearItem->event_id)
                                    {
                                            $e = \common\models\Event::findOne($gearItem->event_id);
                                            return ['error' => Yii::t('app', 'Sprzęt wydany na: ').$e->name];
                                    }
                                    if ($gearItem->rent_id)
                                    {
                                            $r = \common\models\Rent::findOne($gearItem->rent_id);
                                            return ['error' => Yii::t('app', 'Sprzęt wydany na: ').$r->name];
                                    }
                                    
                            }else{
                                    $warehouse = \common\models\Warehouse::findOne($gearItem->warehouse_id);
                                    if ($warehouse)
                                    {
                                        return ['error' => Yii::t('app', 'Sprzęt znajduje się w magazynie: ').$warehouse->name];
                                    }else{
                                         return ['error' => Yii::t('app', 'Sprzęt nieprzypisany do żadnego magazynu.')];
                                    }
                            }
                            
                        }
                    }
                    
        }
        if ($gearGroup)
        {
                    $available = $gearGroup->numberOfAvailable();
                    if ($available > 0) {
                        return ['ok' => true, 'group' => $gearGroup->id, 'name' => $gearGroup->name];
                    }
                    else {
                        return ['error' => Yii::t('app', 'Ten sprzęt nie ma już dostępnych egzemplarzy!')];
                    }
        }
        // koniec barcodow

        return ['error' => Yii::t('app', 'Nie znaleziono sprzętu o tym kodzie')];
    }

    public function actionGetGearPics($gear_id, $id, $type, $warehouse_id, $total=null)
    {
        $gearItems = GearItem::find()->where(['gear_id'=>$gear_id])->andWhere(['active'=>true])->andWhere(['status'=>[1,2]])->andWhere(['is', 'group_id', null])->andWhere(['warehouse_id'=>$warehouse_id])->all();
        $gearGItems = GearItem::find()->where(['gear_id'=>$gear_id])->andWhere(['active'=>true])->andWhere(['status'=>[1,2]])->andWhere(['is not', 'group_id', null])->andWhere(['warehouse_id'=>$warehouse_id])->all();
        $gearService = GearItem::find()->where(['gear_id'=>$gear_id])->andWhere(['active'=>true])->andWhere(['status' => 10])->andWhere(['warehouse_id'=>$warehouse_id])->all();
                $ids = [];
        foreach ( $gearGItems as $gi)
        {
            $ids[] = $gi->group_id;
        }
        $groups = GearGroup::find()->where(['IN', 'id', $ids])->all();
        return $this->renderPartial('get-gear-pics', [
            'gear_id' => $gear_id,
            'items'=>$gearItems,
            'groups'=>$groups,
            'itemsService' => $gearService,
            'total'=>$total
        ]);
        exit;
    }

    // @return tablica sprzętu, który wydajemy, a nie był zaplanowany dla danego eventu
    public function actionGearNotIncludedWhenPlanningEvent() {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $event_id = Yii::$app->request->post('OutcomesWarehouse')['event_id'];
        $items = json_decode(Yii::$app->request->post('OutcomesWarehouse')['items']);
        $groups = json_decode(Yii::$app->request->post('OutcomesWarehouse')['groups']);

        // nasz sprzęt (gear item), który planowaliśmy zabrać
        $planned_gears = EventGear::find()->where(['event_id' => $event_id])->all();
        $planned_outer_gears = EventOuterGear::find()->where(['event_id' => $event_id])->all();
        $planned_outer_ids = [];

        $our_gear = [];
            foreach ($items as $id => $value) {

                    if ($value)
                    {
                        $item = GearItem::find()->where(['id'=>$id])->one();
                        
                        if ($item){
                            if ($item->gear->no_items==1)
                            {
                                $our_gear[$item->gear_id] = $value;
                            }else{
                            if (isset($our_gear[$item->gear_id]))
                                $our_gear[$item->gear_id] += 1;
                            else
                                $our_gear[$item->gear_id] = 1;                        
                            }
                        }
                        
                    }
                    

            }
        foreach ($groups as $id => $value) {

                    
                    if ($value)
                    {
                    $gear_items = GearItem::find()->where(['group_id'=>$id])->andWhere(['status'=>1])->andWhere(['active'=>1])->all();
                    foreach ($gear_items as $item)
                    {
                        if (isset($our_gear[$item->gear_id]))
                            $our_gear[$item->gear_id] += 1;
                        else
                            $our_gear[$item->gear_id] = 1;                        
                    } 
                    }
                             

            }
        


        $not_planned_gear = [];
        foreach ($our_gear as $gear_id => $gear_quantity) {
            $eg = EventGear::find()->where(['gear_id'=>$gear_id])->andWhere(['event_id'=>$event_id])->one();
            if (!$eg){
                $not_planned_gear[] = ['name' => Gear::find()->where(['id' => $gear_id])->one()->name, 'quantity' => $gear_quantity];
            }
            else
            {
                if ($eg->quantity<$gear_quantity)
                {
                    $not_planned_gear[] = ['name' => Gear::find()->where(['id' => $gear_id])->one()->name, 'quantity' => $gear_quantity-$eg->quantity];
                }
            }
        }


        return  $not_planned_gear;
    }

    public function actionPdf2($id) {
        return  OutcomesWarehouse::find()->where(['id' => $id])->one()->generatePdf();
    }


    public function actionPdf($id, $values = true)
    {
        $pdf = $this->preparePDF($id, $values);
        return $pdf->render();
    }

    protected function preparePDF($id, $values=true){
        
        $model = OutcomesWarehouse::find()->where(['id' => $id])->one();
        $user = User::find()->where(['id' => $model->user])->one();
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 

        $content = $this->renderPartial('pdf', [
            'model' =>  $model,
            'settings' => $settings,
            'user'=>$user,
            'values'=>$values
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $model,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $model,
            'settings' => $settings,
            'user'=>$user
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
                'marginTop' => 45,
                'marginBottom' => 30,
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
                'options' => ['title' => "WZ-".$model->id],
                'filename' => "WZ_".$model->id.'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0        ];
        return $pdf;
    }

    public function actionCreateStart() {
        return $this->render('select_event');

    }

    public function actionGetGearList($model_id) {
        // ustawiamy cookies
        $gear_items = GearItem::find()->where(['gear_id' => $model_id])->andWhere(['active'=>1])->andWhere(['status'=>1])->all();
        $gears = [];
        $groups = [];
        $group_done = [];
        foreach ($gear_items as $gear_item) {
            if ($gear_item->group_id != null && !in_array($gear_item->group_id, $group_done)) {
                $group_done[] = $gear_item->group_id;
                $group = GearGroup::find()->where(['id'=>$gear_item->group_id])->one();
                if ($group) {
                    $available = $group->numberOfAvailable();
                    if ($available > 0) {
                        $groups[] = $group->id;
                    }
                }
            }
            if ($gear_item->group_id == null) {
                if ($gear_item->isAvailableForOutcome()) {
                    $gears[] = $gear_item->id;
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [$gears, $groups];
    }

    public function actionGetGearItem($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gear = GearItem::find()->where(['id'=>$gear_id])->andWhere(['active'=>1])->asArray()->one();
        if ($gear) {
            $gearM = GearItem::find()->where(['id'=>$gear_id])->one();
            $gear['qrcode'] = $gearM->getBarCodeValue();
            $gear['no_items'] = $gearM->gear->no_items;
        }else{
            $gear = [];
        }
        return $gear;
    }

    public function actionGetGearItemOuter($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gear = OuterGear::find()->where(['id'=>$gear_id])->asArray()->one();
        $gear['qrcode'] = OuterGear::find()->where(['id'=>$gear['id']])->one()->getBarCodeValue();
        return $gear;
    }

    public function actionGetGear($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Gear::find()->where(['id'=>$gear_id])->asArray()->one();
    }

    public function actionGetGearGroup($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $group = GearGroup::find()->where(['id'=>$gear_id])->andWhere(['active'=>1])->asArray()->one();
        $group['items'] = [];
        $group['gear_ids'] = [];
        foreach (GearItem::find()->where(['group_id'=>$gear_id])->andWhere(['active'=>1])->andWhere(['status'=>[1, 2]])->all() as $gearItem) {
            $gear = ArrayHelper::toArray($gearItem);
            $gear['qrcode'] = $gearItem->getBarCodeValue();
            $group['items'][] = $gear;
            if (!in_array($gearItem->gear_id, $group['gear_ids'])) {
                $group['gear_ids'][] = $gearItem->gear_id;
            }
        }
        return $group;
    }

    public function actionGetGearNoItems($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return GearItem::findOne($gear_id)->gear;
    }
}
