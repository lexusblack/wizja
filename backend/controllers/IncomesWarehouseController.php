<?php

namespace backend\controllers;

use backend\components\Controller;
use common\components\filters\AccessControl;
use common\models\BarCode;
use common\models\form\WarehouseSearch;
use common\models\Gear;
use common\models\PacklistGear;
use common\models\GearCategory;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\IncomesForCustomer;
use common\models\IncomesForEvent;
use common\models\IncomesForRent;
use common\models\IncomesGearOur;
use common\models\IncomesGearOuter;
use common\models\OutcomesForCustomer;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\OutcomesGearOur;
use common\models\OutcomesWarehouse;
use backend\models\OutcomesGearGeneral;
use common\models\OuterGear;
use common\models\User;
use common\models\EventGear;
use common\models\EventGearItem;
use common\models\RentGear;
use common\models\RentGearItem;
use Yii;
use common\models\IncomesWarehouse;
use common\models\IncomesWarehouseSearch;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use kartik\mpdf\Pdf;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;
use common\models\Settings;
/**
 * IncomesWarehouseController implements the CRUD actions for IncomesWarehouse model.
 */
class IncomesWarehouseController extends Controller
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
                        'actions' => ['index', 'gear-list', 'check-quantity'],
                        'roles' => ['gearWarehouseIncomes'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['create', 'create-start'],
                        'roles' => ['eventRentsMagazin'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['pdf'],
                        'roles' => ['gearWarehouseIncomesViewPdf'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['delete'],
                        'roles' => ['gearWarehouseIncomesDelete'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['view'],
                        'roles' => ['gearWarehouseIncomesView'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all IncomesWarehouse models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IncomesWarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IncomesWarehouse model.
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

        return $this->render('create-outer', ['gearDataProvider'=>$this->_gearDataProvider, 'activeModel' => $activeModel, 'event'=>$event, 'rent'=>$rent]);
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
     * Creates a new IncomesWarehouse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($event = null, $rent = null, $customer = null, $gear = null, $outer_gear = null, $group_gear = null, $c=null, $s=null, $s2=null, $onlyEvent = false, $packlist_id=null, $warehouse_id=null)
    {

        $model = new IncomesWarehouse();
        $modelsGear = [new OutcomesGearGeneral()];

        if (Yii::$app->request->post()) {
            $post_items = json_decode(Yii::$app->request->post('IncomesWarehouse')['items']);
            $post_groups = json_decode(Yii::$app->request->post('IncomesWarehouse')['groups']);
            $model->load(Yii::$app->request->post());
            $model->user = Yii::$app->getUser()->id;
            date_default_timezone_set(Yii::$app->params['timeZone']);
            $model->datetime = date('Y-m-d H-i-s');
            $warehouse = \common\models\Warehouse::findOne(intval($model->warehouse_id));
            if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT)
            {
                $event = \common\models\Event::findOne($event);
                $post_items = $event->checkItems($post_items, $packlist_id);
                $post_groups = $event->checkGroups($post_groups, $packlist_id);
            }
            if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                $rent = \common\models\Rent::findOne($rent);
                $post_items = $rent->checkItems($post_items, $packlist_id);
                $post_groups = $rent->checkGroups($post_groups, $packlist_id);
            }
            
                $items = [];
            if ($model->save()) {
                // zapisujemy rodzaj eventu dla którego jest wydanie z magazynu
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_NONE) {
                    $outcomes_for = new IncomesForCustomer();
                    $outcomes_for->customer_id = $model->customer_id;
                    $outcomes_for->income_id = $model->id;
                    $outcomes_for->save();
                }
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                    $outcomes_for = new IncomesForEvent();
                    $outcomes_for->event_id = $model->event_id;
                    $outcomes_for->income_id = $model->id;
                    $outcomes_for->packlist_id = $packlist_id;
                    $outcomes_for->save();
                    $event_id = $model->event_id;
                }
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                    $outcomes_for = new IncomesForRent();
                    $outcomes_for->rent_id = $model->rent_id;
                    $outcomes_for->income_id = $model->id;
                    $outcomes_for->save();
                    $event_id = $model->rent_id;
                }


                
                    foreach ($post_items as $gear_item => $value) {
                        if ($value)
                        {
                            $gear = new IncomesGearOur();
                            $gear->income_id = $model->id;
                            $gear->gear_id = $gear_item;
                            $gear->quantity = $value;
                            $gear->save();
                            $gearItem = GearItem::findOne($gear_item);
                            $gearItem->makeIncome($model->warehouse_id, $event_id, $packlist_id, $model->event_type, $value);
                           
                            $items[] = $gear_item;                           
                        }


                    }
                
                    foreach ($post_groups as $gear_group => $value) {
                        if ($value)
                        {
                                $gear_items = GearItem::find()->where(['group_id'=>$gear_group])->all();
                                if ($gear_items) {
                                    foreach ($gear_items as $gear_item) {
                                            $gear = new IncomesGearOur();
                                            $gear->income_id = $model->id;
                                            $gear->gear_id = $gear_item->id;
                                            $gear->quantity = 1;
                                            $gear->save();
                                $gearItem = GearItem::findOne($gear_item);
                                if ($gearItem->gear->no_items == 1) {
                                    if ($value<$gearItem->outcomed)
                                        $gearItem->outcomed-=$value;
                                    else
                                        $gearItem->outcomed = 0;
                                }else{
                                    $gearItem->outcomed = 0;
                                    $gearItem->event_id = null;
                                    $gearItem->rent_id = null;
                                    $gearItem->packlist_id = null;
                                    $gearItem->warehouse_id = $model->warehouse_id;
                                }
                                $gearItem->save();
                                             $items[] = $gear_item->id;
                                    }
                                }       
                        }
                    }
                
                date_default_timezone_set(Yii::$app->params['timeZone']);
                $now = date('Y-m-d H:i');
                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_RENT) {
                        $gears = RentGearItem::find()->where(['in', 'gear_item_id', $items])->andWhere(['rent_id'=>$model->rent_id])->all();
                        foreach ($gears as $gear)
                        {
                                if ($model->shorten)
                                {
                                    if ($gear->end_time>$now)
                                    {
                                        $gear->end_time = $now;
                                        $gear->save();
                                    }
                                }

                        }
                        $g_ids = ArrayHelper::map(GearItem::find()->where(['in', 'id', $items])->asArray()->all(), 'gear__id', 'gear_id');
                        $eventgears = RentGear::find()->where(['gear_id'=>$g_ids])->andWhere(['rent_id'=>$model->rent_id])->all();
                        foreach ($eventgears as $eventgear)
                        {
                                if ($model->shorten)
                                {
                                        if ($eventgear->end_time>$now)
                                        {
                                                $eventgear->end_time = $now;
                                                $eventgear->save();                                             
                                        }
                                }
                           
                        }

                    }

                if ($model->event_type == OutcomesWarehouse::EVENT_TYPE_EVENT) {
                        $gears = EventGearItem::find()->where(['in', 'gear_item_id', $items])->andWhere(['event_id'=>$model->event_id])->all();
                        foreach ($gears as $gear)
                        {
                                if ($model->shorten)
                                {
                                    if ($gear->end_time>$now)
                                    {
                                        $gear->end_time = $now;
                                        $gear->save();
                                    }
                                }
                        }
                        $g_ids = ArrayHelper::map(GearItem::find()->where(['in', 'id', $items])->asArray()->all(), 'gear_id', 'gear_id');
                        $eventgears = PacklistGear::find()->where(['in', 'gear_id', $g_ids])->andWhere(['packlist_id'=>$packlist_id])->all();
                        foreach ($eventgears as $eventgear)
                        {
                                if ($model->shorten)
                                {
                                        if ($eventgear->end_time>$now)
                                        {
                                                $eventgear->end_time = $now;
                                                $eventgear->save();                                             
                                        }
                                }
                           
                        }

                    }

            }
            else {
                var_dump($model->errors);
                die(Yii::t('app', "Model nie przeszedł walidacji"));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            if ($event)
            {
                $onlyEvent=1;
                if (!$packlist_id)
                {
                    $p = \common\models\Packlist::find()->where(['event_id'=>$event, 'main'=>1])->one();
                    $packlist_id = $p->id;
                }
            }
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
                'customer' => $customer,
                'outer_gear' => $outer_gear,
                'group_gear' => $group_gear,
                'onlyEvent'=>$onlyEvent,
                'packlist_id'=>$packlist_id
            ]);
        }
    }

    /**
     * Updates an existing IncomesWarehouse model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing IncomesWarehouse model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

        public function actionCheckQuantity($event_id, $packlist_id, $type, $gear_id, $quantity)
    {
        $item = \common\models\GearItem::findOne($gear_id);
        if ($type==1)
            $wq = \common\models\EventGearOutcomed::findOne(['event_id'=>$event_id, 'packlist_id'=>$packlist_id, 'gear_id'=>$item->gear_id]);
        else
            $wq = \common\models\RentGearOutcomed::findOne(['rent_id'=>$event_id,  'gear_id'=>$item->gear_id]);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
        if ($wq->quantity<$quantity)
        {
            return ['success'=>0, 'quantity'=>$wq->quantity];
        }else{
            return ['success'=>1, 'quantity'=>$wq->quantity];
        }
    } 
    public function actionDelete($id)
    {
        // usuwamy informacje dla kogo jest wydanie z magazynu
       /* $rent = IncomesForRent::find()->where(['income_id' => $id])->one();
        $customer = IncomesForCustomer::find()->where(['income_id' => $id])->one();
        $event = IncomesForEvent::find()->where(['income_id' => $id])->one();
        if ($rent) {
            $rent->delete();
        }
        if ($customer) {
            $customer->delete();
        }
        if ($event) {
            $event->delete();
        }

        // usuwamy wydania sprzetów
        $gears_our = IncomesGearOur::find()->where(['income_id' => $id])->all();
        $gears_outer = IncomesGearOuter::find()->where(['income_id' => $id])->all();
        foreach ($gears_our as $gear) {
            if ($gear->gear->gear->no_items==1)
            {
                $gear->gear->outcomed = $gear->gear->outcomed+$gear->quantity;
                $gear->gear->save();
            }else{
                $gear->gear->outcomed = 1;
                $gear->gear->save();
            }
            $gear->delete();
        }
        foreach ($gears_outer as $gear) {
            $gear->delete();
        }

        $this->findModel($id)->delete();*/

        return $this->redirect(['index']);
    }

    /**
     * Finds the IncomesWarehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IncomesWarehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IncomesWarehouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionPdf2($id) {
        return  IncomesWarehouse::find()->where(['id' => $id])->one()->generatePdf();
    }

    public function actionPdf($id)
    {
        $pdf = $this->preparePDF($id);
        return $pdf->render();
    }

    protected function preparePDF($id){
        
        $model = IncomesWarehouse::find()->where(['id' => $id])->one();
        $user = User::find()->where(['id' => $model->user])->one();
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 

        $content = $this->renderPartial('pdf', [
            'model' =>  $model,
            'settings' => $settings,
            'user'=>$user
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
                'options' => ['title' => "PZ-".$model->id],
                'filename' => "PZ_".$model->id.'.pdf',
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
    public function actionGearList($q, $event_id=null, $rent_id=null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $gearItem = false;
        $gearGroup = false;
        $c = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        if ($c->own_ean)
        {
            $gearItem = GearItem::find()->where(['code'=>$q])->andWhere(['active'=>1])->one();
            $gearGroup = GearGroup::find()->where(['code'=>$q])->one();
        }else{
        // rozszyfrowujemy barcody i qrcody
        if (strlen($q) == 13) {
            $id = (int)substr($q, 4, 9);

            // mamy do czynienia z casem (gear_group)
            if (substr($q, 0, 2) == BarCode::ITEMS_GROUP) {
                $gearGroup = GearGroup::find()->where(['id'=>$id])->one();
                
            }

            // mamy do czynienia ze sprzetem z naszego magazynu (gear)
            else if (substr($q, 0, 2) == BarCode::SINGEL_PRODUCT) {
                if (substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                    $gear = GearItem::find()->where(['id'=>$id])->one();
                    
                }

                // mamy do czynienia ze sprzetem z zewnetrznego magazynu (outer_gear)
                else if (substr($q, 2, 2) == BarCode::OUTER_WAREHOUSE) {
                    $gear = OuterGear::find()->where(['id'=>$id])->one();
                    if ($gear) {
                        $available = $gear->numberOfAvailable();
                        if ($available < $gear->quantity) {
                            if (isset($_COOKIE['checkbox-item-outer-id'][$id])) {
                                if ($gear->quantity-$available <= $_COOKIE['checkbox-item-outer-id'][$id]) {
                                    return ['error' => Yii::t('app', 'Aż taka ilość sprzętu nie zostałą wydana z magazynu!')];
                                }
                                setcookie("checkbox-item-outer-id[".$id."]", $_COOKIE['checkbox-item-outer-id'][$id]+1, time()+3600*24, '/');
                            }
                            else {
                                setcookie("checkbox-item-outer-id[" . $id . "]", 1, time() + 3600 * 24, '/');
                            }
                            return ['ok' => true, 'outer' => $id, 'name' => $gear->name];
                        }
                        else {
                            return ['error' => Yii::t('app', 'Ten sprzęt nie został wydany z magazynu!')];
                        }
                    }
                }
            }
        }
        }
        if ($gearGroup) {
                    $gear_out = 0;
                    foreach ($gearGroup->gearItems as $item) {
                        $gear_out += $item->outcomed;
                    }
                    if ($gear_out > $gear_in) {
                        return ['ok' => true, 'group' => $gearGroup->id, 'name' => $gearGroup->name];
                    }
                    else {
                        return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany z magazynu!')];
                    }
                }
        if ($gearItem)
        {
            if ($gearItem->gear->no_items)
                    {
                        if ($event_id)
                        {
                                $ego = \common\models\EventGearOutcomed::findOne(['gear_id'=>$gearItem->gear_id, 'event_id'=>$event_id]);
                                if ($ego)
                                {
                                        if ($ego->quantity>0)
                                        {
                                                return ['ok' => true, 'item' => $gearItem->id, 'name' => $gearItem->gear->name, 'no_items'=>1];
                                        }else{
                                            return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany na to wydarzeniu lub wrócił już do magazynu!')];
                                        }
                                }else{
                                    return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany na to wydarzeniu lub wrócił już do magazynu!')];
                                }
                        }else{
                                $ego = \common\models\RentGearOutcomed::findOne(['gear_id'=>$gearItem->gear_id, 'rent_id'=>$rent_id]);
                                if ($ego)
                                {
                                        if ($ego->quantity>0)
                                        {
                                                return ['ok' => true, 'item' => $gearItem->id, 'name' => $gearItem->gear->name, 'no_items'=>1];
                                        }else{
                                            return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany na to wydarzeniu lub wrócił już do magazynu!')];
                                        }
                                }else{
                                    return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany na to wydarzeniu lub wrócił już do magazynu!')];
                                }
                        }

                    }else{
                        if (($gearItem->event_id==$event_id)&&($gearItem->rent_id==$rent_id)) {
                            return ['ok' => true, 'item' => $gearItem->id, 'name' => $gearItem->gear->name, 'no_items'=>0];
                        }
                        else {
                            return ['error' => Yii::t('app', 'Ten sprzęt nie był wydany na to wydarzeniu lub wrócił już do magazynu!')];
                        }
                    
                        }
        }
        // koniec barcodow

        return ['error' => Yii::t('app', 'Nie znaleziono sprzętu o tym kodzie')];
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
                $group = GearGroup::find()->where(['id'=>$gear_item->group_id])->andWhere(['active'=>1])->one();
                if ($group && $group->numberOfAvailable() == 0) {
                    $groups[] = $group->id;
                }
            }
            if ($gear_item->group_id == null) {
                if (!$gear_item->isAvailableForOutcome()) {
                    $gears[] = $gear_item->id;
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [$gears, $groups];
    }
}
