<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\models\EventLog;
use common\models\EventVehicle;
use DateTime;
use Yii;
use common\models\Vehicle;
use common\models\VehicleService;
use common\models\VehicleSearch;
use backend\components\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use common\models\Event;
use common\models\Offer;
use common\models\OfferVehicle;
use yii\web\Response;

/**
 * VehicleController implements the CRUD actions for Vehicle model.
 */
class VehicleController extends Controller
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
                    'actions' => ['index', 'upload'],
                    'roles' => ['fleetVehicles'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['fleetVehiclesCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['fleetVehiclesView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['fleetVehiclesDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'service', 'service-return', 'edit-service', 'delete-service'],
                    'roles' => ['fleetVehiclesEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['assign-vehicle', 'assign-vehicle2'],
                    'roles' => ['eventsEventEditEyeVehiclesDelete', 'eventsEventEditEyeVehiclesManage'],
                ],
                [
                    'allow' => true,
                    'actions' => ['manage', 'manage-ajax', 'add-event-vehicle', 'update-event-vehicle', 'delete-event-vehicle', 'conflict-calendar', 'change-dates', 'change-dates2'],
                    'roles' => ['eventsEventEditEyeVehiclesManage'],
                ],
                [
                    'allow' => true,
                    'actions' => ['manage-offer', 'assign-offer-vehicle'],
                    'roles' => ['menuOffersEdit']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/vehicle'
            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

        public function actionChangeDates2($user_id, $event_id, $role_id)
    {
        $post = Yii::$app->request->post();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = str_replace("T"," ",$post['start']);
        $end = str_replace("T"," ",$post['end']);        
            //szukamy czy nie nachodzi na inne
            $schedule = \common\models\EventSchedule::findOne($event_id);
            $user = \common\models\User::findOne($user_id);
            $canSave = $user->checkAvability($start, $end, $schedule->event_id);
            if ($canSave)
            {
            $id = $schedule->event_id;
            $schedule_id = $schedule->id;
            $eventUSer = EventUser::findOne(['user_id'=>$user_id, 'event_id'=>$id]);
            if (!$eventUSer)
            {
                    $eventUSer = new EventUser();
                   $eventUSer->event_id = $id;
                   $eventUSer->user_id = $user_id;
                   $eventUSer->type = 1;
                   $eventUSer->create_time = date('Y-m-d H:i:s');
                   $eventUSer->update_time = date('Y-m-d H:i:s');
                   $eventUSer->save();
            }
            $new = EventUserPlannedWrokingTime::find()->where(['user_id'=>$user_id])->andWhere(['event_id'=>$id])->andWhere(['event_schedule_id'=>$schedule_id])->one();
            if (!$new)
            {
                
                $new = new EventUserPlannedWrokingTime();
                $new->user_id = $user_id;
                $new->event_id = $id;
                $new->start_time = $start;
                $new->end_time = $end;
                $new->event_schedule_id = $schedule_id;
                $new->save();
            }
            $r = new EventUserRole();
            $r->event_user_id = $eventUSer->id;
            $r->user_event_role_id = $role_id;
            $r->working_hours_id = $new->id;
            $r->save();
            return ['success'=>1, 'message'=>$eventUSer->user->displayLabel.Yii::t('app', ' przypisany do etapu ').$schedule->name];
            }else{
                return ['success'=>false];
            }


        return ['success'=>false]; 

    }

    public function actionChangeDates($vehicle_id, $event_id)
    {
        $post = Yii::$app->request->post();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = str_replace("T"," ",$post['start']);
        $end = str_replace("T"," ",$post['end']);        
        if ($post['type']=='event')
        {
            //szukamy czy nie nachodzi na inne
            $hour = \common\models\EventVehicleWorkingHours::findOne($event_id);
            //$user = \common\models\User::findOne($user_id);
            $canSave = true;
            if ($canSave)
            {
                
                $hour->start_time = $start;
                $hour->end_time = $end;
                $hour->save();
                return ['success'=>true]; 
            }else{
                return ['success'=>false];
            }


        }

        return ['success'=>false]; 

    } 

    public function actionConflictCalendar($vehicle_id, $event_id, $schedule_id=null, $role_id=null)
    {
        $vehicle= \common\models\Vehicle::findOne($vehicle_id);
        $event = \common\models\Event::findOne($event_id);
        if ($schedule_id)
                $schedule = \common\models\EventSchedule::findOne($schedule_id);
            else
                $schedule = null;
        return $this->renderAjax('_conflictCalendar', ['vehicle'=>$vehicle, 'event'=>$event, 'schedule'=>$schedule, 'role_id'=>$role_id]);
    }

    public function actionAddEventVehicle($id, $schedule)
    {
        $model = new \common\models\EventOfferVehicle();
        $model->event_id = $id;
        $model->schedule = $schedule;
        $ids = \common\helpers\ArrayHelper::map(\common\models\EventOfferVehicle::find()->where(['event_id'=>$id, 'schedule'=>$schedule])->asArray()->all(), 'vehicle_model_id', 'vehicle_model_id');
        $roles = \common\helpers\ArrayHelper::map(\common\models\VehicleModel::find()->where(['active'=>1])->andWhere(['NOT IN', 'id', $ids])->asArray()->all(), 'id', 'name');
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add-event-vehicle', [
            'model'=>$model,
            'roles'=>$roles
        ]);
        }
    }

    public function actionUpdateEventVehicle($vehicle_model_id, $event_id, $schedule)
    {
        $model = \common\models\EventOfferVehicle::findOne(['event_id'=>$event_id, 'vehicle_model_id'=>$vehicle_model_id, 'schedule'=>$schedule]);
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add-event-vehicle', [
            'model'=>$model,
        ]);
        }
    }

    public function actionDeleteEventVehicle($vehicle_model_id, $event_id, $schedule)
    {
        $model = \common\models\EventOfferVehicle::findOne(['event_id'=>$event_id, 'vehicle_model_id'=>$vehicle_model_id, 'schedule'=>$schedule]);
        $model->delete();
        exit;
    }
    public function actionService($id)
    {
        $model  = new VehicleService;
        $model->vehicle_id = $id;
        $model->create_time = date("Y-m-d");
        $model->status = 1;
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id'=>$id]);
            }
        }
        return $this->render('service', [
            'model' => $model,
        ]);
    }

    public function actionServiceReturn($id)
    {
        $model = VehicleService::find()->where(['vehicle_id'=>$id])->andWhere(['status'=>1])->one();
        $model->status = 2;
        $model->end_time = date("Y-m-d");
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id'=>$id]);
            }
        }
        return $this->render('service-return', [
            'model' => $model,
        ]);
    }

    public function actionEditService($id)
    {
        $model = VehicleService::find()->where(['id'=>$id])->one();
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id'=>$model->vehicle_id]);
            }
        }
        return $this->render('edit-service', [
            'model' => $model,
        ]);
    }

    public function actionDeleteService($id)
    {
        $model = VehicleService::find()->where(['id'=>$id])->one();
        if ($model->status==1)
        {
            $model->vehicle->status = 1;
            $model->vehicle->save();
        }
        $vehicle_id = $model->vehicle_id;
        $model->delete();

        return $this->redirect(['view', 'id'=>$vehicle_id]);
    }

    /**
     * Lists all Vehicle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehicleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vehicle model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vehicle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vehicle();

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save()) {
                $model->linkObjects();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Vehicle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();

        if ($model->load(Yii::$app->request->post())) {
            foreach ($model->notificationSmses as $sms) {
                $sms->delete();
            }
            foreach ($model->notificationMails as $mail) {
                $mail->delete();
            }
            $model->linkObjects();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Vehicle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        foreach ($model->notificationSmses as $sms) {
            if (new DateTime($sms->sending_time) > new DateTime()) {
                $sms->delete();
            }
        }
        foreach ($model->notificationMails as $mail) {
            $mail->delete();
        }
        $model->active = 0;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vehicle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vehicle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vehicle::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionManage($id, $from_date=null, $to_date=null)
    {
        $model = Event::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException();
        }


        $assignedItems = $model->getVehicles()->andWhere(['active'=>1])->column();
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $searchModel = new VehicleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);
        $dataProvider->pagination = false;

        return $this->render('manage', [
            'model'=>$model,
            'assignedItems'=>$assignedItems,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,

        ]);
    }

    public function actionManageAjax($id, $vehicle_id, $schedule=null)
    {
        $model = Event::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException();
        }


        $assignedItems = $model->getVehicles()->andWhere(['active'=>1])->column();
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $searchModel = new VehicleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);
        $dataProvider->pagination = false;

        return $this->renderAjax('manage-ajax', [
            'model'=>$model,
            'assignedItems'=>$assignedItems,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'schedule'=>$schedule,
            'vehicle_id'=>$vehicle_id

        ]);
    }

    //todo: filter ajax
    public function actionAssignVehicle($id)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $params = Yii::$app->request->post();
        $params['itemId'] = ArrayHelper::getValue($_POST, 'itemId', ArrayHelper::getValue($_POST, 'itemid'), 0);

        $attributes = [
            'event_id'=>$id,
            'vehicle_id'=>$params['itemId'],
        ];
        $vehicle = Vehicle::findOne($params['itemId']);
        if ($params['add'] == 1)
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Do eventu przypisano pojazd").": ".$vehicle->name.".";
            $eventlog->save();
            EventVehicle::assign($attributes);
        }
        else
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Z eventu usunięto pojazd").": ".$vehicle->name.".";
            $eventlog->save();
            EventVehicle::remove($attributes);
        }
        return $response;

    }

    //todo: filter ajax
    public function actionAssignVehicle2($id)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $params = Yii::$app->request->post();
        $vehicle = Vehicle::findOne($params['vehicle_id']);
        $model_id = $params['vehicle_model_id'];
        $schedule = \common\models\EventSchedule::findOne($params['schedule_id']);

        if ($params['add'] == 1)
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Do eventu przypisano pojazd").": ".$vehicle->name.".";
            $eventlog->save();
            $ev = \common\models\EventVehicle::findOne(['vehicle_id'=>$vehicle->id, 'event_id'=>$id]);
            if (!$ev)
            {
                $ev = new \common\models\EventVehicle();
                $ev->event_id = $id;
                $ev->vehicle_id = $vehicle->id;
                $ev->save();
            }
            $hour = new \common\models\EventVehicleWorkingHours();
            $hour->vehicle_id = $vehicle->id;
            $hour->event_id = $id;
            $hour->event_schedule_id = $schedule->id;
            $hour->vehicle_model_id = $model_id;
            $hour->start_time = $schedule->start_time;
            $hour->end_time = $schedule->end_time;
            $hour->save();
            $response = [
            'success'=>1,
            'message'=>$vehicle->name.Yii::t('app', ' zarezerwowany na etap ').$schedule->name
        ];
        }
        else
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Z eventu usunięto pojazd").": ".$vehicle->name.".";
            $eventlog->save();
            $hour = \common\models\EventVehicleWorkingHours::findOne(['vehicle_id'=>$vehicle->id, 'event_schedule_id'=>$schedule->id]);
            $hour->delete();
            $response = [
            'success'=>2,
            'message'=>$vehicle->name.Yii::t('app', ' usunięty z etapu ').$schedule->name
        ];

        }
        return $response;

    }

    //offer

    public function actionManageOffer($id, $from_date=null, $to_date=null)
    {
        $model = Offer::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException();
        }


        $assignedItems = $model->getVehicles()->column();
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $searchModel = new VehicleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andWhere(['active'=>1]);

        return $this->render('manage-offer', [
            'model'=>$model,
            'assignedItems'=>$assignedItems,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,

        ]);
    }

    //todo: filter ajax
    public function actionAssignOfferVehicle($id)
    {
        $params = Yii::$app->request->post();
        $attributes = [
            'offer_id'=>$id,
            'vehicle_id'=>$params['itemId'],
            'quantity' => 1
        ];
        if ($params['add'] == 1)
        {

            OfferVehicle::assign($attributes);
        }
        else
        {
            OfferVehicle::remove($attributes);
        }

    }

    public function actionDeleteSms($id) {
        if (Yii::$app->request->isPost) {
            foreach ($this->findModel($id)->notificationSmses as $sms) {
                $sms->delete();
            }
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }

    public function actionDeleteMail($id) {
        if (Yii::$app->request->isPost) {
            foreach ($this->findModel($id)->notificationMails as $mail) {
                $mail->delete();
            }
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }
}
