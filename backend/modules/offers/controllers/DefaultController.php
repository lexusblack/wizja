<?php

namespace backend\modules\offers\controllers;

use backend\modules\offers\models\OfferExtraItem;
use backend\modules\offers\models\OfferForm;
use backend\modules\permission\models\BasePermission;
use common\models\SettingAttachment;
use common\models\SettingAttachmentSearch;
use Yii;
use common\models\EventLog;
use common\models\RentLog;
use common\models\Event;
use common\models\Project;
use common\models\Rent;
use common\models\Offer;
use common\models\Gear;
use common\models\OuterGearModel;
use common\models\OfferSearch;
use common\models\OfferVehicle;
use common\models\Settings;
use common\models\OfferUserSkills;
use common\models\Skill;
use common\models\User;
use common\models\UserEventRole;
use common\models\OfferGear;
use common\models\OfferOuterGear;
use common\models\OfferGearItem;
use common\models\OfferSetting;
use common\models\OfferRole;
use common\models\OfferCustomItems;
use common\models\OfferExtraCost;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\helpers\Url;
use yii\web\Response;
use common\components\filters\AccessControl;

/**
 * DefaultController implements the CRUD actions for Offer model.
 */
class DefaultController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = '@backend/themes/e4e/layouts/main-panel';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['manage-gear-connected','manage-gear-outer-connected','manage-gear', 'index', 'show-notes', 'assign-vehicle', 'add-vehicle', 'save-vehicle', 'save2', 'copy-vehicles', 'count-km', 'save-schedule', 'schedule-order', 'add-schedule', 'update-schedule', 'delete-schedule','add-item','remove-item', 'update-offers', 'block', 'unblock', 'change-statut'],
                    'roles' => ['menuOffers'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['pdf2', 'delete-role', 'save-order'],
                    'roles' => ['eventsEventEditEyeOffer'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'create-from-event', 'create-from-rent'],
                    'roles' => ['menuOffersAdd', 'eventsEventEditEyeOfferAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['assign-to-event', 'assign-to-project', 'offer-project'],
                    'roles' => ['eventsEventEditEyeOfferImport'],

                ],                
                [
                    'allow' => true,
                    'actions' => ['assign-to-rent', 'offer-rent'],
                    'roles' => ['eventRentsOffer'],

                ],
                [
                'allow' => true,
                'actions' => ['offer-event'],
                'roles' => ['eventsEventEditEyeOfferDelete', 'eventsEventEditEyeOfferAdd'],
                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'send-mail', 'pdf', 'excel'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('menuOffersView'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isManger('menuOffersView');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('menuOffersDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isManger('menuOffersDelete');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'assign-gear', 'offer-custom-items', 'assign-skills', 'event', 'rent', 'delete-extra-item', 'edit-extra-item',   'delete-custom-field', 'delete-vehicle', 'manage-vehicle', 'change-vehicle-type', 'status', 'add-to-rent', 'add-to-events', 'change-status', 'change-cost', 'change-budget', 'visible-item', 'rules', 'add-note'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('menuOffersEdit'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isManger('menuOffersEdit');
                        //return true;
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['duplicate'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('menuOffersViewDuplicate'.BasePermission::SUFFIX[BasePermission::ALL]) || Yii::$app->user->can('eventsEventEditEyeOfferAdd')) {
                            return true;
                        }
                        return $this->isManger('menuOffersViewDuplicate');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['event'],
                    'roles' => ['eventsEventAdd']
                ]
            ],
        ];
        $behaviors['verbs2'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'delete-from-event' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionUpdateOffers()
    {
        $ids = \common\helpers\ArrayHelper::map(\common\models\OfferSchedule::find()->asArray()->all(), 'offer_id', 'offer_id');
        $offers = Offer::find()->where(['NOT IN', 'id', $ids])->orderBy(['id'=>SORT_DESC])->limit(20)->all();
        foreach ($offers as $offer)
        {
            $event = new \common\models\OfferSchedule();
            $event->offer_id = $offer->id;
            $event->name = Yii::t('app', 'Event');
            $event->prefix = "E";
            $event->start_time = $offer->event_start;
            $event->end_time = $offer->event_end;
            $event->position = 2;
            $event->book_gears = 1;
            $event->is_required = 0;
            $event->save();
            $pakowanie = new \common\models\OfferSchedule();
            $pakowanie->offer_id = $offer->id;
            $pakowanie->name = Yii::t('app', 'Pakowanie');
            $pakowanie->prefix = "P";
            $pakowanie->start_time = $offer->packing_start;
            $pakowanie->end_time = $offer->packing_end;
            $pakowanie->position = 0;
            $pakowanie->book_gears = 1;
            $pakowanie->is_required = 0;
            $pakowanie->save();
            $montaz = new \common\models\OfferSchedule();
            $montaz->offer_id = $offer->id;
            $montaz->name = Yii::t('app', 'Montaż');
            $montaz->prefix = "M";
            $montaz->start_time = $offer->montage_start;
            $montaz->end_time = $offer->montage_end;
            $montaz->position = 1;
            $montaz->book_gears = 1;
            $montaz->is_required = 0;
            $montaz->save();
            $demontaz = new \common\models\OfferSchedule();
            $demontaz->offer_id = $offer->id;
            $demontaz->name = Yii::t('app', 'Demontaż');
            $demontaz->prefix = "D";
            $demontaz->start_time = $offer->disassembly_start;
            $demontaz->end_time = $offer->disassembly_end;
            $demontaz->position = 3;
            $demontaz->book_gears = 1;
            $demontaz->is_required = 0;
            $demontaz->save();
            $offer_id = $offer->id;
            \common\models\OfferRole::updateAll(['time_type' => $pakowanie->id], ['time_type'=>1, 'offer_id'=>$offer_id]);
            \common\models\OfferRole::updateAll(['time_type' => $montaz->id], ['time_type'=>2, 'offer_id'=>$offer_id]);
            \common\models\OfferRole::updateAll(['time_type' => $event->id], ['time_type'=>3, 'offer_id'=>$offer_id]);
            \common\models\OfferRole::updateAll(['time_type' => $demontaz->id], ['time_type'=>4, 'offer_id'=>$offer_id]);
            \common\models\OfferVehicle::updateAll(['type' => $pakowanie->id], ['type'=>1, 'offer_id'=>$offer_id]);
            \common\models\OfferVehicle::updateAll(['type' => $montaz->id], ['type'=>2, 'offer_id'=>$offer_id]);
            \common\models\OfferVehicle::updateAll(['type' => $event->id], ['type'=>3, 'offer_id'=>$offer_id]);
            \common\models\OfferVehicle::updateAll(['type' => $demontaz->id], ['type'=>4, 'offer_id'=>$offer_id]);
            OfferExtraItem::updateAll(['time_type' => $pakowanie->id], ['time_type'=>1, 'offer_id'=>$offer_id]);
            OfferExtraItem::updateAll(['time_type' => $montaz->id], ['time_type'=>2, 'offer_id'=>$offer_id]);
            OfferExtraItem::updateAll(['time_type' => $event->id], ['time_type'=>3, 'offer_id'=>$offer_id]);
            OfferExtraItem::updateAll(['time_type' => $demontaz->id], ['time_type'=>4, 'offer_id'=>$offer_id]);
        }
                Yii::$app->response->format = Response::FORMAT_JSON;
        if ($offers)
        {
            return ['success'=>1, 'offers'=>$offers];
        }else{
            return ['success'=>0];
        }
        exit;
    }

    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        $model->blocked = 1;
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);  
    }
    public function actionUnblock($id)
    {
        $model = $this->findModel($id);
        $model->blocked = 0;
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);  
    }
    public function actionCountKm($offer_id)
    {
        $model = $this->findModel($offer_id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['distance'=> $model->getDistance()];
    }

    public function actionScheduleOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = \common\models\OfferSchedule::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionUpdateSchedule($id)
    {
        $model = \common\models\OfferSchedule::findOne($id);

        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add_schedule', [
            'model'=>$model,
            'offer_id'=>$model->offer_id,
        ]);
        }
    }

    public function actionDeleteSchedule($id)
    {
        $model = \common\models\OfferSchedule::findOne($id);
        $model->delete();
        $model->offer->updateSchedule();
        //usunąć wszystkie role i samochody podpięte pod tę pozycję ??
        return $this->redirect(['view', 'id' => $model->offer_id]);  
    }

    public function actionAddSchedule($id)
    {
        $model = new \common\models\OfferSchedule();
        $model->offer_id = $id;
        $model->position = \common\models\OfferSchedule::find()->where(['offer_id'=>$id])->count();

        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add_schedule', [
            'model'=>$model,
            'offer_id'=>$id,
        ]);
        }
    }

    public function actionSaveSchedule()
    {
        $schedule = Yii::$app->request->post('OfferSchedule');
        if (isset($schedule['id']))
        {
            $s = \common\models\OfferSchedule::findOne($schedule['id']);
            $s->load(Yii::$app->request->post());
            $s->save();
            exit;
        }else{
            exit;
        }
    }

    public function actionSave2($offer_id)
    {
        $model = OfferVehicle::findOne(Yii::$app->request->post('vehicle_id'));
        $vehiclePrice = \common\models\VehiclePrice::find()->where(['id'=>Yii::$app->request->post("group_id")])->one();
                $model->price = $vehiclePrice->price;
                $model->cost = $vehiclePrice->cost;
                $model->unit = $vehiclePrice->unit;
                $model->vehicle_price_id = $vehiclePrice->id;
        $model->save();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;

    }


        public function actionCopyVehicles($time_type, $offer_id)
    {
        $schedule = \common\models\OfferSchedule::findOne($time_type);
        $position = $schedule->position-1;
        $schedule2 = \common\models\OfferSchedule::find()->where(['position'=>$position, 'offer_id'=>$offer_id])->one();
        $models = OfferVehicle::find()->where(['type'=>$schedule2->id])->andWhere(['offer_id'=>$offer_id])->all();
        foreach ($models as $or)
        {
            $orsi = new OfferVehicle();
            $orsi->vehicle_id = $or->vehicle_id;
            $orsi->quantity = $or->quantity;
            $orsi->distance = $or->distance;
            $orsi->type = $time_type;
            $orsi->unit = $or->unit;
            $orsi->cost = $or->cost;
            $orsi->price = $or->price;
            $orsi->description = $or->description;
            $orsi->vehicle_price_id = $or->vehicle_price_id;
            $orsi->offer_id = $offer_id;
            $orsi->save();
        }    
        return $this->redirect(['assign-vehicle', 'id' => $offer_id]);   
    }

    public function actionSaveVehicle($new_group=false, $new_vehicle=false)
    {
        //\Yii::$app->response->format = Response::FORMAT_JSON;
        $model = OfferVehicle::findOne(Yii::$app->request->post("OfferVehicle")['id']);
        $currency = $model->offer->priceGroup->currency;
        if ($model->load(Yii::$app->request->post()))
        {
            if ($new_group)
            {
                $vehiclePrice = \common\models\VehiclePrice::find()->where(['id'=>Yii::$app->request->post("OfferVehicle")['vehicle_price_id']])->one();
                $model->price = $vehiclePrice->price;
                $model->cost = $vehiclePrice->cost;
                $model->unit = $vehiclePrice->unit;
                $model->vehicle_price_id = $vehiclePrice->id;
            }
             if ($new_vehicle)
            {
                $vehiclePrice = \common\models\VehiclePrice::find()->where(['vehicle_model_id'=>$model->vehicle_id, 'currency'=>$currency])->orderBy(['default'=>SORT_DESC])->one();
                if ($vehiclePrice)
                {
                    $model->price = $vehiclePrice->price;
                    $model->cost = $vehiclePrice->cost;
                    $model->unit = $vehiclePrice->unit;
                    $model->vehicle_price_id = $vehiclePrice->id;
                }else{
                     $model->vehicle_price_id = null;
                }
                        $model->save();
                                return $this->renderAjax('add-form', [
                                    'currency' => $model->offer->priceGroup->currency,
                                    'time_type'=>$model->type,
                                    'vehicle'=>$model,
            '                               j'=>''
                                        ]);

            }           
            $model->save();
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;
         
    }

    public function actionAssignVehicle($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        return $this->render('assign-vehicle', [
            'model' => $model
        ]);

    }

    public function actionAddVehicle($time_type, $offer_id)
    {
        $v = new OfferVehicle();
        $v->offer_id = $offer_id;
        $offer = Offer::findOne($offer_id);
        $currency = $offer->priceGroup->currency;
        $v->type = $time_type;
        $v->quantity = 1;
        $v->distance = 1;
        $ids = \common\helpers\ArrayHelper::map(OfferVehicle::find()->where(['offer_id'=>$offer_id, 'type'=>$time_type])->asArray()->all(), 'vehicle_id', 'vehicle_id');
        $vehicle = \common\models\VehicleModel::find()->where(['NOT IN', 'id', $ids])->andWhere(['active'=>1])->one();
        if (!$vehicle)
            $vehicle = \common\models\VehicleModel::find()->one();
        $v->vehicle_id = $vehicle->id;
        $vehiclePrice = \common\models\VehiclePrice::find()->where(['vehicle_model_id'=>$vehicle->id, 'currency'=>$currency])->orderBy(['default'=>SORT_DESC])->one();
        if ($vehiclePrice)
        {
            $v->vehicle_price_id = $vehiclePrice->id;
            $v->price = $vehiclePrice->price;
            $v->cost = $vehiclePrice->cost;
            $v->unit = $vehiclePrice->unit;
        }
        $v->save();
        return $this->renderAjax('add-form', [
            'currency' => $currency,
            'time_type'=>$time_type,
            'vehicle'=>$v,
            'j'=>count($ids)
        ]); 
    }

    public function actionSaveOrder()
    {
        foreach (Yii::$app->request->post("items") as $key=>$val)
        {
            $item = explode("_", $val);
            if ($item[1]=="gear")
            {
                $og = OfferGear::findOne($item[0]);
                $og->position = $key;
                $og->save();
            }
            if ($item[1]=="outerGear")
            {
                $og = OfferOuterGear::findOne($item[0]);
                $og->position = $key;
                $og->save();
            }
            if ($item[1]=="extraGear")
            {
                $og = OfferExtraItem::findOne($item[0]);
                $og->position = $key;
                $og->save();
            }         
        }
    }

    private function isManger($text) {
        $offer = $this->findModel(Yii::$app->request->get('id'));
        if (!$offer)
            $offer = $this->findModel(Yii::$app->request->get('offer_id'));
        if (Yii::$app->user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            if ($offer->manager_id == Yii::$app->user->id) {
                return true;
            }
            if ($offer->created_by == Yii::$app->user->id) {
                return true;
            }

        }
        return false;
    }

    public function actionShowNotes($id)
    {
        $notes = \common\models\CustomerNote::find()->where(['offer_id'=>$id])->all();
        return $this->renderAjax('show-notes', [
            'notes'=>$notes
        ]);
    }

    public function actionAddNote($id)
    {
        $event = $this->findModel($id);
        $model = new \common\models\CustomerNote();
        $model->offer_id = $id;
        $model->customer_id = $event->customer_id;
        $model->contact_id = $event->contact_id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->datetime = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('_customer_note_form', [
            'model'=>$model,
            'offer_id'=>$id,
            'ajax'=>true
        ]);
        }

    }

    public function actionVisibleItem($type, $offerId, $itemId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($type=='gear')
        {
            $og = OfferGear::findOne($itemId);
            if ($og->visible)
            {
                $og->visible=0;
            }else{
                $og->visible=1;
            }
            $og->save();
            if ($og->type==1)
            {
                $ogs = OfferGear::find()->where(['offer_gear_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
                $ogs = OfferOuterGear::find()->where(['offer_gear_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
            }
            return (['visible'=>$og->visible, 'type'=>$type, 'item'=>$itemId]);
        }
        if ($type=='outerGear')
        {
            $og = OfferOuterGear::findOne($itemId);
            if ($og->visible)
            {
                $og->visible=0;
            }else{
                $og->visible=1;
            }
            $og->save();
            if ($og->type==1)
            {
                $ogs = OfferGear::find()->where(['offer_outer_gear_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
                $ogs = OfferOuterGear::find()->where(['offer_outer_gear_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
            }
            return (['visible'=>$og->visible, 'type'=>$type, 'item'=>$itemId]);
        }
        if ($type=='extraGear')
        {
            $og = OfferExtraItem::findOne($itemId);
            if ($og->visible)
            {
                $og->visible=0;
            }else{
                $og->visible=1;
            }
            $og->save();
            if ($og->type==1)
            {
                $ogs = OfferGear::find()->where(['offer_group_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
                $ogs = OfferOuterGear::find()->where(['offer_group_id'=>$og->id])->all();
                foreach ($ogs as $ogear)
                {
                    $ogear->visible = $og->visible;
                    $ogear->save();
                }
            }
            return (['visible'=>$og->visible, 'type'=>$type, 'item'=>$itemId]);
        }
    }


    /**
     * Lists all Offer models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null, $st=2)
    {
        $searchModel = new OfferSearch();
        $params = Yii::$app->request->queryParams;
        $date = new \DateTime();
        if($y==null)
        {
                $y = $date->format('Y');
            
        }
        if ($m==null)
        {
            $m = 0;
        }
        

        
        $searchModel->year = $y;
        $searchModel->month = $m;   
        $searchModel->searchType = $st;         

        $dataProvider = $searchModel->search($params);
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date1 = clone $date;
        $date2 = clone $date;
        $date1->sub($dateInterval);
        $date2->add($dateInterval);
        $prev = [
            'y'=>$date1->format('Y'),
            'm'=>$date1->format('n'),
        ];
        $next = [
            'y'=>$date2->format('Y'),
            'm'=>$date2->format('n'),
        ];

        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('menuOffers'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Offer::find()->where(['or', ['manager_id' => Yii::$app->user->id], ['created_by' => Yii::$app->user->id]]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'next' => $next,
            'prev' => $prev,
            'm'=>$m,
            'y'=>$y,
            'st'=>$st
        ]);
    }
    /**
     * Displays a single Offer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
         $view = $this->prepareView($id);
         if (($view['model']->blocked)||($view['model']->offerStatut->blocked))
         {
            //oferta zablokowana do edycji wyświetlamy pdf
            return $this->redirect(['pdf', 'id'=>$id]);
         }

        Url::remember();

       
        $view['model']->countValues();
        $attchments = new SettingAttachmentSearch();
        $attchments->type = SettingAttachment::TYPE_OFFER;
        $settingAttachmentDataProvider = $attchments->search([]);
        $settingAttachmentDataProvider->pagination = false;
        $settingAttachmentDataProvider->sort = false;

        $offerForm = new OfferForm([
            'offer'=>$view['model'],
        ]);

        if (Yii::$app->request->isPost)
        {
            $offerForm->loadAndSave();
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Zapisano'));
            //$view = $this->prepareView($id);
            //$view['model']->countValues();
            return$this->redirect(['view', 'id'=>$id]);
        }



        return $this->render('view2', [
            'model' =>  $view['model'],
            'gear_list' => $view['gear_list'],
            'vehicles' => $view['vehicles'],
            'settings' => $view['settings'],
            'skills' => $view['skills'],
            'users' => $view['users'],
            'settingAttachmentDataProvider' => $settingAttachmentDataProvider,
            'offerForm' => $offerForm,
        ]);
    }
    /**
     * Displays a single Offer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView2($id)
    {
        Url::remember();

        $view = $this->prepareView($id);

        $attchments = new SettingAttachmentSearch();
        $attchments->type = SettingAttachment::TYPE_OFFER;
        $settingAttachmentDataProvider = $attchments->search([]);
        $settingAttachmentDataProvider->pagination = false;
        $settingAttachmentDataProvider->sort = false;

        $offerForm = new OfferForm([
            'offer'=>$view['model'],
        ]);

        if (Yii::$app->request->isPost)
        {
            $offerForm->loadAndSave();
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Zapisano'));
            return $this->refresh();
        }



        return $this->render('view', [
            'model' =>  $view['model'],
            'gear_list' => $view['gear_list'],
            'vehicles' => $view['vehicles'],
            'settings' => $view['settings'],
            'skills' => $view['skills'],
            'users' => $view['users'],
            'settingAttachmentDataProvider' => $settingAttachmentDataProvider,
            'offerForm' => $offerForm,
        ]);
    }

    public function actionAssignToEvent($event_id)
    {
    	$event = Event::findOne($event_id);
    	if(!$event){
    		throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
    	}
        $query = Offer::find()->where(['event_id' => $event_id])->orWhere(['event_id' => null]);
        $searchModel = new OfferSearch();


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('assign-to-event', [
            'dataProvider' => $dataProvider,
            'event' => $event,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionAssignToProject($project_id)
    {
        $event = Project::findOne($project_id);
        if(!$event){
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
        $query = Offer::find()->where(['project_id' => $project_id])->orWhere(['project_id' => null]);
        $searchModel = new OfferSearch();


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('assign-to-project', [
            'dataProvider' => $dataProvider,
            'event' => $event,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionAssignToRent($rent_id)
    {
        $event = Rent::findOne($rent_id);
        if(!$event){
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
        $query = Offer::find()->where(['rent_id' => $rent_id])->orWhere(['rent_id' => null]);
        $searchModel = new OfferSearch();


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('assign-to-rent', [
            'dataProvider' => $dataProvider,
            'rent' => $event,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionPdf($id)
    {
        $view = $this->prepareView($id);
        Yii::$app->language = $view['model']->language; 
        $pdf = $this->preparePDF($id,$view);
        return $pdf->render();
    }

    public function actionPdf2($id)
    {
        $view = $this->prepareView($id);
        Yii::$app->language = $view['model']->language; 
        $view['prices'] = true;
        $pdf = $this->preparePDF($id,$view);
        return $pdf->render();
    }

    protected function prepareExcelData($view)
    {
        
        
        $border_row =[];
        $formatter = Yii::$app->formatter;
        $offerForm = new OfferForm([
            'offer'=>$view['model'],
        ]);
        $model = $view['model'];
        $data[] = ["", "", "", "",  Yii::t('app', "Nazwa projektu:"), $model->name];
        $data[] = ["", "", "", "",  Yii::t('app', "Numer:"), $model->id];
        $data[] = ["", "", "", "",  Yii::t('app', "Termin:"), $model->term_from."do ".$model->term_to];
        $data[] = ["", "", "", "",  Yii::t('app', "Data oferty:"),         $model->offer_date];
        $data[] = [ Yii::t('app', "Zamawiajacy:")];
        $data[] = [$model->customer->name, "", "","",  Yii::t('app', "Kierownik projektu:"), $model->manager->first_name." ".$model->manager->last_name];
        $data[] = [$model->customer->address." ".$model->customer->zip." ".$model->customer->city, "", "", "", "",  Yii::t('app', "tel:").$model->manager->phone];
        $data[] = [ Yii::t('app', "tel:").$model->customer->phone, "", "", "", "",  Yii::t('app', "e-mail:").$model->manager->email];
        $data[] = [ Yii::t('app', "e-mail:").$model->customer->email];
        $data[] = [""];

        $data[] = [ Yii::t('app', "Miejsce i adres:")];
        if (isset($model->location))
        {
        $data[] = [$model->location->name];
        $data[] = [$model->location->address];
        $data[] = [$model->location->city." ".$model->location->zip];
        $row = 16;
        $bold_row = [5,6,11,16];
        $underline_row = [11, 16];
    }else{
        $data[] = [$model->address];
        $row = 14;
        $bold_row = [5,6,11,14];
        $underline_row = [11, 14];
    }
        $data[] = [""];
        $data[] = [ Yii::t('app', "Harmonogram:")];
        foreach ($model->offerSchedules as $schedule){
                if(isset($schedule->start_time)){
                    $data[] =[$schedule->name, $schedule->start_time, $schedule->end_time ];
                $row++;

        } } 
        /*$attrs = [
                        'packing',
                        'montage',
                        'practice',
                        'readiness',
                        'event',
                        'disassembly'
                    ];
        
        $labels = $model->attributeLabels();
        foreach ($attrs as $key => $attr) {
            if(isset($model->{$attr.'_start'}) && isset($model->{$attr.'_end'})){
                $data[] =[$labels[$attr.'DateRange'], $model->{$attr.'_start'}, $model->{$attr.'_end'} ];
                $row++;
        }}*/

        $total_summ_of_cats = 0;
        $summ_of_one_cat = 0;
        $summ_of_v = 0;
        $summ_of_weight = 0;
        $summ_of_power_consumption = 0;
        foreach ($offerForm->allGears as $categoryName => $items):
                $summ_of_one_cat = 0;
                $data[] = [""];
                $row++;
                $data[] = [$categoryName];
                $row++;
                $bold_row[]=$row;
                $underline_row[]=$row;
                $data[] = [ Yii::t('app', "Nazwa"),  Yii::t('app', "Cena"),  Yii::t('app', "Liczba"),  Yii::t('app', "Rabat"),  Yii::t('app', "Dni pracy"),  Yii::t('app', "Razem netto")];
                $row++;
                $bold_row[]=$row;
                $border_row[] =$row;
                foreach ($items as  $gearId => $dat):
                    if ($dat['type'] != 'outerGear') {
                        if ($dat['type'] != 'extraGear') {
                            $baseIndex = 'gearModels[' . $gearId . ']';
                            $data[] = [\common\models\Gear::getTranslateName($dat['gear_id'], $view['model']->language, $dat['name']), $formatter->asCurrency($dat['price']), $dat['quantity'], $dat['discount'], $dat['duration'], $formatter->asCurrency($dat['value'])];
                            $row++;
                            $border_row[] =$row;
                            $summ_of_one_cat += $dat['value'];
                            $summ_of_v += $dat['volume'] * $dat['quantity'];
                            $summ_of_weight += $dat['weight'] * $dat['quantity'];
                            $summ_of_power_consumption += $dat['power_consumption'] * $dat['quantity'];
                            $offerGears = OfferGear::find()->where(['offer_gear_id'=>$dat['id']])->all();
                            foreach ($offerGears as $og)
                            {

                                $data[] = [\common\models\Gear::getTranslateName($og->gear_id, $view['model']->language, $og->gear->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                $row++;
                                $border_row[] =$row;
                                $summ_of_one_cat += $og->getValue();
                            }
                            $offerGears = OfferOuterGear::find()->where(['offer_gear_id'=>$dat['id']])->all();
                            foreach ($offerGears as $og)
                            {
                                $data[] = [\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $view['model']->language, $og->outerGearModel->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                $row++;
                                $border_row[] =$row;
                                $summ_of_one_cat += $og->getValue();
                            }
                        }
                        else {
                            $summ_of_one_cat += $dat['value'];
                            $data[] = [$dat['name'], $formatter->asCurrency($dat['price']), $dat['quantity'], $dat['discount'], $dat['duration'], $formatter->asCurrency($dat['value'])];
                            $row++;
                            $border_row[] =$row;
                            $offerGears = OfferGear::find()->where(['offer_group_id'=>$dat['id']])->all();
                            foreach ($offerGears as $og)
                            {
                                $data[] = [\common\models\Gear::getTranslateName($og->gear_id, $view['model']->language, $og->gear->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                $row++;
                                $border_row[] =$row;
                                $summ_of_one_cat += $og->getValue();
                            }
                            $offerGears = OfferOuterGear::find()->where(['offer_group_id'=>$dat['id']])->all();
                            foreach ($offerGears as $og)
                            {
                                $data[] = [\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $view['model']->language, $og->outerGearModel->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                $row++;
                                $border_row[] =$row;
                                $summ_of_one_cat += $og->getValue();
                            }
                        }
                    }
                endforeach;
                if (isset($offerForm->outerGear[$categoryName])) {
                    foreach ($offerForm->outerGear[$categoryName] as $outer) {
                                $data[] = [\common\models\OuterGearModel::getTranslateName($outer['gear_id'], $view['model']->language, $outer['name']), $formatter->asCurrency($outer['price']), $outer['quantity'], $outer['discount'], $outer['duration'], $formatter->asCurrency($outer['value'])];
                                $row++;
                                $border_row[] =$row;
                                $summ_of_one_cat += $outer['value'];
                                $summ_of_v += $outer['volume'] * $outer['quantity'];
                                $summ_of_weight += $outer['weight'] * $outer['quantity'];
                                $summ_of_power_consumption += $outer['power_consumption'] * $outer['quantity'];
                                $offerGears = OfferGear::find()->where(['offer_group_id'=>$outer['id']])->all();
                                foreach ($offerGears as $og)
                                {
                                    $data[] = [\common\models\Gear::getTranslateName($og->gear_id, $view['model']->language, $og->gear->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                    $row++;
                                    $border_row[] =$row;
                                    $summ_of_one_cat += $og->getValue();
                                }
                                $offerGears = OfferOuterGear::find()->where(['offer_group_id'=>$outer['id']])->all();
                                foreach ($offerGears as $og)
                                {
                                    $data[] = [\common\models\OuterGearModel::getTranslateName($og->outerGearModel->id, $view['model']->language, $og->outerGearModel->name), $formatter->asCurrency($og->price), $og->quantity, $og->discount, $og->duration, $formatter->asCurrency($og->getValue())];
                                    $row++;
                                    $border_row[] =$row;
                                    $summ_of_one_cat += $og->getValue();
                                }
                            }
                        }
                $data[] = ["", "", "", "",  Yii::t('app', "Łącznie")." ".$categoryName, $formatter->asCurrency($summ_of_one_cat)];
                $row++;
                $bold_row[]=$row;
                $total_summ_of_cats += $summ_of_one_cat;
        endforeach;
        $data[] = [""];
        $row++;
        
        $data[] = [ Yii::t('app', "Transport:")];
        $row++;
        $bold_row[]=$row;
        $underline_row[]=$row;
        $data[] = [ Yii::t('app', "Samochód"),  Yii::t('app', "Liczba"),  Yii::t('app', "Przelicznik"),  Yii::t('app', "Cena"), "",  Yii::t('app', "Razem netto")];
        $row++;
        $border_row[] =$row;
        $bold_row[]=$row;
        $transport_summ = 0;
        foreach ($model->offerSchedules as $schedule)
            {
                $time_type = $schedule->id;
                $title = [$schedule->name, "", "", "", ""];
                $title_shown = false;
         foreach ($model->getVehicleData() as $key => $vehicles): 
                foreach ($vehicles as $id=>$vehicle):
                if (!$title_shown)
                {
                    $title_shown = true;
                    $data[] = $title;
                    $row++;
                    $border_row[] =$row;
                    $bold_row[]=$row;
                }
                if ($vehicle['type']==$time_type){
                 $baseIndex = 'vehicleModels['.$id.']'; 
                $price = $vehicle['value'];
                $transport_summ += $price;
                $data[] = [\common\models\Vehicle::getTranslateName($vehicle['id'], $view['model']->language, $vehicle['name']), $vehicle['quantity'], $vehicle['distance'], $vehicle['price'], "", $formatter->asCurrency($price)];
                $row++;
                $border_row[] =$row;
                }
                endforeach; 
        endforeach;
        foreach ($model->getExtraItem(OfferExtraItem::TYPE_VEHICLE) as $vehicle) {
                if (!$title_shown)
                {
                    $title_shown = true;
                    $data[] = $title;
                    $row++;
                    $border_row[] =$row;
                    $bold_row[]=$row;
                }
                if ($vehicle->time_type==$time_type){
                $transport_summ += $vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100);
                $data[] = [$vehicle->name, $vehicle->quantity, $vehicle->duration, $vehicle->price, "", $formatter->asCurrency($vehicle->price * $vehicle->quantity * $vehicle->duration * (1 - $vehicle->discount / 100))];
                $row++;
                $border_row[] =$row;
                }
            }
        }
        $data[] = ["", "", "", "",  Yii::t('app', "Łącznie Transport"), $formatter->asCurrency($transport_summ)];
        $row++;
        $bold_row[]=$row;
        $data[] = [""];
        $row++;
        $data[] = [ Yii::t('app', "Obsługa techniczna:")];
        $row++;
        $underline_row[]=$row;
        $bold_row[]=$row;
        $data[] = [ Yii::t('app', "Nazwa"),  Yii::t('app', "Cena"),  Yii::t('app', "Liczba"),  Yii::t('app', "Liczba dni"), "",  Yii::t('app', "Razem netto")];
        $row++;
        $border_row[] =$row;
        $bold_row[]=$row;
        $skills_summ = 0;
        foreach ($model->offerSchedules as $schedule)
            {
                $time_type = $schedule->id;
                $title = [$schedule->name, "", "", "", ""];
                $title_shown = false;
            foreach ($offerForm->roleModels as $id => $rm):
                if ($rm->time_type==$time_type){
                $skills_summ += $rm->getValue();
                if (!$title_shown)
                {
                    $title_shown = true;
                    $data[] = $title;
                    $row++;
                    $border_row[] =$row;
                    $bold_row[]=$row;
                }
                $role = $rm->role;
                $baseIndex = 'roleModels['.$id.']';
                $data[] = [\common\models\UserEventRole::getTranslateName($role->id, $view['model']->language, $role->name), $formatter->asCurrency($rm->price), $rm['quantity'], $rm['duration'],"", $formatter->asCurrency($rm->getValue())];
                $row++;
                $border_row[] =$row;
                }
            endforeach;
        foreach ($model->getExtraItem(OfferExtraItem::TYPE_CREW) as $crew) {
                if ($crew->time_type==$time_type){
                    if (!$title_shown)
                {
                    $title_shown = true;
                    $data[] = $title;
                    $row++;
                    $border_row[] =$row;
                    $bold_row[]=$row;
                }
                $skills_summ += $crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100);
                $data[]=[$crew->name, $formatter->asCurrency($crew->price), $crew->quantity, $crew->duration, "", $formatter->asCurrency($crew->price * $crew->quantity * $crew->duration * (1 - $crew->discount / 100))];
                $row++;
                $border_row[] =$row;
                }
            }
        }
        $data[] = ["", "", "", "",  Yii::t('app', "Łącznie obsługa techniczna"), $formatter->asCurrency($skills_summ)];
        $row++;
        $bold_row[]=$row;
        $show_discount = false;
        foreach ($model->offerCustomItems as $key => $custom_field) {
            if ($custom_field->discount) {
                    $show_discount = true;
                }
            }

        $data[] = [""];
        $row++;

        $data[] = [ Yii::t('app', "Inne:")];
        $row++;
        $underline_row[]=$row;
        $bold_row[]=$row;
        $data[] = [ Yii::t('app', "Nazwa"),  Yii::t('app', "Cena"),  Yii::t('app', "Liczba"),  Yii::t('app', "Rabat %"), "",  Yii::t('app', "Razem netto")];
        $row++;
        $border_row[] =$row;
        $bold_row[]=$row;
        $custom_summ = 0;
        foreach ($model->offerCustomItems as $key => $custom_field) { 
            $custom_full_price = $custom_field->diff_count*$custom_field->quantity*($custom_field->price - ($custom_field->price*(int)$custom_field->discount/100));
            if ($show_discount) { $dis = $custom_field->discount; }else{$dis="";}
            $data[] = [$custom_field->name, $formatter->asCurrency($custom_field->price), $custom_field->diff_count, $dis,"", $formatter->asCurrency($custom_full_price)];
            $row++;
            $border_row[] =$row;
            $custom_summ += $custom_full_price; 
        }
        $data[] = ["", "", "", "",  Yii::t('app', "Łącznie inne"), $formatter->asCurrency($custom_summ)];
        $row++;
        $bold_row[]=$row;
        $data[] = [""];
        $row++;
        $total = $total_summ_of_cats+$transport_summ+$skills_summ+$custom_summ;
        
        $data[] = ["", "", "", "",  Yii::t('app', "Podsumowanie"), $formatter->asCurrency($total)];
        $row++;
        $data[] = [""];
        $bold_row[]=$row;
        $data[] = ["", "", "", "",  Yii::t('app', "Podsumowanie kosztów")];
        $data[] = ["", "", "", "",  Yii::t('app', "Koszt sprzętu:"), $formatter->asCurrency($total_summ_of_cats)];
        $data[] = ["", "", "", "",  Yii::t('app', "Koszt obsługi:"), $formatter->asCurrency($skills_summ)];
        $data[] = ["", "", "", "",  Yii::t('app', "Inne koszty:"), $formatter->asCurrency($custom_summ)];
        $data[] = ["", "", "", "",  Yii::t('app', "Wartość netto po rabacie:"), $formatter->asCurrency($total)];
        if (($model->budget>0)&&($total>$model->budget)) {
            $data[] = ["", "", "", "",  Yii::t('app', 'Dodatkowy rabat'), $formatter->asCurrency($total-$model->budget)." (".round(($total-$model->budget)/$total*100)."%)"];
            $data[] = ["", "", "", "",  Yii::t('app', 'Wartość po dodatkowym rabacie'), $formatter->asCurrency($model->budget)];
            $total = $model->budget;
                } 
        $vat = $total*0.23; 
        $brutto = $total + $vat; 
        $data[] = ["", "", "", "",  Yii::t('app', "Podatek VAT 23%:"), $formatter->asCurrency($vat)];
        $data[] = ["", "", "", "",  Yii::t('app', "Wartość brutto:"), $formatter->asCurrency($brutto)];
        if ($model->payment_date) { 
            $data[] = [""];
            $data[] = [ Yii::t('app', "Płatność w terminie:"), $model->payment_date];
        }
         if ($model->term_to) { 
            $data[] = [""];
            $data[] = [ Yii::t('app', "Oferta ważna do:"), $model->term_to];
        }  
        $data[] = [""];
        //$data[] = [ Yii::t('app', "Uwagi do sprzętu:")." "];
        //$summ_of_v_in_m_3 = $summ_of_v/1000000;
        //$data[] = [ Yii::t('app', "Całkowita objętość:")." ", $summ_of_v_in_m_3." ". Yii::t('app', 'm3')];
        //$data[] = [ Yii::t('app', "Całkowita waga netto:"). " " , $summ_of_weight." ". Yii::t('app', "kg")];
        //$data[] = [ Yii::t('app', "Całkowita moc:") ." " , $summ_of_power_consumption." ". Yii::t('app', "W")];

        return ['data'=>$data, 'bold'=>$bold_row, 'underline'=>$underline_row, 'border'=>$border_row];        
    }

    protected function createExcel($data, $view)
    {
         $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [

                str_replace(mb_substr($view['model']->name, 0, 31), "/", "_") => [   // Name of the excel sheet
                    'data' => $data['data'],

                    // Set to `false` to suppress the title row
                    'titles' => false
                ],
            ]
        ]);
        foreach(range('A','F') as $columnID) {
            $file->getWorkbook()->getSheet(0)->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        foreach ($data['bold'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":F".$i)->getFont()->setBold(true);
        }
        foreach ($data['underline'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":F".$i)->getFont()->setUnderline(true);
        }
        foreach ($data['border'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":F".$i)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        }
        return $file;       
    }

    public function actionExcel($id)
    {
        $view = $this->prepareView($id);
        Yii::$app->language = $view['model']->language; 
        $data = $this->prepareExcelData($view);
        $file = $this->createExcel($data, $view);

        // Save on disk
        $file->send($view['model']->name.'.xlsx');
    }

    public function actionSendMail($id)
    {
        $model = new \backend\models\SendOfferMail();
        $model->offerId = $id;
        $offer = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()){

//        	if  (empty($model->email) == false)
//	        {
//	        	$model->recipients[$model->email] = $model->email;
//	        }

            $mail = \Yii::$app->mailer->compose('@app/modules/offers/views/default/mail', [
                'model' =>  $model,
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->user->identity->email])
            ->setTo($model->recipients)
            ->setSubject($model->subject)
            ->setReplyTo(Yii::$app->user->identity->email);
            $view = $this->prepareView($id);
                $pdf = $this->preparePDF($id,$view,Pdf::DEST_FILE);
                

            if($model->attachPDF){
                $pdf->render();
                $filename = Inflector::slug($view['model']->name);
                $mail->attach($pdf->filename);
                $atts = SettingAttachment::find()->where(['type'=>SettingAttachment::TYPE_OFFER])->all();
                foreach ($atts as $a)
                {
                    $mail->attach($a->getFilePath());
                }
            }
            if($model->attachExcel){
                $view = $this->prepareView($id);
                $data = $this->prepareExcelData($view);
                $file = $this->createExcel($data, $view);

                $file->saveAs(Yii::getAlias('@uploadroot/xls/'.str_replace($view['model']->name, "/", "_").'.xlsx'));
                $mail->attach(Yii::getAlias('@uploadroot/xls/'.str_replace($view['model']->name, "/", "_").'.xlsx'));
            }             
            if ($mail->send())
            {
                $model->updateEvent();
                \common\models\Note::createNote(3, 'offerSend', $view['model'], $view['model']->id);
                $filename .= date('YmdHis');
                $pdf->Output(Yii::getAlias('@uploadroot/offer/'.$filename.'.pdf'), Yii::getAlias('@uploadroot/offer/'.$filename.'.pdf'), 'F');
                $view['model']->saveOfferSend($model->recipients, $filename.'.pdf');
                $eventlog = new \common\models\OfferLog;
                        $eventlog->offer_id = $view['model']->id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Oferta ").$view['model']->name.Yii::t('app', ' została wysłana.');
                        date_default_timezone_set(Yii::$app->params['timeZone']);
                        $eventlog->create_time = date('Y-m-d H:i:s');
                        $eventlog->save();
                if ($view['model']->event_id)
                {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $view['model']->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Oferta ").$view['model']->name.Yii::t('app', ' została wysłana.');
                        $eventlog->save();
                }
                if ($view['model']->rent_id)
                {
                        $eventlog = new RentLog;
                        $eventlog->rent_id = $view['model']->rent_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Oferta ").$view['model']->name.Yii::t('app', ' została wysłana.');
                        $eventlog->save();                    
                }
                Yii::$app->session->setFlash('success',  Yii::t('app', 'Email wysłany!'));
            } else {
                Yii::$app->session->setFlash('danger',  Yii::t('app', 'Błąd!'));
            }

            return $this->redirect(['view', 'id'=>$id]);
        } 
        $model->attachExcel = true;
        return $this->render('send-mail', [
            'model' => $model,
        ]);
    }

    protected function preparePDF($id, $view, $distination=null){
    	$dist = isset($distination) ? $distination : Pdf::DEST_BROWSER;
    	
        $settings = $view['settings'];
        $offerForm = new OfferForm([
            'offer'=>$view['model'],
        ]);
        if (isset($view['prices']))
        {
            $prices = false;
        }else{
            $prices = true;
        }
        $draft = $view['model']->offerDraft;
        if ($draft->header_section==1)
        {
            $marginTop = 45;
        }else{
            $marginTop = 15;
        }
        if ($draft->footer_section==1)
        {
            if ($settings['footerSize']->value)
                $marginBottom = $settings['footerSize']->value;
            else
                $marginBottom = 35;
        }else{
            $marginBottom = 15;
        }
        $content = $this->renderPartial('pdf', [
            'offerForm' => $offerForm,
            'model' =>  $view['model'],
            'gear_list' => $view['gear_list'],
            'vehicles' => $view['vehicles'],
            'settings' => $settings,
            'skills' => $view['skills'],
            'users' => $view['users'],
            'prices' => $prices
        ]);
        if ($draft->header_section==2)
        {
                $header = "";
        }else{
            $header = $this->renderPartial('pdf-header', [
            'model' =>  $view['model'],
            'settings' => $settings
                ]);
            if ($draft->header_section==3)
            {
                    $content = $header.$content;
                    $header="";
            }
        }
        if ($draft->footer_section==2)
        {
                $footer = "";
        }else{
            $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $view['model'],
            'settings' => $settings
        ]);
            if ($draft->footer_section==3)
            {
                    $content = $content.$footer;
                    $footer = "";
            }
        }

        



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
                'marginTop' => $marginTop,
                'marginBottom' => $marginBottom,
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
                'options' => ['title' => $view['model']->name],
                'filename' => Inflector::slug($view['model']->name).'_'.$id.'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
		
		$pdf->options = [
			'defaultheaderline' => 0,
			'defaultfooterline' => 0		];
		return $pdf;
    }

    protected function prepareView($id){
        $model = $this->findModel($id);
        $vehicles = OfferVehicle::find()->joinWith(['vehicle'])->where(['offer_id'=>$id])->all();
        $settings = Settings::find()->indexBy('key')->all(); 
        $skills = Skill::find()->indexBy('id')->all(); 
        $users = User::find()->indexBy('id')->all(); 
        $gear_list = Offer::getAssignedGearsList($id);

        return [
            'model' =>  $model,
            'gear_list' => $gear_list,
            'vehicles' => $vehicles,
            'settings' => $settings,
            'skills' => $skills,
            'users' => $users,
        ];
    }

    public function actionAssignSkills($id)
    {
        $this->enableCsrfValidation = false;
        $models =  OfferUserSkills::find()->where(['offer_id'=>$id])->all();
        if(!$models){
            $models = [new OfferUserSkills()];
        }

        $request = Yii::$app->request->post();
        if ($request) {
            if(isset($request["OfferUserSkills"])){
                OfferUserSkills::deleteAll(["offer_id"=>$id]);
                foreach ($request["OfferUserSkills"] as $key => $offerUserSkills) {
                    $arr["OfferUserSkills"] = $offerUserSkills;
                    $model = new OfferUserSkills();
                    $model->load($arr);
                    $model->offer_id = $id;
                    $model->save();
                }
            }

            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('assign-skills', [
                'models' => $models,
            ]);
        }
    }

    /**
     * Creates a new Offer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null,$event_id=null, $rent_id=null, $project_id = null)
    {
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $model = new Offer();
        if (Yii::$app->params['companyID']=="visualsupport")
        {
            $model->comment = "<table><tr><td width='200px'>".Yii::t('app', 'Data dostawy')."</td><td></td></tr><tr><td>".Yii::t('app', 'Data odbioru')."</td><td></td></tr><tr><td>".Yii::t('app', 'Miejsce dostawy')."</td><td></td></tr><tr><td>".Yii::t('app', 'Miejsce odbioru')."</td><td></td></tr></table>";
        }
        $settings = Settings::find()->indexBy('key')->all(); 
        $model->event_id = $event_id;
        $model->rent_id = $rent_id;
        $model->project_id = $project_id;
        $model->offer_date = date('Y-m-d');
        $model->manager_id = Yii::$app->user->id;
        if (isset($settings['offerPayingTerm']))
                $model->payment_days = $settings['offerPayingTerm']->value;
        $schedule = \common\models\ScheduleType::find()->orderBy(['default'=>SORT_DESC])->one();
        if ($schedule)
            $model->event_type = $schedule->id;
        if ($model->event !== null)
        {
            $event = $model->event;

            unset($event->status);
            $model->attributes = $event->attributes;
        }

        if ($model->rent != null)
        {
            $rent = $model->rent;
            $model->name = $rent->name;
            $model->customer_id = $rent->customer_id;
            $model->contact_id = $rent->contact_id;
            $model->manager_id = $rent->created_by;
        }

        if ($model->load(Yii::$app->request->post()) && $model->setDateAttributes() && $model->save()) {

            $schedules = Yii::$app->request->post('ScheduleForm');
            $type = Yii::$app->request->post('Offer')['event_type'];
            $model->saveSchedule($type, $schedules['schedules']);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {

            $model->prepareDateAttributes();
            $user = Yii::$app->user->identity;
            $model->manager_id = $user->id;
            return $this->render('create', [
                'model' => $model,
                'paymentsArray'=>\common\models\Customer::getPaymentArray()
            ]);
        }
    }

    public function actionSchedule($id, $type)
    {
        if ($type!=1000000)
        {
            $models = Schedule::find()->where(['event_type_id'=>$type])->orderBy(['position'=>SORT_ASC])->all();
        }else{
            $models = null;
        }
        if ($models)
        {

        }
    }

    /**
     * Updates an existing Offer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && $model->setDateAttributes() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->prepareDateAttributes();
            return $this->render('update', [
                'model' => $model,
                'paymentsArray'=>\common\models\Customer::getPaymentArray()
            ]);
        }
    }

    public function actionRules($id)
    {
        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) &&  $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, '#'=>'tab-rules']);
        } else {
            exit;
        }
    }


    public function actionChangeStatus($id, $status)
    {
        $model = $this->findModel($id);
        $model->status = $status;
        $model->save();
        exit;
    }

    public function actionChangeStatut($id)
    {
        return $this->renderAjax('change_statut', ['model'=>$this->findModel($id)]);
    }

    public function actionChangeBudget($id, $value)
    {
        $model = $this->findModel($id);
        $model->budget = $value;
        $model->save();
        exit;
    }

    public function actionChangeCost($id, $value, $type)
    {
        $model = $this->findModel($id);
        if ($type==2)
        {
            $model->pm_cost=$value;
        }else{
            $model->pm_cost_percent=$value;
        }
        $model->save();
        exit;
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $old = $model->status;
        $post = Yii::$app->request->post();
        $status = $post['Offer'][$post['editableIndex']]['status'];
        $model->status = $status;
        $model->save();
        if (isset($model->rent))
            $model->rent->addLog(Yii::t('app', 'Zmiana statusu oferty ').$model->name);
        $list = \common\models\Offer::getStatusList();
        $output = ['output'=>$model->getStatusButton(), 'message'=>''];
        return $output;
        exit;
    }

    /**
     * Deletes an existing Offer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionEvent($id)
    {
        $model = $this->findModel($id);
        $event = new Event;
        $event->attributes = $model->attributes;
        $event->code = null;
        $event->number = null;
        $event->type = \common\models\EventModel::findOne(['type'=>1])->id;
        if ($event->save())
        {
            $model->event_id = $event->id;
            $model->save();
            $event->copySchedules($model);
            return $this->redirect(['/event/view', 'id' => $event->id]);
        }else{
            return $this->redirect(['view', 'id' => $id]);
        }
    }

    public function actionRent($id)
    {
        $model = $this->findModel($id);
        $rent = new Rent;
        $rent->name = $model->name;
        $rent->status = 1;
        $rent->customer_id = $model->customer_id;
        $rent->contact_id = $model->contact_id;
        if ($model->event_start)
            $rent->start_time = $model->event_start;
        else
            $rent->start_time = date("Y-m-d H:i:s");
        if ($model->event_end)
            $rent->end_time = $model->event_end;
        else
            $rent->end_time = date("Y-m-d H:i:s");
        $rent->days = 1;
        if ($rent->save())
        {
            $model->rent_id = $rent->id;
            $model->save();
            return $this->redirect(['/rent/update', 'id' => $rent->id]);
        }else{
            return $this->redirect(['view', 'id' => $id]);
        }       
    }

    public function actionCreateFromEvent($event_id)
    {
        $event = Event::findOne($event_id);
        $model = new Offer;
        $model->attributes = $event->attributes;
        $model->created_by = Yii::$app->user->id;
        $model->event_id = $event_id;
        $model->offer_date = date("Y-m-d H:i:s");
        $draft = \common\models\OfferDraft::find()->one();
        $model->offer_draft_id = $draft->id;
        $priceGroup = \common\models\PriceGroup::find()->one();
        $model->price_group_id = $priceGroup->id;
        $model->status = \common\models\OfferStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->one()->id;
        if ($model->save())
        {
            //skopiować sprzęt
            foreach ($event->eventSchedules as $schedule)
            {
                $os = new \common\models\OfferSchedule();
                $os->attributes = $schedule->attributes;
                $os->offer_id = $model->id;
                $os->save();
            }
            foreach($event->eventGears as $gear)
            {
                $params = ['offer_id'=>$model->id, 'gear_id' => $gear->gear_id];
                $og = new OfferGear($params); 
                $price = $gear->gear->getDefaultPrice($model->id);
                if ($price){
                    $og->price = $price->price;
                    $og->gears_price_id = $price->gears_price_id;
                }
                $og->quantity = $gear->quantity;
                if (Yii::$app->params['companyID']=="loungetime")
                {
                    $og->duration = 1;
                }else{
                    $og->duration = ceil((strtotime($gear->end_time)-strtotime($gear->start_time))/(3600*24));
                }
                
                $og->loadOfferSettings();
                $og->save(false);
            }
            foreach($event->eventOuterGearModels as $gear)
            {
                $params = ['offer_id'=>$model->id, 'outer_gear_model_id' => $gear->outer_gear_model_id];
                $og = new OfferOuterGear($params); 
                $og->quantity = $gear->quantity;
                if (Yii::$app->params['companyID']=="loungetime")
                {
                    $og->duration = 1;
                }else{
                    $og->duration = ceil((strtotime($gear->end_time)-strtotime($gear->start_time))/(3600*24));
                }
                $og->loadOfferSettings();
                $og->save(false);
            }
            $data =$event->getAssignedUsersByTime(); 
            foreach ($event->eventSchedules as $schedule)
            {
                $duration = 1;
                if (isset($data[$schedule->id]))
                foreach ($data[$schedule->id] as $index => $role){
                    $s = OfferSchedule::find()->where(['name'=>$schedule->name, 'offer_id'=>$model->id])->one();
                    $role2 =  UserEventRole::findOne($index);
                    $or = new OfferRole;
                    $or->offer_id = $model->id;
                    $or->role_id = $index;
                    $or->duration = $duration;
                    $or->time_type = $s->id;
                    $or->price = $role2->salary_customer;
                    if ( $role['quantity']> $role['added'])
                        $or->quantity = $role['quantity'];
                    else
                        $or->quantity = $role['added'];
                    $or->save();
                }
            }

            //skopiować sprzęt 
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else{
            //echo var_dump($model);
            //exit;
            return $this->redirect(['/event/view', 'id' => $event->id]);
        }
    }

    public function actionCreateFromRent($rent_id)
    {
        $rent = Rent::findOne($rent_id);
        $model = new Offer;
        $model->attributes = $rent->attributes;
        $model->event_start = $rent->start_time;
        $model->event_end = $rent->end_time;
        $model->rent_id = $rent_id;
        $model->offer_date = date("Y-m-d H:i:s");
        $draft = \common\models\OfferDraft::find()->one();
        $model->offer_draft_id = $draft->id;
        $priceGroup = \common\models\PriceGroup::find()->one();
        $model->price_group_id = $priceGroup->id;
                $model->status = \common\models\OfferStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->one()->id;

        if ($model->save())
        {
            foreach($rent->rentGears as $gear)
            {
                $params = ['offer_id'=>$model->id, 'gear_id' => $gear->gear_id];
                $og = new OfferGear($params); 
                $og->quantity = $gear->quantity;
                $og->duration = $rent->days;
                $og->loadOfferSettings();
                $og->save(false);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
            return $this->redirect(['/rent/view', 'id' => $rent->id]);
    }

    public function actionDuplicate($id)
    {
        $model = $this->findModel($id);
        $clone = new Offer;
        $clone->attributes = $model->attributes;
        $clone->number = null;
        $clone->code = null;
        $clone->status = \common\models\OfferStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->one()->id;
        $clone->offer_date = date('Y-m-d');
        $clone->created_by = Yii::$app->user->id;
        $clone->name = $model->name." (". Yii::t('app', "nowa").")";
        if ($clone->save()) {
            $vehicles = OfferVehicle::find()->where(['offer_id'=>$id])->all();
            $gears = OfferGear::find()->where(['offer_id'=>$id])->andWhere(['type'=>1])->all();
            $outer_gears = OfferOuterGear::find()->where(['offer_id'=>$id])->andWhere(['type'=>1])->all();
            $extra = OfferExtraItem::find()->where(['offer_id'=>$id])->all();
            $settings = OfferSetting::find()->where(['offer_id'=>$id])->all();
            $skills = OfferUserSkills::find()->where(['offer_id'=>$id])->all();
            $roles = OfferRole::find()->where(['offer_id'=>$id])->all();
            $customs = OfferCustomItems::find()->where(['offer_id'=>$id])->all();
            $costs = OfferExtraCost::find()->where(['offer_id'=>$id])->andWhere(['offer_gear_id'=>null])->andWhere(['offer_extra_item_id'=>null])->all();
            
            $schedules = \common\models\OfferSchedule::find()->where(['offer_id'=>$id])->all();
            $schedules_map = [];
            foreach ($schedules as $schedule)
            {
                $clone_schedule = new \common\models\OfferSchedule;
                $clone_schedule->attributes = $schedule->attributes;
                $clone_schedule->offer_id = $clone->id;
                $clone_schedule->save();
                $schedules_map[$schedule->id] = $clone_schedule->id;
            }
            foreach ($vehicles as $vehicle)
            {
                $clone_vehicle = new OfferVehicle;
                $clone_vehicle->attributes = $vehicle->attributes;
                $clone_vehicle->offer_id = $clone->id;
                $clone_vehicle->type = $schedules_map[$vehicle->type];
                $clone_vehicle->save();
            }
            foreach ($gears as $gear)
            {
                $clone_gear = new OfferGear;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();    
                $offerGears = OfferGear::find()->where(['offer_gear_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_gear_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                } 
                $offerGears = OfferOuterGear::find()->where(['offer_gear_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferOuterGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_gear_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                }         
            }
            foreach ($outer_gears as $gear)
            {
                $clone_gear = new OfferOuterGear;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();   
                $offerGears = OfferGear::find()->where(['offer_outer_gear_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_outer_gear_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                } 
                $offerGears = OfferOuterGear::find()->where(['offer_outer_gear_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferOuterGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_outer_gear_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                }             
            }
            foreach ($extra as $gear)
            {
                $clone_gear = new OfferExtraItem;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                if ($gear->type)
                    $clone_gear->time_type = $schedules_map[$gear->time_type];
                $clone_gear->save();    
                $offerGears = OfferGear::find()->where(['offer_group_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_group_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                } 
                $offerGears = OfferOuterGear::find()->where(['offer_group_id'=>$gear->id])->andWhere(['offer_id'=>$model->id])->all(); 
                foreach ($offerGears as $og)
                {
                    $clone_gear2 = new OfferOuterGear;
                    $clone_gear2->attributes = $og->attributes;
                    $clone_gear2->offer_group_id = $clone_gear->id;
                    $clone_gear2->offer_id = $clone->id;
                    $clone_gear2->save();
                }           
            }
            foreach ($settings as $gear)
            {
                $clone_gear = new OfferSetting;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();               
            }
            foreach ($skills as $gear)
            {
                $clone_gear = new OfferUserSkills;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();               
            }
            foreach ($roles as $gear)
            {
                $clone_gear = new OfferRole;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->time_type = $schedules_map[$gear->time_type];
                $clone_gear->save();               
            }
            foreach ($customs as $gear)
            {
                $clone_gear = new OfferCustomItems;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();               
            }
            foreach ($costs as $gear)
            {
                $clone_gear = new OfferExtraCost;
                $clone_gear->attributes = $gear->attributes;
                $clone_gear->offer_id = $clone->id;
                $clone_gear->save();               
            }
            return $this->redirect(['view', 'id' => $clone->id]);
        } else {
           return $this->redirect(['index']);
        }
    }

    public function actionAddToEvents($id=null,$event_id=null)
    {
        $model = $this->findModel($id);
        if(isset($model->event_id)){
        	return $this->redirect(['/event/view', 'id' => $model->event_id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	return $this->redirect(['/offer/default/view', 'id' => $model->id]);
        }

        return $this->render('add-to-event', [
            'model' => $model,
        ]);
        
    }

    public function actionAddToRent($id=null,$rent_id=null)
    {
        $model = $this->findModel($id);
        if(isset($model->rent_id)){
            return $this->redirect(['/rent/view', 'id' => $model->rent_id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/offer/default/view', 'id' => $model->id]);
        }

        return $this->render('add-to-rent', [
            'model' => $model,
        ]);
        
    }

    public function actionOfferProject($project_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax && Project::findOne($project_id))
        {
            $params = Yii::$app->request->post();
            $model = Offer::findOne($params['itemId']);
            if(!$model){return false;}
            if ($params['add'] == 1)
            {
                $model->project_id = $project_id;
                return $model->save();
            }
            else
            {
                return $model->removeFromProject();
            }

        } else {
            return false;
        }
        
    }

    public function actionOfferEvent($event_id)
    {
    	Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax && Event::findOne($event_id))
        {
            $params = Yii::$app->request->post();
            $model = Offer::findOne($params['itemId']);
            if(!$model){return false;}
            if ($params['add'] == 1)
            {
                $model->event_id = $event_id;
                        $eventlog = new EventLog;
                        $eventlog->event_id = $model->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content =  Yii::t('app', "Do eventu dodano ofertę").": ".$model->name;
                        $eventlog->save();
                return $model->save();
            }
            else
            {
                return $model->removeFromEvent();
            }

        } else {
        	return false;
        }
        
    }

    public function actionOfferRent($rent_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        $rent = Rent::findOne($rent_id);
        if (Yii::$app->request->isAjax && $rent )
        {
            $params = Yii::$app->request->post();
            $model = Offer::findOne($params['itemId']);
            if(!$model){return false;}
            if ($params['add'] == 1)
            {
                $model->rent_id = $rent_id;
                $rent->addLog(Yii::t('app', 'Do wypożyczenia podpięto ofertę: '.$model->name));
                return $model->save();
            }
            else
            {
                $model->rent_id = null;
                $rent->addLog(Yii::t('app', 'Z wypożyczenia odpięto ofertę: '.$model->name));
                return $model->save();

            }

        } else {
            return false;
        }
        
    }

    public function actionAssignGear()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $data = Yii::$app->request->post('data');
        foreach ($data as $query)
        {
            parse_str($query, $x);
            $d = $x['OfferGear'];

            $params = ['offer_id'=>$d['offer_id'], 'gear_id' => $d['gear_id'], 'id'=>$d['id']];
            echo var_dump($params);
            $model = OfferGear::findOne($params);
            if ($model === null)
            {
                $model = new OfferGear($params);

            }
            if($model->load($x))
            {
                if ($model->quantity < 1)
                {
                    Offer::removeGear($model->offer_id, $model->gear_id);
                }
                else if ($model->validate() == false)
                {
                    $error = current($model->getFirstErrors());
                    $response = [
                        'success'=>0,
                        'error'=>$error,
                    ];
                }
                else
                {
                    $model->loadOfferSettings();
                    $model->save(false);
                }

            }

        }
        return $response;

    }

    public function actionManageGearConnected($offer_id, $type2=null, $item=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $gsi= Gear::findOne($request->post('gear_id'));
        $quantity = $request->post('quantity');
        $params = ['offer_id'=>$offer_id, 'gear_id' => $gsi->id];
        if (!$type2)
        {
            $params['type'] = 1;
            if ($request->post('parent_id'))
            {
                $params['type'] = 2;
                $og = OfferGear::findOne(['offer_id'=>$offer_id, 'gear_id'=>$request->post('parent_id'), 'type'=>1]);
                $params['offer_gear_id'] = $og->id;
            }
        }else{
            $params['type'] =2;
            if ($type2=='gear')
            {
                $params['offer_gear_id'] = $item;
                $og = OfferGear::findOne($item);
            }
            if ($type2=='outerGear')
            {
                $params['offer_outer_gear_id'] = $item;
                $og = OfferOuterGear::findOne($item);
            }
            if ($type2=='extraGear')
            {
                $params['offer_group_id'] = $item;
                $og = OfferExtraItem::findOne($item);
            }
        }
        $event = Offer::find()->where(['id'=>$offer_id])->one();
                $oldQuantity=0;
                $egm = OfferGear::findOne($params);
                if ($egm)
                    $oldQuantity = $egm->quantity;
                else{
                    
                    $egm = new OfferGear($params);
                    $price = $egm->gear->getDefaultPrice($offer_id);
                    $egm->price = $price->price;
                    $egm->gears_price_id = $price->gears_price_id;
                    $egm->first_day_percent = $og->first_day_percent;
                    $egm->discount = $og->discount;
                }
                $egm->quantity = $oldQuantity+$quantity;
                if ($egm->save() == false)
                {
                    $error = current($egm->getFirstErrors());
                    $response['responses'][] = [
                        'success'=>0,
                        'error'=>$error,
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                    ];
                }else{
                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                        ];


                }
        return $response;
    }

    public function actionManageGearOuterConnected($offer_id, $type2=null, $item=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $gsi= OuterGearModel::findOne($request->post('gear_id'));
        $quantity = $request->post('quantity');
        $event = Offer::find()->where(['id'=>$offer_id])->one();
        $params = ['offer_id'=>$offer_id, 'outer_gear_model_id' => $gsi->id];
        if (!$type2)
        {
            $params['type'] = 1;
            if ($request->post('parent_id'))
            {
                $params['type'] = 2;
                $og = OfferGear::findOne(['offer_id'=>$offer_id, 'gear_id'=>$request->post('parent_id'), 'type'=>1]);
                $params['offer_gear_id'] = $og->id;
            }
        }else{
            $params['type'] =2;
            if ($type2=='gear')
            {
                $params['offer_gear_id'] = $item;
                $og = OfferGear::findOne($item);
            }
            if ($type2=='outerGear')
            {
                $params['offer_outer_gear_id'] = $item;
                $og = OfferOuterGear::findOne($item);
            }
            if ($type2=='extraGear')
            {
                $params['offer_group_id'] = $item;
                $og = OfferExtraItem::findOne($item);
            }
        }
                $oldQuantity=0;
                $egm = OfferOuterGear::findOne($params);
                if ($egm)
                    $oldQuantity = $egm->quantity;
                else{
                    $egm = new OfferOuterGear($params);
                    $egm->first_day_percent = $og->first_day_percent;
                    $egm->discount = $og->discount;
                    $egm->gears_price_id = $og->gears_price_id;
                }
                $egm->quantity = $oldQuantity+$quantity;
                $egm->loadOfferSettings();
                if ($egm->save() == false)
                {
                    $error = current($egm->getFirstErrors());
                    $response['responses'][] = [
                        'success'=>0,
                        'error'=>$error,
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                    ];
                }else{
                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                        ];


                }
        return $response;
    }

    public function actionManageGear($offer_id)
    { 
        Yii::$app->response->format = Response::FORMAT_JSON;
        // $this->enableCsrfValidation = false;
        $response = [
            'success'=>1,
            'error'=>'',
            'connected'=>[],
            'outerconnected'=>[]
        ];
        $connected = [];
        $outerconnected = [];
        $request = Yii::$app->request->post();
        if($request){
            $params = $request['OfferGear'];
            $model = OfferGear::findOne($request['OfferGear']['id']);
            
            if ($model === null)
            {
                $model = new OfferGear($params);
                $old_quantity = 0;

            }else{
                $old_quantity = $model->quantity;
            }
            if($model->load($request))
            {
                if ($model->quantity < 1)
                {
                    Offer::removeGear($offer_id, $request['OfferGear']['id']);
                    $response = [
                        'success'=>0,
                        'error'=>Yii::t('app', 'Sprzęt usunięty z oferty'),
                    ];
                }
                else if ($model->validate() == false)
                {
                    $error = current($model->getFirstErrors());
                    $response = [
                        'success'=>0,
                        'error'=>$error,
                    ];
                }
                else
                {
                    $model->loadOfferSettings();
                    $price = $model->gear->getDefaultPrice($offer_id);
                    if ($price){
                        $model->price = $price->price;
                        $model->gears_price_id = $price->gears_price_id;
                    }else
                        $model->price = $model->gear->price;
                    
                    $model->save(false);
                    $response = [
                            'success'=>1,
                            'error'=>'',
                            'gear_id'=>$model->id,
                            'connected'=>$connected,
                            'outerconnected'=>$outerconnected,
                        ];
                if ($model->quantity>$old_quantity)
                {
                    
                    if ((count($model->gear->gearConnecteds)>0)||(count($model->gear->gearOuterConnecteds)>0))
                    {
                        $count = $model->quantity-$old_quantity;
                        foreach($model->gear->gearConnecteds as $gc)
                        {
                            if ($gc->in_offer)
                                if ($gc->gear_quantity)
                                    $connected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'subgroup'=>$gc->subgroup, 'gear_id'=>$gc->gear_id];
                        }
                        foreach($model->gear->gearOuterConnecteds as $gc)
                        {
                            if ($gc->in_offer){
                                if ($gc->gear_quantity)
                                    $outerconnected[] = ['id'=>$gc->connected->id, 'count'=>ceil($count*$gc->quantity/$gc->gear_quantity), 'checked'=>$gc->checked, 'name'=>$gc->connected->name, 'subgroup'=>$gc->subgroup, 'gear_id'=>$gc->gear_id];
                            }
                        }
                        $response = [
                            'success'=>1,
                            'error'=>'',
                            'gear_id'=>$model->id,
                            'connected'=>$connected,
                            'outerconnected'=>$outerconnected,
                        ];
                        
                    }                  
                }
                }
                return $response;
            }
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));

        }

    }

    public function actionDeleteOuterGearItem($offer_id)
    { 
        if(isset($_POST['itemId'])){
            $this->enableCsrfValidation = false;
            return Offer::removeOuterGearItem($offer_id,$_POST['itemId']);
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
        
    }

    public function actionManageOuterGearItem($offer_id, $update=false)
    { 
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        if(isset($_POST['itemId'])){
            $gear_id = $_POST['itemId'];
            if($update && isset($_POST['quantity']) && isset($_POST['discount']) && (int)$_POST['quantity'] > 0){
                $discount = ($_POST['discount'] == "") ? null : (int)$_POST['discount'];
                if(Offer::assignOuterGear($offer_id, $gear_id, (int)$_POST['quantity'], $discount)){
                    return ["status" => 1, "mess"=> Yii::t('app', "Zapisano!")];
                } else {
                    return ["status" => 0, "mess"=> Yii::t('app', "Bląd!")];
                }
            } else {
                $model = OfferOuterGear::find()->where(['offer_id' => $offer_id, 'outer_gear_id' => $gear_id])->one();
                if($model) {
                    return ['quantity' => $model->quantity,'discount' => $model->discount];
                }
            }
            
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }

    }

    public function actionDeleteVehicle($id)
    { 
            return OfferVehicle::findOne($id)->delete();
        exit;
        
    }

    public function actionChangeVehicleType($offer_id, $type) {
        $offer = Offer::findOne($offer_id);
        foreach ($offer->offerVehicles as $vehicle) {
            $vehicle->changePriceType($type);
        }
        $this->redirect(['view', 'id' => $offer_id]);
    }

    public function actionDeleteRole($id) {
            return OfferRole::findOne($id)->delete();

    }

    public function actionDeleteCustomField($offer_id) {
        if (isset($_POST['custom'])) {
            return Offer::removeCustomField($offer_id, $_POST['custom']);
        }
        else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionRemoveItem($offerId, $itemType, $itemId)
    {
        if (Yii::$app->request->isPost == false)
        {
            throw new BadRequestHttpException();
        }
        switch ($itemType)
        {
            case 'outerGear':
                Offer::removeOuterGear($offerId, $itemId);
                break;
            case 'gear':
                Offer::removeGear($offerId, $itemId);
                break;
            default:
                throw new BadRequestHttpException(Yii::t('app', 'Błędny typ'));
                break;
        }

    }

    public function actionManageVehicle($offer_id, $update=false)
    { 
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(isset($_POST['itemId'])){
            $vehicle_id = $_POST['itemId'];
            if($update && isset($_POST['quantity']) && (int)$_POST['quantity'] > 0){
                if(Offer::assignVehicle($offer_id, $vehicle_id, (int)$_POST['quantity'])){
                    return ["status" => 1, "mess"=> Yii::t('app', "Zapisano!")];
                } else {
                    return ["status" => 0, "mess"=> Yii::t('app', "Bląd!")];
                }
            } else {
                $model = OfferVehicle::find()->where(['offer_id' => $offer_id, 'vehicle_id' => $vehicle_id])->one();
                if($model) {
                    return ['quantity' => $model->quantity];
                }
            }
            
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }

    }

    public function actionOfferCustomItems($id)
    {
        $models = \common\models\OfferCustomItems::findAll(['offer_id'=>$id]);
        if(!$models){
            $models = [new \common\models\OfferCustomItems()];
        }
        
        $request = Yii::$app->request->post();
        if ($request) {
            \common\models\OfferCustomItems::deleteAll(['offer_id'=>$id]);
            foreach ($request['OfferCustomItems'] as $key => $value) {
                $model = new \common\models\OfferCustomItems($value);
                $model->offer_id = $id;
                $model->save();
            }

            return $this->redirect(['/offer/default/view', 'id'=>$id]);
        }

        return $this->render('offer-custom-items', [
            'models' => $models,
        ]);
    }

    public function actionOfferCustomItemEdit($id, $item_id)
    {
        $models = \common\models\OfferCustomItems::findAll(['offer_id'=>$id]);
        if(!$models){
            $model = [new \common\models\OfferCustomItems()];
        }
        
        $request = Yii::$app->request->post();
        if ($request) {
            \common\models\OfferCustomItems::deleteAll(['offer_id'=>$id]);
            foreach ($request['OfferCustomItems'] as $key => $value) {
                $model = new \common\models\OfferCustomItems($value);
                $model->offer_id = $id;
                $model->save();
            }

            return $this->redirect(['/offer/default/view', 'id'=>$id]);
        }

        return $this->render('offer-custom-items', [
            'models' => $models,
        ]);
    }  

    /**
     * Finds the Offer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Ta oferta nie istnieje lub została usunięta'));
        }
    }

    public function actionAddItem($offer) {
        $model = new OfferExtraItem();
        $model->offer_id = $offer;
        $model->gears_price_id = Offer::getDefaultGearsPrice($offer);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $this->redirect(['view', 'id' => $offer]);
        }
        else {
            $category_array = \common\models\GearCategory::find()->addOrderBy('root, lft')->andWhere(['lvl'=>1])->andWhere(['active'=>1])->asArray()->all();
            $category_array = \yii\helpers\ArrayHelper::map($category_array, 'id', 'name');

            return $this->renderAjax('new-offer-item', [
                'model'=>$model,
                'categories' => $category_array,
            ]);
        }


    }

    public function actionAddNew($id, $type, $item=null, $category_id=null, $type2=null){
            $offer = Offer::findOne($id);
            if (!$type2)
                $type2 = 'gear';
            if($type2=='gear')
            {
                $model = new OfferGear();
                $model->offer_id = $id;
            }
            if($type2=='outerGear')
            {
                $model = new OfferOuterGear();
                $model->offer_id = $id;
            }
            if($type2=='extraGear')
            {
                $model = new OfferExtraItem();
                $model->offer_id = $id;
                $model->category_id = null;
            }
            if ($type=='gear')
            {
                $model->offer_gear_id = $item;
            }
            if ($type=='extraGear')
            {
                $model->offer_group_id = $item;
            }
            if ($type=='outerGear')
            {
                $model->offer_outer_gear_id = $item;
            }
            if ($category_id)
            {
                 $params = [
                'type'=>OfferSetting::TYPE_GEAR,
                'offer_id' => $id,
                'category_id'=>$category_id,
                ];     
                $setting = OfferSetting::findOne($params);
                if ($setting === null)
                {
                    $discountList = $offer->customer->getDiscountsList();
                    $setting = new OfferSetting($params);


                    $setting->discount = ArrayHelper::getValue($discountList, $setting->category_id, 0);
                    $setting->duration = 1;
                    $setting->first_day_percent = Yii::$app->settings->get('firstDayPercent','offer', 50);
                    $setting->save();
                }
                    $model->discount = $setting->discount;
                    $model->first_day_percent = $setting->first_day_percent;
                    $model->duration = $setting->duration;
                          
            }


        
            return $this->renderAjax('add-new', [
                'model'=>$model,
                'type'=>$type,
                'type2'=>$type2
            ]);
    }

    public function actionDeleteExtraItem($item) {

        return OfferExtraItem::findOne($item)->delete();
    }
    public function actionEditExtraItem($item) {

        $model = OfferExtraItem::findOne($item);
        $category_array = \common\models\GearCategory::find()->addOrderBy('root, lft')->andWhere(['lvl'=>1])->andWhere(['active'=>1])->asArray()->all();
        $category_array = \yii\helpers\ArrayHelper::map($category_array, 'id', 'name');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $this->redirect(['view', 'id' => $model->offer_id]);
        }
        return $this->renderAjax('edit-extra-item', [
                'model'=>$model,
                'categories' => $category_array,
            ]);
    }


}
