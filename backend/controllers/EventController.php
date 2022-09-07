<?php

namespace backend\controllers;

use backend\modules\permission\models\BasePermission;
use common\components\filters\AccessControl;
use common\models\ConflictUserWorkingHours;
use common\models\Contact;
use common\models\EventLog;
use common\models\Packlist;
use common\models\EventProvision;
use common\models\EventBreaks;
use common\models\EventConflict;
use common\models\EventBreaksUser;
use common\models\EventGearItem;
use common\models\EventGear;
use common\models\EventExtraItem;
use common\models\EventUser;
use common\models\EventUserPlannedBreaks;
use common\models\EventUserPlannedWrokingTime;
use common\models\EventUserRole;
use common\models\EventUserWorkingTime;
use common\models\EventVehicle;
use common\models\EventVehicleWorkingHours;
use common\models\GearItem;
use common\models\IncomesForEvent;
use common\models\OutcomesForEvent;
use common\models\EventOuterGear;
use common\models\EventOuterGearModel;
use common\models\User;
use common\models\UserEventRole;
use common\models\Notification;
use common\models\Vacation;
use common\models\Vehicle;
use common\models\PacklistGear;
use common\models\EventSchedule;
use DateInterval;
use DateTime;
use Yii;
use common\models\Event;
use common\models\EventSearch;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use kartik\mpdf\Pdf;
use common\models\Settings;
use yii\helpers\Inflector;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
{

    protected $_workingTimeClassName;
    protected $_workingTimeRelationName;
    protected $_workingIdAttribute;
    public $enableCsrfValidation = false;

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index', 'packlist-pdf', 'get-events-for-calendar', 'print-prod-tasks', 'day-plan', 'print-tasks', 'create-prod-event', 'save-gear-comment', 'get-purchase-items', 'delete-from-project', 'excel', 'add-schedule', 'update-schedule', 'delete-schedule', 'copy-crew-from-offer', 'copy-vehicle-from-offer', 'crew-tab', 'vehicle-tab', 'gear-tab', 'offer-tab', 'log-tab', 'finance-tab', 'stat-tab', 'notes-tab', 'recalculate-costs', 'set-schedule-change-ok'],
                    'roles' => ['eventsEvents'],
                ],
                [
                    'allow'=>true,
                    'actions' => ['cost', 'invoice-ready'],
                    'roles' => ['eventsEventEditEyeFinanceProjectStatus'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'add-prod', 'add-prod-task'],
                    'roles' => ['eventsEventAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['update-user', 'user-form'],
                    'roles' => ['eventsEventEditEyeCrewEdit']
                ],
                [
                    'allow' => true,
                    'actions' => ['add-packlist', 'get-packlist-modal', 'save-packlist-modal', 'packlist-delete', 'copy-from', 'copy-from-save', 'block-packlist', 'total-outcome'],
                    'roles' => ['eventEventEditEyeGearManage']
                ],

                [
                    'allow' => true,
                    'actions' => ['update',  'edit-name',  'save-field'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventEventEditPencil'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isEventUser('eventEventEditPencil');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventEventDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isEventUser('eventEventDelete');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'packing-list'],
	                'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventEventEditEye'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isEventUser('eventEventEditEye');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['update-working-time-event-gear-group', 'update-working-time-event-gear-item', 'update-working-time-event-gear', 'update-working-time', 'vehicle-form', 'get-assigned-gear', 'count-costs'],
                    'roles' => ['eventEventEditEyeGearEdit', 'eventsEventEditEyeVehiclesEdit']
                ],
                [
                    'allow' => true,
                    'actions' => ['are-there-user-working-hours-conflicts', 'assign-user-break', 'change-user-working-hours', 'check-availability-for-event', 'contact-list', 'resolve-conflict', 'send-event-notifications', 'send-all-events-notifications', 'resolve-conflict', 'conflict-calendar', 'edit-provision', 'save-section', 'copy-provisions', 'show-notes', 'add-note', 'add-to-projects', 'save-calendar-date', 'assign-users', 'change-status-modal', 'add-produkcja-event', 'save-reminders', 'delete-conflict', 'edit-provision-group', 'conflict-modal', 'schedule-order', 'save-schedule', 'save-packlist-modal-one', 'save-packlist-modal-conflict', 'repair-events', 'repair-events-all', 'status','change-status', 'change-sc-level', 'change-additional-status', 'change-additional-status2'],
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }
	
	    public function actionTotalOutcome($id)
    {
        $event = Event::findOne($id);
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        //wyszukujemy wszystkie wydania
        $gears = \common\models\EventGear::find()->where(['event_id'=>$id])->all();
        $content = $this->renderPartial('pdf_outcome', [
            'model' => $event,
            'settings' => $settings,
            'gears' =>$gears,
        ]);
			
			


        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
            'settings' => $settings
        ]);
        $footerSize = $settings['footerSize']->value;
        if ((!$footerSize)||($footerSize==""))
            $footerSize = 30;
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
                'marginTop' => 45,
                'marginBottom' => $footerSize,
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
                'options' => ['title' => 'Raport'],
                'filename' => Inflector::slug($event->name).'_raport_wydan.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();
    }

    public function actionRepairEventsAll()
    {
        return $this->render('repair-events-all');
    }
    public function actionSetScheduleChangeOk($id)
    {
         $model = \common\models\Event::findOne($id);
         $model->packing_type = 0;
         $model->save();
         exit;
    }
    public function actionRepairEvents()
    {
        $ids = \common\helpers\ArrayHelper::map(\common\models\EventSchedule::find()->asArray()->all(), 'event_id', 'event_id');
        $events = Event::find()->where(['NOT IN', 'id', $ids])->andWhere(['or', ['>', 'packing_start', '2000'], ['>', 'montage_start', '2000'], ['>', 'event_start', '2000'], ['>', 'disassembly_start', '2000']])->orderBy(['id'=>SORT_DESC])->limit(10)->all();
        foreach ($events as $event)
        {
            if ($event->packing_start)
            {
                $pack = new EventSchedule();
                $pack->name = "Pakowanie";
                $pack->prefix = "P";
                $pack->event_id = $event->id;
                $pack->start_time = $event->packing_start;
                $pack->end_time = $event->packing_end;
                $pack->position = 0;
                $pack->color = "#c9daf8";
            }
            if ($event->montage_start)
            {
                $montage = new EventSchedule();
                $montage->name = "Montaż";
                $montage->prefix = "M";
                $montage->event_id = $event->id;
                $montage->start_time = $event->montage_start;
                $montage->end_time = $event->montage_end;
                $montage->position = 1;
                $montage->color = "#ff9900";
            }
            if ($event->event_start)
            {
                $e = new EventSchedule();
                $e->name = "Event";
                $e->prefix = "E";
                $e->event_id = $event->id;
                $e->start_time = $event->event_start;
                $e->end_time = $event->event_end;
                $e->position = 4;
                $e->color = "#ff0000";
            }
            if ($event->disassembly_start)
            {
                $disassembly = new EventSchedule();
                $disassembly->name = "Demontaż";
                $disassembly->prefix = "D";
                $disassembly->event_id = $event->id;
                $disassembly->start_time = $event->disassembly_start;
                $disassembly->end_time = $event->disassembly_end;
                $disassembly->position = 5;
                $disassembly->color = "#b6d7a8";
            }
            if ($event->readiness_start)
            {
                $readiness = new EventSchedule();
                $readiness->name = "Gotowość";
                $readiness->prefix = "G";
                $readiness->event_id = $event->id;
                $readiness->start_time = $event->readiness_start;
                $readiness->end_time = $event->readiness_end;
                $readiness->position = 3;
                $readiness->color = "#ff9900";
            }
            if ($event->practice_start)
            {
                $practice = new EventSchedule();
                $practice->name = "Próby";
                $practice->prefix = "Pr";
                $practice->event_id = $event->id;
                $practice->start_time = $event->practice_start;
                $practice->end_time = $event->practice_end;
                $practice->position = 2;
                $practice->color = "#980000";
            }   

            if ($event->packing_start)
            {
                $pack->save();
            }
            if ($event->montage_start)
            {
                $montage->save();
            }
            if ($event->event_start)
            {
                $e->save();
            }
            if ($event->disassembly_start)
            {
                $disassembly->save();
            }
            if ($event->readiness_start)
            {
                $readiness->save();
            }
            if ($event->practice_start)
            {
                $practice->save();
            }  
            if (($event->packing_start)||($event->montage_start)||($event->event_start)||($event->disassembly_start))
            {


            $times = EventUserPlannedWrokingTime::find()->where(['event_id'=>$event->id])->all();
            foreach ($times as $t)
            {
                if ($t->start_time==$event->packing_start)
                {
                    $t->event_schedule_id = $pack->id;
                }
                if ($t->start_time==$event->montage_start)
                {
                    $t->event_schedule_id = $montage->id;
                }
                if ($t->start_time==$event->event_start)
                {
                    $t->event_schedule_id = $e->id;
                }
                if ($t->start_time==$event->disassembly_start)
                {
                    $t->event_schedule_id = $disassembly->id;
                }
                if ($t->start_time==$event->readiness_start)
                {
                    $t->event_schedule_id = $readiness->id;
                }
                if ($t->start_time==$event->practice_start)
                {
                    $t->event_schedule_id= $practice->id;
                }
                $t->save();
            }  
            $times = EventVehicleWorkingHours::find()->where(['event_id'=>$event->id])->all();
            foreach ($times as $t)
            {
                if ($t->start_time==$event->packing_start)
                {
                    $t->event_schedule_id = $pack->id;
                }
                if ($t->start_time==$event->montage_start)
                {
                    $t->event_schedule_id = $montage->id;
                }
                if ($t->start_time==$event->event_start)
                {
                    $t->event_schedule_id  = $e->id;
                }
                if ($t->start_time==$event->disassembly_start)
                {
                    $t->event_schedule_id  = $disassembly->id;
                }
                if ($t->start_time==$event->readiness_start)
                {
                    $t->event_schedule_id  = $readiness->id;
                }
                if ($t->start_time==$event->practice_start)
                {
                    $t->event_schedule_id  = $practice->id;
                }
                $t->save();
            }
            $packlist = new \common\models\Packlist();  
            $packlist->event_id = $event->id;
            $packlist->name = Yii::t('app',' Główna grupa');
            $packlist->main = 1;
            $packlist->start_time = $event->getTimeStart();
            $packlist->end_time = $event->getTimeEnd();   
            $packlist->color = "#333333";
            $packlist->save(); 

            $outcomes = \common\models\OutcomesForEvent::find()->where(['event_id'=>$event->id])->all();
            foreach ($outcomes as $o)
            {
                $o->packlist_id = $packlist->id;
                $o->save();
            }
            $incomes = \common\models\IncomesForEvent::find()->where(['event_id'=>$event->id])->all();
            foreach ($outcomes as $o)
            {
                $o->packlist_id = $packlist->id;
                $o->save();
            }
            $gears = EventGear::find()->where(['event_id'=>$event->id])->all();
            foreach ($gears as $gear)
            {
                $packlists = \common\models\PacklistGear::find()->where(['event_gear_id'=>$gear->id])->all();
                $q = 0;
                foreach ($packlists as $pg)
                {
                    $pg->gear_id = $gear->gear_id;
                    $pg->start_time = $gear->start_time;
                    $pg->end_time = $gear->end_time;
                    $pg->save();
                    $q+=$pg->quantity;
                }
                $p = new \common\models\PacklistGear();
                $p->event_gear_id = $gear->id;
                $p->gear_id = $gear->gear_id;
                $p->packlist_id = $packlist->id;
                $p->start_time = $packlist->start_time;
                $p->end_time = $packlist->end_time;
                $p->quantity = $gear->quantity-$q;
                $p->save();
                $conflict = EventConflict::find()->where(['event_id'=>$gear->event_id, 'gear_id'=>$gear->gear_id])->one();
                if ($conflict)
                {
                    $conflict->packlist_gear_id = $p->id;
                    $conflict->save();
                }
            }
        }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($events)
        {
            return ['success'=>1, 'events'=>$events];
        }else{
            return ['success'=>0];
        }
    }

    public function actionScheduleOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = \common\models\EventSchedule::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

        public function actionSaveSchedule()
    {
        $schedule = Yii::$app->request->post('EventSchedule');
        //date_default_timezone_set(Yii::$app->params['timeZone']);
        if (isset($schedule['id']))
        {
            $s = \common\models\EventSchedule::findOne($schedule['id']);
            
            $range = (Yii::$app->request->post("EventSchedule")[ "dateRange"]);
            $start = str_split($range,16)[0];
            $end = str_split($range, 19)[1];
            $s->start_time = $start;
            $s->end_time = $end;
            
            //$s->load(Yii::$app->request->post());
            $s->save();
            echo Yii::t('app', 'Termin').": ".Yii::$app->formatter->asDateTime($s->event->getTimeStart(),'short')." - ".Yii::$app->formatter->asDateTime($s->event->getTimeEnd(), 'short'); 
            exit;
        }else{
            exit;
        }
    }
    public function actionGearTab($id, $sort="cat")
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabGear', [
            'model'=>$model, 'sort'=>$sort        ]);
    }
    public function actionCrewTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabUser', [
            'model'=>$model        ]);
    }

    public function actionVehicleTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabVehicle', [
            'model'=>$model        ]);
    }
    public function actionOfferTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabOffers', [
            'model'=>$model        ]);
    }
    public function actionFinanceTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabFinances', [
            'model'=>$model        ]);
    }

    public function actionLogTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabLog', [
            'model'=>$model        ]);
    }
    public function actionStatTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabStatistic', [
            'model'=>$model        ]);
    }
    public function actionNotesTab($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_tabNotes', [
            'model'=>$model        ]);
    }

    public function actionRecalculateCosts($id)
    {
        $model = $this->findModel($id);
        $model->saveEventCosts();
        exit;
    }

    public function actionCopyVehicleFromOffer($id)
    {
        //pobieramy zaakceptowane oferty
        //z nich pobieramy role i przypisujemy do odpowiednich etapów
        $statuts = ArrayHelper::map(\common\models\OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        $models = \common\models\OfferVehicle::find()
            ->innerJoinWith([
                'offer',
            ])
            ->where([
                'offer.event_id'=>$id,
                'offer.status'=>$statuts
            ])
            ->all(); 
        \common\models\EventOfferVehicle::deleteAll(['event_id'=>$id]);

        //co zrobić z takimi co już są wpisane?? na razie kasuję

        foreach ($models as $model)
        {
            $schedule = \common\models\OfferSchedule::findOne($model->type);
            $eor = \common\models\EventOfferVehicle::find()->where(['event_id'=>$id, 'schedule'=>$schedule->name, 'vehicle_model_id'=>$model->vehicle_id])->one();
            if ($eor)
            {
                $eor->quantity += $model->quantity;
                $eor->save();
            }else{

                $eor = new \common\models\EventOfferVehicle();
                $eor->event_id = $id;
                $eor->vehicle_model_id = $model->vehicle_id;
                $eor->quantity = $model->quantity;
                
                $eor->schedule = $schedule->name;
                $eor->save();
            }

        }
        return $this->redirect(['view', 'id' => $id, '#'=>'tab-vehicle']);
    }

    public function actionCopyCrewFromOffer($id)
    {
        //pobieramy zaakceptowane oferty
        //z nich pobieramy role i przypisujemy do odpowiednich etapów
        $statuts = ArrayHelper::map(\common\models\OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        $models = \common\models\OfferRole::find()
            ->innerJoinWith([
                'offer',
                'role'
            ])
            ->where([
                'offer.event_id'=>$id,
                'offer.status'=>$statuts
            ])
            ->all(); 
        \common\models\EventOfferRole::deleteAll(['event_id'=>$id]);

        //co zrobić z takimi co już są wpisane?? na razie kasuję

        foreach ($models as $model)
        {
            $schedule = \common\models\OfferSchedule::findOne($model->time_type);
            $eor = \common\models\EventOfferRole::find()->where(['event_id'=>$id, 'schedule'=>$schedule->name, 'user_role_id'=>$model->role_id])->one();
            if ($eor)
            {
                $eor->quantity += $model->quantity;
                $eor->save();
            }else{

                $eor = new \common\models\EventOfferRole();
                $eor->event_id = $id;
                $eor->user_role_id = $model->role_id;
                $eor->quantity = $model->quantity;
                
                $eor->schedule = $schedule->name;
                $eor->save();
            }

        }
        return $this->redirect(['view', 'id' => $id, '#'=>'tab-crew']);
    }

        public function actionUpdateSchedule($id)
    {
        $model = \common\models\EventSchedule::findOne($id);

        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            $model->event->updateSchedule();
            return true;
        }else{
            return $this->renderAjax('add_schedule', [
            'model'=>$model,
            'event_id'=>$model->event_id,
        ]);
        }
    }

    public function actionDeleteSchedule($id)
    {
        $model = \common\models\EventSchedule::findOne($id);
        $model->delete();
        $model->event->updateSchedule();
        //usunąć wszystkie role i samochody podpięte pod tę pozycję
        return $this->redirect(['view', 'id' => $model->event_id]);  
    }

    public function actionAddSchedule($id, $name="")
    {
        $model = new \common\models\EventSchedule();
        $model->event_id = $id;
        $model->name = $name;
        $model->position = \common\models\EventSchedule::find()->where(['event_id'=>$id])->count();

        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add_schedule', [
            'model'=>$model,
            'event_id'=>$id,
        ]);
        }
    }



    public function actionConflictModal($conflict, $c)
    {
        $conflict = EventConflict::findOne($conflict);
        $settings = \common\models\Settings::find()->indexBy('key')->where(['section'=>'main'])->all();

        $names = explode(" ", $conflict->gear->name);
        $models1 = \common\models\GearModel::find();
        $models2 = \common\models\GearModel::find();
        foreach($names as $name)
        {
            if (strlen($name)>2)
            {
                $models1->andWhere(['like', 'name', $name]);
                $models2->orWhere(['like', 'name', $name]);
            }

        }
        $ids1 = \common\helpers\ArrayHelper::map($models1->asArray()->all(), 'id', 'id');
        $ids2 = \common\helpers\ArrayHelper::map($models2->asArray()->all(), 'id', 'id');
        //echo var_dump($ids2);
                $crn_event = [];
                $crn_event2 = [];
                $crn_event3 = [];
        $company = \common\models\Company::find()->where(['code'=>Yii::$app->params['companyID']])->one();
        $lat_min = $company->latitude-0.2;
        $lat_max = $company->latitude+0.2;
        $lon_min = $company->longitude-0.2;
        $lon_max = $company->longitude+0.2;
        $crn_warehouse = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
        ->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
        $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
        $crn_warehouse2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
        ->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
        if (isset($conflict->event->location)){
            if ($conflict->event->location!=$settings['companyCity']->value){
                $location = $conflict->event->location;
                        $lat_min = $location->latitude-0.2;
                        $lat_max = $location->latitude+0.2;
                        $lon_min = $location->longitude-0.2;
                        $lon_max = $location->longitude+0.2;
                $crn_event = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                ->andWhere(['>', 'latitude', $lat_min])
                ->andWhere(['<', 'latitude', $lat_max])
                ->andWhere(['>', 'longitude', $lon_min])
                ->andWhere(['<', 'longitude', $lon_max])
                ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
                $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                    ->andWhere(['>', 'latitude', $lat_min])
                    ->andWhere(['<', 'latitude', $lat_max])
                    ->andWhere(['>', 'longitude', $lon_min])
                    ->andWhere(['<', 'longitude', $lon_max])
                    ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
                $crn_event2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
                ->andWhere(['>', 'latitude', $lat_min])
                ->andWhere(['<', 'latitude', $lat_max])
                ->andWhere(['>', 'longitude', $lon_min])
                ->andWhere(['<', 'longitude', $lon_max])
                ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
            }
        }else{
            if ($conflict->event->address!="")
            {
                if (!strstr($conflict->event->address, $settings['companyCity']->value)){
                    $address = $conflict->event->address;
                $to =  $address;
                $to = urlencode($to);
                $apiKey= "AIzaSyAPDBOEfgjSaEHEiC8Zx3BpV5lT_cIRiBQ";  


                $data = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$to."&key=".$apiKey);
                $data = json_decode($data, true);

                if ($data['status']=="OK")
                {
                    if (isset($data['results'][0]))
                    {
                       $r = $data['results'][0];
                       $latitude = $r['geometry']['location']['lat'];
                       $longitude = $r['geometry']['location']['lng'];
                       $lat_min = $latitude-0.2;
                        $lat_max = $latitude+0.2;
                        $lon_min = $longitude-0.2;
                        $lon_max = $longitude+0.2;
                        $crn_event = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                        ->andWhere(['>', 'latitude', $lat_min])
                        ->andWhere(['<', 'latitude', $lat_max])
                        ->andWhere(['>', 'longitude', $lon_min])
                        ->andWhere(['<', 'longitude', $lon_max])
                        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
                        $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                            ->andWhere(['>', 'latitude', $lat_min])
                            ->andWhere(['<', 'latitude', $lat_max])
                            ->andWhere(['>', 'longitude', $lon_min])
                            ->andWhere(['<', 'longitude', $lon_max])
                            ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
                        $crn_event2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
                        ->andWhere(['>', 'latitude', $lat_min])
                        ->andWhere(['<', 'latitude', $lat_max])
                        ->andWhere(['>', 'longitude', $lon_min])
                        ->andWhere(['<', 'longitude', $lon_max])
                        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
                    }
                }
                
            }
            }
        }
        $used_ids = [];
        foreach ($crn_warehouse as $c)
        {
            $used_ids[] = $c->id;
        }
        foreach ($crn_warehouse2 as $c)
        {
            $used_ids[] = $c->id;
        }
        foreach ($crn_event as $c)
        {
            $used_ids[] = $c->id;
        }
        foreach ($crn_event2 as $c)
        {
            $used_ids[] = $c->id;
        }
        $crn_all = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
                        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->andWhere(['NOT IN', 'id', $used_ids])->all();
        return $this->renderAjax('conflict-modal', [
                'category' => $c,
                'conflict'=>$conflict,
                'crn'=>['cw'=>$crn_warehouse, 'cw2'=>$crn_warehouse2, 'ce'=>$crn_event, 'ce2'=>$crn_event2, 'ceall'=>$crn_all]
            ]);
    }

    public function actionDeleteFromProject($id)
    {
        $model = $this->findModel($id);
        $project_id = $model->project_id;
        $model->project_id = null;
        $model->save();
        return $this->redirect(['/project/view', 'id' => $project_id, '#'=>'tab-event']);
    }

    public function actionGetPurchaseItems($id)
    {
        $items= \common\models\PurchaseListItem::find()->where(['event_id'=>$id])->orderBy(['status'=>SORT_ASC])->all();
        return $this->renderAjax('get-purchase-items', [
                'items' => $items,
            ]);
    }

    public function actionEditProvisionGroup($id)
    {
        $model = \common\models\EventProvisionGroup::findOne($id);
        if (Yii::$app->request->post('EventProvisionGroupProvision')){
            if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
                $eventlog = new EventLog;
                $eventlog->event_id = $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Edytowano prowizje.");
                $eventlog->save();
                return $this->redirect(['view', 'id' => $model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->renderAjax('update_provision', [
                'model' => $model,
            ]);
        }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                            $eventlog = new EventLog;
                $eventlog->event_id = $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Edytowano prowizje.");
                $eventlog->save();
            return $this->redirect(['view', 'id' => $model->event_id, '#'=>'tab-finances']);
        } else {
            return $this->renderAjax('update_provision', [
                'model' => $model,
            ]);
        }
    }

    public function actionCopyProvisions($id)
    {
        $model = $this->findModel($id);
        $model->copyProvisionGroups();
        $model->copyProvisions();
                        $eventlog = new EventLog;
                $eventlog->event_id = $id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Edytowano prowizje.");
                $eventlog->save();
        return $this->redirect(['view', 'id'=>$id, '#'=>'tab-finances']);
    }

    public function actionSaveField($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $val = Yii::$app->request->post("val");
        $field = Yii::$app->request->post("field_id");
        $ef = \common\models\EventField::find()->where(['event_field_setting_id'=>$field])->andWhere(['event_id'=>$id])->one();
        if (!$ef){
            $ef = new \common\models\EventField();
            $ef->event_field_setting_id=$field;
            $ef->event_id = $id;
        }
        if ($ef->eventFieldSetting->type==1)
            $ef->value_int = $val;
        else
            $ef->value_text = $val;
        $success = $ef->save();
        $output = ['success'=>$success];
        return $output;
    }

    public function actionCopyFrom($id, $type)
    {
        $model = new \backend\models\CopyFromEventForm();
        $model->event_to = $id;
        $model->type = $type;
        return $this->renderAjax('copy-from', ['model'=>$model]);
    }

    public function actionCopyFromSave($id, $type)
    {
        $model = new \backend\models\CopyFromEventForm();
        $model->event_copy = Yii::$app->request->post("event_copy");
        $model->event_to = $id;
        $model->type = $type;
        $event = Event::findOne($id);
        if ($type == "gear")
        {
                    $gears = EventGear::find()->where(['event_id'=>$model->event_copy])->all();
                    $output = [];
                    /*foreach ($gears as $gear)
                    {
                            $r = Event::assignGearCon($id, $gear->gear_id, $gear->quantity);
                            $output[] = ['gear'=>$gear, 'result'=>$r['result'], 'conflict'=>$r['conflict']];
                    }*/
                    $output_outer = [];
                    $output_extra = [];

                    $gears = \common\models\EventExtraItem::find()->where(['event_id'=>$model->event_copy])->all();
                    foreach ($gears as $gear)
                    {
                        $eei = new \common\models\EventExtraItem();
                        $eei->attributes = $gear->attributes;
                        $eei->event_id = $id;
                        $eei->save();
                        $output_extra[] = $eei;
                    }
                    $gears = \common\models\EventOuterGearModel::find()->where(['event_id'=>$model->event_copy])->all();
                    foreach ($gears as $gear)
                    {
                        $eogm = new \common\models\EventOuterGearModel();
                        $eogm->attributes = $gear->attributes;
                        $eogm->event_id = $id;
                        $eogm->start_time = $event->getTimeStart();
                        $eogm->end_time = $event->getTimeEnd();
                        $eogm->save(); 
                        $output_outer[] = $eogm;
                    }
                    $packlists = \common\models\Packlist::find()->where(['event_id'=>$model->event_copy])->all();
                    foreach ($packlists as $p)
                    {
                        
                        $pack = \common\models\Packlist::find()->where(['name'=>$p->name, 'event_id'=>$id])->one();
                        if (!$pack)
                        {
                            $pack = new \common\models\Packlist();
                            $pack->attributes = $p->attributes;
                            $pack->event_id = $id;
                            $pack->start_time = $event->getTimeStart();
                            $pack->end_time = $event->getTimeEnd();
                            $pack->save();
                        }

                        $gears = \common\models\PacklistGear::find()->where(['packlist_id'=>$p->id])->all();
                        foreach ($gears as $gear)
                        {
                            /*$g = \common\models\EventGear::find()->where(['event_id'=>$id])->andWhere(['gear_id'=>$gear->eventGear->gear_id])->one();
                            if ($g){
                                $pg = new \common\models\PacklistGear();
                                $pg->attributes = $gear->attributes;
                                $pg->event_gear_id = $g->id;
                                $pg->packlist_id = $pack->id;
                                $pg->save();
                            }*/
                            $conflict = Event::assignGearToPacklist($pack->id, $gear->gear_id, $gear->quantity, $pack->start_time, $pack->end_time, 0);
                            if (!$conflict)
                            {
                                //brakuje sprzętów robimy konflikt
                                //sprawdzamy ile jest dostępnych
                                $available = $gear->gear->getAvailabe($pack->start_time, $pack->end_time);
                                $model = new PacklistGear();
                                $model->packlist_id = $pack->id;
                                $model->gear_id = $gear->gear_id;
                                $model->quantity = $available;
                                $model->start_time = $pack->start_time;
                                $model->end_time = $pack->end_time;
                                $model->save();
                                $model2 = new EventConflict();
                                $model2->event_id = $id;
                                $model2->gear_id = $gear->gear_id;
                                $model2->packlist_gear_id = $model->id;
                                $model2->quantity = $gear->quantity - $model->quantity;
                                $model2->added = $model->quantity;
                                $model2->save();
                                $output[] = ['gear'=>$gear, 'result'=>1, 'conflict'=>$model2];
                            }else{
                                //dodane
                                $output[] = ['gear'=>$gear, 'result'=>1, 'conflict'=>0];
                            }
                            
                        }
                    }

                    return $this->renderAjax('copy-from-save', ['output'=>$output, 'id'=>$id, 'output_extra'=>$output_extra, 'output_outer'=>$output_outer, 'output_users'=>null, 'output_users_not'=>null]);
        }

        if ($type =="crew")
        {
            $users = EventUser::find()->where(['event_id'=>$model->event_copy ])->all();
            foreach ($users as $user)
            {
                $u = new EventUser();
                $u->user_id = $user->user_id;
                $u->event_id = $id;
                $u->start_time = $event->getTimeStart();
                $u->end_time = $event->getTimeEnd();
                $u->save();
            }
            $output_users = [];
            $output_users_not = [];
            $hours = \common\models\EventUserPlannedWrokingTime::find()->where(['event_id'=>$model->event_copy ])->all();
            foreach ($hours as $h)
            {
                $eu = EventUser::find()->where(['event_id'=>$id ])->andWhere(['user_id'=>$h->user_id])->one();
                if ($eu)
                {
                        $hour = new \common\models\EventUserPlannedWrokingTime();
                        $hour->attributes = $h->attributes;
                        $hour->event_id = $id;
                        $schedule = \common\models\EventSchedule::find()->where(['name'=>$h->eventSchedule->name])->andWhere(['event_id'=>$id])->one();
                        if ($schedule)
                        {
                            $hour->start_time = $schedule->start_time;
                            $hour->end_time = $schedule->end_time;
                            $hour->event_schedule_id = $schedule->id;
                            if ($h->user->isAvailableInRange($hour->start_time, $hour->end_time))
                            {
                                if ($hour->save())
                                {
                                    $roles = \common\models\EventUserRole::find()->where(['working_hours_id'=>$h->id])->all();
                                    $output_users[] = $hour;
                                    foreach ($roles as $r)
                                    {
                                        
                                        $role = new \common\models\EventUserRole();
                                        $role->working_hours_id = $hour->id;
                                        $role->user_event_role_id = $r->user_event_role_id;
                                        $role->event_user_id = $eu->id;
                                        $role->save();
                                    }

                                }
                            }else{
                                $output_users_not[] = $hour;
                            }
                        }
                        

                }

            }
            return $this->renderAjax('copy-from-save', ['output'=>null, 'id'=>$id, 'output_extra'=>null, 'output_outer'=>null, 'output_users'=>$output_users, 'output_users_not'=>$output_users_not]);
        }

        exit;
    }
    public function actionSaveGearComment($gear_id)
    {
        if (Yii::$app->request->post())
        {
        $eg = PacklistGear::findOne($gear_id);
        $eg->comment = Yii::$app->request->post('PacklistGear')['comment'];
        $eg->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $output = ['output'=>$eg->comment, 'ok'=>1];
        return $output;
        }else{
            $eg = PacklistGear::findOne($gear_id);
            return $this->renderAjax('save-gear-comment', ['model'=>$eg]);
        }
        exit;

    }

    public function actionCreateProdEvent($event_id)
    {
        $id = Yii::$app->request->post('itemid');
        $quantity = Yii::$app->request->post('quantity');
        $model = \common\models\EventExtraItem::find()->where(['event_id'=>$event_id, 'offer_extra_item_id'=>$id])->one();
        $oei = \backend\modules\offers\models\OfferExtraItem::findOne($id);
        if ($model)
        {
            $model->quantity+=$quantity;
            $model->save();
        }else{
            
            $model = new \common\models\EventExtraItem();
            $model->event_id = $event_id;
            $model->offer_extra_item_id = $id;
            $model->quantity = $quantity;
            $model->name = $oei->name;
            $model->gear_category_id = $oei->category_id;
            $model->weight = $oei->weight*$quantity;
            $model->volume = $oei->volume*$quantity;
            $model->save();

        }


            $event = new Event();
            $event->type = 2;
            $event->customer_id = 1;
            $event->name = $model->name;
            $event->save();
            $task = new \common\models\Task();
            $task->title = $event->name;
            $task->event_id = $event_id;
            $task->save();
            $et = new \common\models\EventTask(['task_id'=>$task->id, 'event_id'=>$event->id]);
            $et->save();
            //dodajemy sprzęt zewnętrzny
            foreach ($oei->offerOuterGears as $gear)
            {
                            Event::assignOuterGearModel($event->id, $gear->outer_gear_model_id, $gear->quantity);
                            $eogm = \common\models\EventOuterGearModel::findOne(['event_id'=>$event->id, 'outer_gear_model_id'=>$gear->outer_gear_model_id]);
                            $ids = ArrayHelper::map(\common\models\OuterGear::find()->where(['outer_gear_model_id'=>$gear->outer_gear_model_id])->asArray()->all(), 'id', 'id');
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

            }
            //dodajemy koszty dodatkowe

            foreach ($oei->offerExtraCosts as $cost)
            {
                $expense = new \common\models\EventExpense();
                $expense->name = $cost->name;
                $expense->event_id = $event->id;
                $expense->sections = [$cost->section];
                $expense->amount = $cost->quantity*$cost->cost;
                $expense->save();
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = [
                'success'=>1,
                'error'=>'',
                'connected'=>[],
                'outerconnected'=>[]
            ];
            return $response;
            exit;
            
        
    }

    public function actionAddProd($id, $type)
    {
        $event = new Event();
        $event->type = $type;
        $event->customer_id = 1;
        if (Yii::$app->request->post() && $event->load(Yii::$app->request->post()))
        {
            $event->save();
            $task = new \common\models\Task();
            $task->title = $event->name;
            $task->event_id = $id;
            $task->save();
            $et = new \common\models\EventTask(['task_id'=>$task->id, 'event_id'=>$event->id]);
            $et->save();
            return $this->renderAjax('small-event-task', ['event'=>$event]);

        }else{
            return $this->renderAjax('add-prod', ['model'=>$event, 'id'=>$id]);
        }
    }

    public function actionAddProdTask($id)
    {
        $task = new \common\models\Task();
        $task->event_id = $id;
        if (Yii::$app->request->post() && $task->load(Yii::$app->request->post()))
        {
            $task->save();
            return $this->renderAjax('small-task-event', ['task'=>$task]);

        }else{
            return $this->renderAjax('add-prod-task', ['model'=>$task, 'id'=>$id]);
        }
    }

    public function actionPrintTasks($event_id, $category_id)
    {
        $event = Event::findOne($event_id);
        $date = $event->name;
        if ($category_id)
            $tasks = \common\models\Task::find()->where(['task_category_id'=>$category_id])->all();
        else
            $tasks = \common\models\Task::find()->where(['task_category_id'=>null])->andWhere(['event_id'=>$event_id])->all();
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf_plan', [
            'events' => [],
            'tasks' => $tasks,
            'settings' => $settings,
            'date' => $event->name
        ]);
        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
            'settings' => $settings
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
                'options' => ['title' => 'Zadania - '.$date],
                'filename' => Inflector::slug($date.'-tasks').'.pdf',
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();
    }

    public function actionDayPlan($day)
    {
        $date = $day;
        $events = Event::find()->where(['type'=>2])->andWhere(['<', 'event_start', $date." 23:59:59"])->andWhere(['>', 'event_end', $date])->all();
        $ids = ArrayHelper::map(\common\models\EventTask::find()->asArray()->all(), 'event_id', 'event_id');
        $event_ids = ArrayHelper::map(Event::find()->where(['type'=>2])->asArray()->all(), 'id', 'id');
        $tasks = \common\models\Task::find()->where(['event_id'=>$event_ids])->andWhere(['<', 'from', $date." 23:59:59"])->andWhere(['>', 'datetime', $date])->all();
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf_plan', [
            'events' => $events,
            'tasks' => $tasks,
            'settings' => $settings,
            'date' => $date
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
                'marginTop' => 15,
                'marginBottom' => 10,
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
                'options' => ['title' => 'Produkcja - '.$date],
                'filename' => 'packing-list-'.Inflector::slug($date.'-produkcja').'.pdf',
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();        
    }

    public function actionPrintProdTasks($id)
    {
        $event = Event::findOne($id);
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf_tasks', [
            'model' => $event,
            'settings' => $settings,
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
            'settings' => $settings
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
                'options' => ['title' => 'Produkcja - '.$event->name],
                'filename' => 'packing-list-'.Inflector::slug($event->name.'-produkcja').'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();
    }

    public function actionGetEventsForCalendar($start, $end)
    {
        $start = explode("-", $start);
        $end = explode("-", $end);
        $d = mktime(0,0,0, $start[1], $start[2], $start[0]);
        $start = date("Y-m-d H:i:s", $d);
        $d2 = mktime(0,0,0, $end[1], $end[2], $end[0]); 
        $end = date("Y-m-d H:i:s", $d2);
        $model = new \common\models\form\CalendarSearch();
        $model->start = $start;
        $model->end = $end;
        $model->search([]);
        $events2 = $model->getEvents();
        $rents = $model->getRents();
        $meetings = $model->getMeetings();
        $eventsArray = []; 
        if ($events2) 
            foreach ($events2 as $event)
            {
                foreach ($event->eventSchedules as $schedule){
                    $eventsArray[] = $schedule->prepareForCalendar();
                }
                
            }
        if ($rents)
            foreach ($rents as $event)
            {
                $eventsArray[] = $event->prepareForCalendar();
            }
            if ($meetings)
            foreach ($meetings as $event)
            {
                $eventsArray[] = $event->prepareForCalendar();
            }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $eventsArray;
    }

    public function actionPacklistPdf($id, $packlist_id, $sort, $money=false)
    {
        $event = Event::findOne($id);
        $packlist = Packlist::findOne($packlist_id);
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf_packlist', [
            'model' => $event,
            'settings' => $settings,
            'packlist' =>$packlist,
            'money'=>$money,
            'sort'=>$sort
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
            'settings' => $settings
        ]);
        $footerSize = $settings['footerSize']->value;
        if ((!$footerSize)||($footerSize==""))
            $footerSize = 30;
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
                'marginTop' => 45,
                'marginBottom' => $footerSize,
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
                'options' => ['title' => 'PackLista-'.$event->name.'-'.$packlist->name],
                'filename' => 'packing-list-'.Inflector::slug($event->name.'-'.$packlist->name).'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();
    }

    public function actionBlockPacklist($id, $packlist_id, $type)
    {
        $p = Packlist::findOne($packlist_id);
        $p->blocked = $type;
        $p->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['ok'=>1];
    }

    public function actionPacklistDelete($id, $packlist_id)
    {
        Packlist::findOne($packlist_id)->delete();
        return $this->redirect(['view', 'id' => $id]);
    }
    public function actionSavePacklistModalConflict($id, $packlist_id, $packlist_from)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['ok'=>1];
    }

    public function actionSavePacklistModalOne($id, $packlist_id, $packlist_from)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['ok'=>1];
    }

    public function actionSavePacklistModal($id, $packlist_id, $packlist_from)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gears = Yii::$app->request->post("quantity");
        $errors = [];
        $successes = [];
        if ($gears){
            $packlist_from = \common\models\Packlist::findOne($packlist_from);
            $packlist_to = \common\models\Packlist::findOne($packlist_id);
            if (($packlist_from->start_time>$packlist_to->start_time)||($packlist_from->end_time<$packlist_to->end_time))
            {
                //dla każdego sprzętu sprawdzamy dostępność

                foreach ($gears as $id =>$quantity)
                {
                    $missing = 0;
                    $success = 1;
                    $pack_from = PacklistGear::findOne(['packlist_id'=>$packlist_from, 'event_gear_id'=>$id]);
                    $pack = PacklistGear::findOne(['packlist_id'=>$packlist_id, 'event_gear_id'=>$id]);
                    if ($pack)
                    {
                        $start = $pack->start_time;
                        $end = $pack->end_time;
                    }else{
                        $start = $packlist_to->start_time;
                        $end = $packlist_to->end_time;
                    }
                    if ($pack_from->start_time>$start)
                    {
                        //sprawdzamy dostępność
                        if ($end<$pack_from->start_time)
                        {
                            $available = $pack_from->gear->getAvailabe($start, $end);
                        }else{
                            $available = $pack_from->gear->getAvailabe($start, $pack_from->start_time);
                        }
                        
                        $available = $available-$pack_from->gear->getInService();
                        if ($available<$quantity)
                        {
                            $success = 0;
                            $m = $quantity-$available;
                            if ($missing<$m)
                                $missing = $m;
                        }
                    }
                    if ($pack_from->end_time<$end)
                    {
                        //sprawdzamy dostępność
                        if ($start>$pack_from->end_time)
                        {
                            $available = $pack_from->gear->getAvailabe($start, $end);
                        }else{
                            $available = $pack_from->gear->getAvailabe($pack_from->end_time, $end);
                        }
                        
                        $available = $available-$pack_from->gear->getInService();
                        if ($available<$quantity)
                        {
                            $success = 0;
                            $m = $quantity-$available;
                            if ($missing<$m)
                                $missing = $m;
                        }
                    }
                    if ($success)
                    {
                        
                        if (!$pack){
                            $pack = new PacklistGear(['packlist_id'=>$packlist_id, 'event_gear_id'=>$id, 'gear_id'=>$pack_from->gear_id, 'start_time'=>$packlist_to->start_time, 'end_time'=>$packlist_to->end_time]);
                            $pack->quantity = 0;
                        }
                        $old = $pack->quantity;
                        $pack->quantity = $quantity;
                        $pack->info = Yii::$app->request->post("info")[$id];
                        $pack->save();
                        if ($quantity==0)
                            $pack->delete();
                        
                        $pack_from->quantity+=$old-$quantity;
                        $pack_from->save();
                        if ($pack_from->quantity==0)
                            $pack_from->delete();
                        $successes[] = $pack_from;
                    }else{
                            $error['missing'] = $missing;
                            $error['gear'] = $pack_from;
                            $errors[] = $error;
                    }
                    

                }

            }else{
                foreach ($gears as $id =>$quantity)
                {
                    $pack_from = PacklistGear::findOne(['packlist_id'=>$packlist_from, 'event_gear_id'=>$id]);
                    $pack = PacklistGear::findOne(['packlist_id'=>$packlist_id, 'event_gear_id'=>$id]);
                    if (!$pack){
                        $pack = new PacklistGear(['packlist_id'=>$packlist_id, 'event_gear_id'=>$id, 'gear_id'=>$pack_from->gear_id, 'start_time'=>$packlist_to->start_time, 'end_time'=>$packlist_to->end_time]);
                        $pack->quantity = 0;
                    }
                    $old = $pack->quantity;
                    $pack->quantity = $old+$quantity;
                    $pack->info = Yii::$app->request->post("info")[$id];
                    $pack->save();
                    if ($quantity==0)
                        $pack->delete();
                    $pack = PacklistGear::findOne(['packlist_id'=>$packlist_from, 'event_gear_id'=>$id]);
                    $pack->quantity=$pack->quantity-$quantity;
                    $pack->save();
                    if ($pack->quantity==0)
                        $pack->delete();

                }
            }

        }

        $gears = Yii::$app->request->post("equantity");
        if ($gears)
        foreach ($gears as $id =>$quantity)
        {
            $pack = \common\models\PacklistExtra::findOne(['packlist_id'=>$packlist_id, 'event_extra_id'=>$id]);
            if (!$pack)
                $pack = new \common\models\PacklistExtra(['packlist_id'=>$packlist_id, 'event_extra_id'=>$id]);
            $pack->quantity += $quantity;
            $pack->info = Yii::$app->request->post("einfo")[$id];
            $pack->save();
            if ($quantity==0)
                $pack->delete();
            $pack = \common\models\PacklistExtra::findOne(['packlist_id'=>$packlist_from, 'event_extra_id'=>$id]);
            $pack->quantity=$pack->quantity-$quantity;
            $pack->save();
            if ($pack->quantity==0)
                $pack->delete();
        }
        $gears = Yii::$app->request->post("oquantity");
        if ($gears)
        foreach ($gears as $id =>$quantity)
        {
            $pack = \common\models\PacklistOuterGear::findOne(['packlist_id'=>$packlist_id, 'event_outer_gear'=>$id]);
            if (!$pack)
                $pack = new \common\models\PacklistOuterGear(['packlist_id'=>$packlist_id, 'event_outer_gear'=>$id]);
            $pack->quantity = $quantity;
            $pack->info = Yii::$app->request->post("oinfo")[$id];
            $pack->save();
            if ($quantity==0)
                $pack->delete();
            $pack_from = \common\models\PacklistOuterGear::findOne(['packlist_id'=>$packlist_from, 'event_outer_gear'=>$id]);
            $pack_from->quantity = $pack_from->quantity-$quantity;
            $pack_from->save();
            if ($pack_from->quantity==0)
                $pack_from->delete();
        }
        if (count($errors))
        {
                return ['ok'=>0, 'errors'=>$errors, 'successes'=>$successes];
        }else{
            return ['ok'=>1];
        }
        
        exit;
    }

    public function actionGetPacklistModal($id, $packlist_id)
    {
        $post = Yii::$app->request->post();
        $packlist = Packlist::findOne($post['packlist_id']);
        if (isset($post['all']))
        {
            $g_ids = ArrayHelper::map($packlist->packlistGears, 'event_gear_id', 'event_gear_id');
            $gears = EventGear::find()->where(['id'=>$g_ids])->all();
            $g_ids = ArrayHelper::map($packlist->packlistOuterGears, 'event_outer_gear', 'event_outer_gear');
            $ogears = EventOuterGear::find()->where(['outer_gear_id'=>$g_ids, 'event_id'=>$packlist->event_id])->all();
            $g_ids = ArrayHelper::map($packlist->packlistExtras, 'event_extra_id', 'event_extra_id');
            $extras = EventExtraItem::find()->where(['id'=>$g_ids])->all();
            $one = 1;
        }else{
            if (isset($post['gears'])){
                $gears = \common\models\PacklistGear::find()->where(['id'=>$post['gears']])->all();
            }
            
            else
            $gears = [];
            if (isset($post['ogears']))
                $ogears = \common\models\PacklistOuterGear::find()->where(['id'=>$post['ogears']])->all();
            else
                $ogears = [];
            if (isset($post['extra']))
                $extras = \common\models\PacklistExtra::find()->where(['id'=>$post['extra']])->all();
            else
                $extras = [];
            $one = 0;
        }
        
        return $this->renderAjax('packlist-modal', [
            'packlist'=>$packlist,
            'gears'=>$gears,
            'ogears'=>$ogears,
            'extras'=>$extras,
            'one'=>$one,
            'packlist_id'=> $packlist_id
        ]);
    }

    public function actionAddPacklist($id, $packlist_id=null)
    {
        $event = $this->findModel($id);
        if (!$packlist_id)
        {

            $model = new Packlist();
            $model->event_id = $id;
            $model->start_time = $event->event_start;
            $model->end_time = $event->event_end;
            $colors = ["#6aa84f", "#a64d79", "#e69138", "#6d9eeb", "#ff00ff", "#23c3c3"];
            $count = Packlist::find()->where(['event_id'=>$id])->count();
            $model->color = $colors[$count%6];
        }else{
            $model = Packlist::findOne($packlist_id);
        }
        
        if (Yii::$app->request->post())
        {
            if (($model->load(Yii::$app->request->post()))&&($model->save()))
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['ok'=>1];
            }else{
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['error'=>Yii::t('app', 'Błąd zapisu')];
            }
        }
        return $this->renderAjax('add-packlist', [
            'model'=>$model
        ]);

    }

    public function actionSaveReminders($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('send_reminders')=='true')
        {
            $model->send_reminders = 1;
        }else{
            $model->send_reminders = 0;
        }
        $model->save();
    }

    public function actionAddProdukcjaEvent($task_id, $type=2)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task = \common\models\Task::findOne($task_id);
        $event = new \common\models\Event();
        $event->name = $task->title;
        $event->customer_id = 1;
        $event->type = $type;
        $event->description = $task->content;
        if ($event->save())
        {
            $er = new \common\models\EventTask(['task_id'=>$task_id, 'event_id'=>$event->id]);
            $er->save();
            return $task;
        }
        return null;
    }

    public function actionAssignUsers($id)
    {
        $model = $this->findModel($id);
        $model->userIds = ArrayHelper::map(EventUser::find()->where(['event_id'=>$id])->asArray()->all(), 'user_id', 'user_id');
        if (Yii::$app->request->post())
        {
                EventUser::deleteAll(['event_id'=>$id]);
                if (isset(Yii::$app->request->post('Event')['userIds']))
                {
                    foreach (Yii::$app->request->post('Event')['userIds'] as $user_id)
                    {
                        $eu = new EventUser();
                        $eu->event_id = $id;
                        $eu->user_id = $user_id;
                        $eu->save();
                    }  
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                $success = $model->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;

        }
        return $this->renderAjax('assign-users', [
            'model'=>$model
        ]);
    }

    public function actionChangeStatusModal($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())&&$model->save())
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $success = $model->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;
        }
        return $this->renderAjax('change-status-modal', [
            'model'=>$model
        ]);
    }

    public function actionSaveCalendarDate()
    {
        $event = Event::findOne(Yii::$app->request->post("id"));
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($event)
        {
            if (Yii::$app->request->post("no-date"))
            {
                $event->event_start= null;
                $event->event_end= null;
                $event->save();
                $success = $event->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;
            }
            $date = Yii::$app->request->post("date_start");
            $dateArr = explode( "-", $date);

            if (Yii::$app->request->post("whole")==1)
            {
                $hourArr = explode( ":", Yii::$app->request->post("hour_start"));
                $d=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $event->event_start= date("Y-m-d H:i:s", $d);
                $d2=mktime($hourArr[0]+1, $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);  
                $event->event_end = date("Y-m-d H:i:s", $d2);
                $event->save();
                $success = $event->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;
            }else{
                
                $hourArr = explode( ":", Yii::$app->request->post("hour_start"));
                $d=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $date = Yii::$app->request->post("date_end");
                $dateArr = explode( "-", $date);
                $hourArr = explode( ":", Yii::$app->request->post("hour_end"));
                $d2=mktime($hourArr[0], $hourArr[1], $hourArr[2], $dateArr[1], $dateArr[2], $dateArr[0]);
                $event->event_start = date("Y-m-d H:i:s", $d);
                $event->event_end = date("Y-m-d H:i:s", $d2);
                $event->save();
                $success = $event->prepareForCalendar();
                //echo var_dump($success);
                return $success;
                exit;
            }
        }
        exit;
    }

    public function actionSaveSection($id, $section)
    {
        $section = Yii::$app->request->post('EventProvision')['section'];
        $model = EventProvision::find()->where(['event_id'=>$id])->andWhere(['section'=>$section])->one();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
                $eventlog = new EventLog;
                $eventlog->event_id = $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Edytowano prowizje.");
                $eventlog->save();
            $model->save(false);
        } else {
        }
        exit;
    }

    public function actionShowNotes($id)
    {
        $notes = \common\models\CustomerNote::find()->where(['event_id'=>$id])->all();
        return $this->renderAjax('show-notes', [
            'notes'=>$notes
        ]);
    }

    public function actionAddNote($id)
    {
        $event = $this->findModel($id);
        $model = new \common\models\CustomerNote();
        $model->event_id = $id;
        $model->customer_id = $event->customer_id;
        $model->contact_id = $event->contact_id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->datetime = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('/customer-note/_form', [
            'model'=>$model,
            'event_id'=>$id,
            'ajax'=>true
        ]);
        }

    }


    public function actionChangeStatus($event_id, $status)
    {
        $model = $this->findModel($event_id);
        $model->status = $status;
        $success = false;
        if ($model->save()){
            $success = true;
        }else{
            var_dump($model->errors);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $success;
        exit;
    }

    public function actionChangeAdditionalStatus($event_id, $status, $id)
    {
        \common\models\EventASResult::deleteAll(['event_id'=>$event_id, 'event_additional_statut_id'=>$id]);
        $model = new \common\models\EventASResult();
        $model->event_id = $event_id;
        $model->event_additional_statut_id = $id;
        $model->event_additional_statut_name_id = $status;
        if ($model->save()){
            $model->eventAdditionalStatutName->sendReminders($event_id);
            $success = true;
        }else{
            var_dump($model->errors);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $success;
        exit;
    }

    public function actionChangeAdditionalStatus2($id, $status)
    {
        $model = \common\models\EventASResult::find()->where(['event_id'=>$id, 'event_additional_statut_id'=>$status])->one();
        if (!$model)
        {
            $model = new \common\models\EventASResult();
            $model->event_id = $id;
            $model->event_additional_statut_id = $status;
        }

        if ($model->load(Yii::$app->request->post()))
        {
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model->eventAdditionalStatutName;
        }else{
            $s = \common\models\EventAdditionalStatut::findOne($status);
            return $this->renderAjax('_editAdditionalStatut', [
            's' => $s,
            'model' => $model
        ]);
        }
    }

    public function actionChangeScLevel($event_id, $status)
    {
        $model = $this->findModel($event_id);
        $model->scenography_level = $status;
        $success = false;
        if ($model->save()){
            $success = true;
        }else{
            var_dump($model->errors);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $success;
        exit;
    }

    public function actionEditProvision($id)
    {
        $sections = EventProvision::find()->where(['event_id'=>$id])->all();
        if (!$sections)
        {
            $model= Event::findOne($id);
            $model->copyProvisions();
            $sections = EventProvision::find()->where(['event_id'=>$id])->all();
        }
        return $this->renderAjax('_editProvisions', [
            'sections' => $sections,
        ]);
    }

    public function actionCountCosts()
    {
        $events = Event::find()->where(['>', 'event_start', '2018-10-01'])->all();
        foreach ($events as $event)
        {
            $event->saveEventCosts();
        }
        exit;
    }

    private function isEventUser($text) {
        $event = $this->findModel(Yii::$app->request->get('id'));
        if ($event->manager_id == Yii::$app->user->id) {
            return true;
        }
        if ($event->creator_id == Yii::$app->user->id) {
            return true;
        }
        if ($text == "eventEventEditEye" ){
            if (Yii::$app->user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            foreach ($event->users as $user) {
                if ($user->id == Yii::$app->user->id) {
                    return true;
                }
            }
            }
        }

        return false;
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $old = $model->status;
        $post = Yii::$app->request->post();
        $status = $post['Event'][$post['editableIndex']]['status'];
        $model->status = $status;
        $model->save();
        $list = \common\models\Event::getStatusList();
        //$model->addLog(Yii::t('app', 'Zmieniono status wydarzenia na:').$list[$model->status]);
        
        $output = ['output'=>$model->getStatusButton(), 'message'=>''];
        return $output;
        exit;
    }

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['eventparams'];
          } else {
            Yii::$app->session['eventparams'] = $params;
        }
        $dataProvider = $searchModel->search($params);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();

        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        if($model->load($post) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->refresh('#tab-description');
        }

        $workingTime = new EventUserWorkingTime([
            'user_id'=>Yii::$app->user->id,
            'event_id'=>$model->id,
        ]);
        $workingTime->loadLinkedObjects();
        if ($workingTime->load($post) && $workingTime->saveAndLink(true))
        {
            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Do eventu dodano godziny pracy.");
            $eventlog->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->refresh('#tab-working-time');
        }
        if ($model->eventModel->type==1)
        {
        return $this->render('view', [
            'model' => $model,
            'workingTime' => $workingTime,
        ]);            
    }else{
        if ($model->eventModel->type==3)
        {
        return $this->render('view_hall', [
            'model' => $model,
            'workingTime' => $workingTime,
        ]); }else{
        return $this->render('view-small2', [
            'model' => $model,
            'workingTime' => $workingTime,
        ]);
    }
    }

    }

    public function actionPackingList($id, $sort)
    {
        $model = $this->findModel($id);
        $pdf = $this->preparePDF($model, $sort);
        return $pdf->render();
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null, $project_id=null, $event_id=null, $type=null, $offer_id=null)
    {
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $event = null;
        $offer = null;
        $schema = \common\models\TasksSchema::find()->where(['type'=>2])->andWhere(['default'=>1])->one();

        $model = new Event();
        $model->manager_id = Yii::$app->user->identity->id;
        if ($type)
        {
            $model->type = $type;
        }
        $schedule = \common\models\ScheduleType::find()->orderBy(['default'=>SORT_DESC])->one();
        if ($schedule)
            $model->schedule_type = $schedule->id;
        if ($event_id)
        {
            $event = Event::findOne($event_id);
            $model->attributes = $event->attributes;
            $model->code = null;
            $model->number = null;
        }
        if ($offer_id)
        {
            $offer = \common\models\Offer::findOne($offer_id);
            $model->attributes = $offer->attributes;
            $model->code = null;
            $model->number = null;
        }
        if (Yii::$app->params['companyID']=="djak")
        {
                $model->details = '<table class="table table-bordered"><tbody><tr> <td colspan="2">TECHNICZNY/ELEKTRYK</td> </tr> <tr> <td style="width:200px;">techniczny elektryk</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td>mail.</td> <td> </td> </tr> <tr> <td>źródło zasilania</td> <td> </td> </tr> <tr> <td>rodzaj przyłącza</td> <td> </td> </tr> <tr> <td>odległość od sceny</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">SCENA</td> </tr> <tr> <td>scena</td> <td> </td> </tr> <tr> <td>osoba kontaktowa</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td>mail</td> <td> </td> </tr> <tr> <td>opis sceny</td> <td> </td> </tr> <tr> <td>kontak podniesienie dachu</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">HOTEL</td> </tr> <tr> <td>hotel</td> <td> </td> </tr> <tr> <td>adres</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">WYŻYWIENIE</td> </tr> <tr> <td>wyżywienie</td> <td> </td> </tr> <tr> <td>osoba kontaktowa</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td colspan="2">MULTIMEDIA</td> </tr> <tr> <td>Firma</td> <td> </td> </tr> <tr> <td>Telefon</td> <td> </td> </tr> <tr> <td>Kamery</td> <td> </td> </tr> <tr> <td>Realizacja</td> <td> </td> </tr> <tr> <td colspan="2">TRANSPORT</td> </tr> <tr> <td>Samochód</td> <td> </td> </tr> <tr> <td>Kierowca</td> <td> </td> </tr> <tr> <td>Telefon</td> <td> </td> </tr> <tr> <td colspan="2">SCENOGRAFIA</td> </tr>  <tr> <td>Firma</td> <td> </td> </tr> <tr> <td>Telefon</td> <td> </td> </tr> <tr> <td>Os. kontaktowa na miejscu</td> <td> </td> </tr>  <tr> <td colspan="2">INNE</td> </tr> <tr> <td>Ubiór</td> <td> </td> </tr></tbody></table>';
        }
        if (Yii::$app->params['companyID']=="admin")
        {
            $model->customer_id = 1; 
            $model->type = 2;
        }
        $model->project_id = $project_id;
        if ($schema)
            $model->tasks_schema_id = $schema->id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $schedules = Yii::$app->request->post('ScheduleForm');
            $type = Yii::$app->request->post('Event')['schedule_type'];
            $model->saveSchedule($type, $schedules['schedules'], $event, $offer);          
            $model->linkObjects();
            
            if (\common\models\PacklistSchema::find()->asArray()->all())
            {
                $model->createPacklists(Yii::$app->request->post('Event')['packlist_schema']);
            }else{
                $model->createPacklists();
            }
            
            
            $eventlog = new EventLog;
            $eventlog->event_id = $model->id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Utworzenie eventu.");
            $eventlog->save();
            if ($model->tasks_schema_id)
            {
                $model->copyTasks();
            }
            if (Yii::$app->params['companyID']=="admin")
            {
            if (isset(Yii::$app->request->post('Event')['userIds']))
            {
                if (Yii::$app->request->post('Event')['userIds'])
                {
                    foreach (Yii::$app->request->post('Event')['userIds'] as $user_id)
                    {
                        $eu = new EventUser();
                        $eu->event_id = $model->id;
                        $eu->user_id = $user_id;
                        $eu->save();
                    } 
                }
 
            }
            }
            if ($offer_id)
            {
                $offer->event_id = $model->id;
                $offer->save();
            }
            if ($model->eventModel->type==3)
            {
                return $this->redirect(['/hall-group/book', 'event_id' => $model->id]);
            }else{
                return $this->redirect(['view', 'id' => $model->id]);
            }
            
        } else {
            $model->prepareDateAttributes();
            return $this->render('create', [
                'model' => $model,
                'schema_change_possible' => true,
                'event'=>$event,
                'offer'=>$offer
            ]);
        }
    }

    public function actionGetAssignedGear($event_id)
    {
        $gears = EventGear::find()->where(['event_id'=>$event_id])->all();
        $conflicts = EventConflict::find()->where(['event_id'=>$event_id, 'resolved'=>0])->all();
        return $this->renderPartial('get-assigned-gear', [
                'gears' => $gears,
                'conflicts'=>$conflicts
            ]);
    }

    public function actionAreThereUserWorkingHoursConfilcts($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return false;
        $model = $this->findModel($id);
        $old_model = new Event();
        $old_model->attributes = $model->attributes;
        $model->loadLinkedObjects();

        if ($model->load(Yii::$app->request->post()) && $model->setDateAttributes()) {
            $new_packing_start = new DateTime(str_replace("/", "-", $model->packing_start));
            $new_packing_end = new DateTime(str_replace("/", "-", $model->packing_end));
            $old_packing_start = new DateTime($old_model->packing_start);
            $old_packing_end = new DateTime($old_model->packing_end);

            $new_event_start = new DateTime(str_replace("/", "-", $model->event_start));
            $new_event_end = new DateTime(str_replace("/", "-", $model->event_end));
            $old_event_start = new DateTime($old_model->event_start);
            $old_event_end = new DateTime($old_model->event_end);

            $new_montage_start = new DateTime(str_replace("/", "-", $model->montage_start));
            $new_montage_end = new DateTime(str_replace("/", "-", $model->montage_end));
            $old_montage_start = new DateTime($old_model->montage_start);
            $old_montage_end = new DateTime($old_model->montage_end);

            $new_disassembly_start = new DateTime(str_replace("/", "-", $model->disassembly_start));
            $new_disassembly_end = new DateTime(str_replace("/", "-", $model->disassembly_end));
            $old_disassembly_start = new DateTime($old_model->disassembly_start);
            $old_disassembly_end = new DateTime($old_model->disassembly_end);

            // jeżeli godziny pracy się zmieniły
            if ($new_event_start != $old_event_start || $new_event_end != $old_event_end || $new_montage_start != $old_montage_start || $new_montage_end != $old_montage_end || $new_disassembly_start != $old_disassembly_start || $new_disassembly_end != $old_disassembly_end || $new_packing_start != $old_packing_start || $new_packing_end != $old_packing_end) {

                // 1. there are custom working periods
                $all_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->count();
                $packing_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_packing_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_packing_end->format("Y-m-d H:i:00")])->count();
                $event_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_event_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_event_end->format("Y-m-d H:i:00")])->count();
                $montage_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_montage_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_montage_end->format("Y-m-d H:i:00")])->count();
                $disassembly_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_disassembly_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_disassembly_end->format("Y-m-d H:i:00")])->count();
                if ($all_working_periods != $event_working_periods + $montage_working_periods + $disassembly_working_periods + $packing_working_periods) {
                    return true;
                }

                // 2. pracownik ma zajęty czas
                // 3. pracownik pracuje w okresie zbliżonym
                // 4. pracownik ma urlop
                // 5. pracownik ma zaplanowany urlop
                foreach ($model->users as $user) {
                    if ($new_packing_start != $old_packing_start || $new_packing_end != $old_packing_end) {
                        $isWorking = Yii::$app->runAction('crew/is-working-in-close-range', ['user_id' => $user->id, 'start' => $new_packing_start->format("Y-m-d H:i:00"), 'end' => $new_packing_end->format("Y-m-d H:i:00"), 'event_id' => $model->id]);
                        if (in_array(1, $isWorking)) {
                            return true;
                        }
                    }

                    
                    if ($new_event_start != $old_event_start || $new_event_end != $old_event_end) {
                        $isWorking = Yii::$app->runAction('crew/is-working-in-close-range', ['user_id' => $user->id, 'start' => $new_event_start->format("Y-m-d H:i:00"), 'end' => $new_event_end->format("Y-m-d H:i:00"), 'event_id' => $model->id]);
                        if (in_array(1, $isWorking)) {
                            return true;
                        }
                    }
                    if ($new_montage_start != $old_montage_start || $new_montage_end != $old_montage_end) {
                        $isWorking = Yii::$app->runAction('crew/is-working-in-close-range', ['user_id' => $user->id, 'start' => $new_montage_start->format("Y-m-d H:i:00"), 'end' => $new_montage_end->format("Y-m-d H:i:00"), 'event_id' => $model->id]);
                        if (in_array(1, $isWorking)) {
                            return true;
                        }
                    }
                    if ($new_disassembly_start != $old_disassembly_start || $new_disassembly_end != $old_disassembly_end) {
                        $isWorking = Yii::$app->runAction('crew/is-working-in-close-range', ['user_id' => $user->id, 'start' => $new_disassembly_start->format("Y-m-d H:i:00"), 'end' => $new_disassembly_end->format("Y-m-d H:i:00"), 'event_id' => $model->id]);
                        if (in_array(1, $isWorking)) {
                            return true;
                        }
                    }
                }

            }
        }

        return false;
    }

    public function actionChangeUserWorkingHours($id) {
        $conflicts = [];

        $model = $this->findModel($id);
        $old_model = new Event();
        $old_model->attributes = $model->attributes;
        $model->loadLinkedObjects();

        $model->load(Yii::$app->request->get());
        $model->setDateAttributes();

        $new_event_start = new DateTime(str_replace("/", "-", $model->event_start));
        $new_event_end = new DateTime(str_replace("/", "-", $model->event_end));
        $old_event_start = new DateTime($old_model->event_start);
        $old_event_end = new DateTime($old_model->event_end);

        $new_montage_start = new DateTime(str_replace("/", "-", $model->montage_start));
        $new_montage_end = new DateTime(str_replace("/", "-", $model->montage_end));
        $old_montage_start = new DateTime($old_model->montage_start);
        $old_montage_end = new DateTime($old_model->montage_end);

        $new_disassembly_start = new DateTime(str_replace("/", "-", $model->disassembly_start));
        $new_disassembly_end = new DateTime(str_replace("/", "-", $model->disassembly_end));
        $old_disassembly_start = new DateTime($old_model->disassembly_start);
        $old_disassembly_end = new DateTime($old_model->disassembly_end);


        // 1. there are custom working periods
        $all_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->select('id')->asArray()->all();
        $event_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_event_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_event_end->format("Y-m-d H:i:00")])->select('id')->asArray()->all();
        $montage_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_montage_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_montage_end->format("Y-m-d H:i:00")])->select('id')->asArray()->all();
        $disassembly_working_periods = EventUserPlannedWrokingTime::find()->where(['event_id' => $model->id])->andWhere(['start_time' => $old_disassembly_start->format("Y-m-d H:i:00")])->andWhere(['end_time' => $old_disassembly_end->format("Y-m-d H:i:00")])->select('id')->asArray()->all();

        $diff = [];
        foreach ($all_working_periods as $period_id) {
            if (in_array($period_id, $event_working_periods)) {
                continue;
            }
            if (in_array($period_id, $montage_working_periods)) {
                continue;
            }
            if (in_array($period_id, $disassembly_working_periods)) {
                continue;
            }
            $diff[] = $period_id;
        }

        foreach ($diff as $arr) {
            foreach ($arr as $working_hour_id) {
                $conflicts[] = new ConflictUserWorkingHours(
                    ConflictUserWorkingHours::CUSTOM_WORKING_TIME,
                    EventUserPlannedWrokingTime::findOne($working_hour_id),
                    $model
                );
            }
        }

        // 2, 3, 4, 5
        $vacations = [];
        $planned_vacations = [];
        $is_working = [];
        $is_working_in_close_range = [];
        foreach ($model->users as $user) {

            // *** Urlopy *** //
            if ($new_event_start != $old_event_start || $new_event_end != $old_event_end) {
                $vac = CrewController::getUserVacationsInPeriod($user->id, $new_event_start, $new_event_end);
                $vacations = array_merge($vacations, $vac[0]);
                $planned_vacations = array_merge($planned_vacations, $vac[1]);
            }
            if ($new_montage_start != $old_montage_start || $new_montage_end != $old_montage_end) {
                $vac = CrewController::getUserVacationsInPeriod($user->id, $new_montage_start, $new_montage_end);
                $vacations = array_merge($vacations, $vac[0]);
                $planned_vacations = array_merge($planned_vacations, $vac[1]);
            }
            if ($new_disassembly_start != $old_disassembly_start || $new_disassembly_end != $old_disassembly_end) {
                $vac = CrewController::getUserVacationsInPeriod($user->id, $new_disassembly_start, $new_disassembly_end);
                $vacations = array_merge($vacations, $vac[0]);
                $planned_vacations = array_merge($planned_vacations, $vac[1]);
            }
            // *** Urlopy *** //

            // *** Pracujący w czasie, który się zmienia *** //
            foreach (EventUserPlannedWrokingTime::find()->where(['user_id' => $user->id])->andWhere(['<>', 'event_id', $model->id])->all() as $time) {
                $work_start = new DateTime($time->start_time);
                $work_end = new DateTime($time->end_time);

                if ($new_event_start != $old_event_start || $new_event_end != $old_event_end) {
                    if (!$this->isAvailableInRange($new_event_start, $new_event_end, $work_start, $work_end)) {
                        $is_working[] = $time;
                    }
                    if ($this->isWorkingInCloseRange($time, $new_event_start, $new_event_end)) {
                        $is_working_in_close_range[] = $time;
                    }
                }

                if ($new_montage_start != $old_montage_start || $new_montage_end != $old_montage_end) {
                    if (!$this->isAvailableInRange($new_montage_start, $new_montage_end, $work_start, $work_end)) {
                        $is_working[] = $time;
                    }
                    if ($this->isWorkingInCloseRange($time, $new_montage_start, $new_montage_end)) {
                        $is_working_in_close_range[] = $time;
                    }
                }

                if ($new_disassembly_start != $old_disassembly_start || $new_disassembly_end != $old_disassembly_end) {
                    if (!$this->isAvailableInRange($new_disassembly_start, $new_disassembly_end, $work_start, $work_end)) {
                        $is_working[] = $time;
                    }
                    if ($this->isWorkingInCloseRange($time, $new_disassembly_start, $new_disassembly_end)) {
                        $is_working_in_close_range[] = $time;
                    }
                }

            }
            // *** Pracujący w czasie, który się zmienia *** //
        }

        // 2. pracownik ma zajęty czas
        $work_done = [];
        foreach ($is_working as $work) {
            if (!in_array($work->id, $work_done)) {
                $work_done[] = $work->id;
                $conflicts[] = new ConflictUserWorkingHours(ConflictUserWorkingHours::ALREADY_WORKING, $work, $model, $work->event);
            }
        }

        // 3. pracuje w odstępie 12 godzin
        $work_close_range_done = [];
        foreach ($is_working_in_close_range as $work) {
            if (!in_array($work->id, $work_close_range_done) && !in_array($work->id, $work_done)) {
                $work_close_range_done[] = $work->id;
                $conflicts[] = new ConflictUserWorkingHours(ConflictUserWorkingHours::WORKING_IN_CLOSE_RANGE, $work, $model, $work->event);
            }
        }

        // 4. pracownik ma urlop
        $vacation_done = [];
        foreach ($vacations as $vacation) {
            if (!in_array($vacation->id, $vacation_done)) {
                $vacation_done[] = $vacation->id;
                $conflicts[] = new ConflictUserWorkingHours(ConflictUserWorkingHours::VACATIONS, null, $model, null, $vacation);
            }
        }

        // 5. pracownik ma zaplanowany urlop
        $vacation_planned_done = [];
        foreach ($planned_vacations as $vacation) {
            if (!in_array($vacation->id, $vacation_planned_done)) {
                $vacation_planned_done[] = $vacation->id;
                $conflicts[] = new ConflictUserWorkingHours(ConflictUserWorkingHours::PLANNED_VACATIONS, null, $model, null, $vacation);
            }
        }

        return $this->renderPartial('_change_working_hours', [
            'model' => $model,
            'old_model' => $old_model,

            'conflicts' => $conflicts
        ]);
    }

    private function isAvailableInRange($start, $end, $work_start, $work_end) {
        if ($work_start >= $start && $work_end <= $end ) {
            return false;
        }
        if ($work_start <= $start && $work_end >= $end) {
            return false;
        }
        if ($work_start <= $start && $work_end >= $start) {
            return false;
        }
        if ($work_start <= $end && $work_end >= $end) {
            return false;
        }
        return true;
    }

    private function isWorkingInCloseRange($time, $start, $end) {
        $work_start = new DateTime($time->start_time);
        $work_end = new DateTime($time->end_time);

        $test1 = clone($work_start)->add(new DateInterval('PT12H')) ;
        $test2 = clone($work_start)->sub(new DateInterval('PT24H'));
        $test3 = clone($work_end)->sub(new DateInterval('PT12H'));
        $test4 = clone($work_end)->add(new DateInterval('PT24H'));

        $isWorking = false;
        if ($test1 <= $start && $test2 >= $start) {
            $isWorking = true;
        }
        if ($test3 <= $start && $test4 >= $start) {
            $isWorking = true;
        }
        if ($test1 <= $end && $test2 >= $end) {
            $isWorking = true;
        }
        if ($test3 <= $end && $test4 >= $end) {
            $isWorking = true;
        }
        return $isWorking;
    }

    public function actionResolveConflicts() {
        if (isset($_POST['model']) && isset($_POST['selected_value'])) {
            foreach ($_POST['model'] as $i => $model) {
                /* @var $conflictUserWorkingHours \common\models\ConflictUserWorkingHours  */
                $conflictUserWorkingHours = unserialize(gzinflate(base64_decode($model)));
                $conflictUserWorkingHours->selected_value = $_POST['selected_value'][$i];
                if (!$conflictUserWorkingHours->resolveConflict() ) {
                    return false;
                }
            }
        }
        return true;
	}

    public function actionCost($id)
    {
        $model = $this->findModel($id);
        $model->expense_entered = 1;
        $model->expense_entered_user_id = Yii::$app->user->identity->id;
        $model->save();
                $notification = Notification::getByName(Notification::COSTS_ADDED);
                $users = $notification->getRecipients()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                foreach ($users as $user)
                {
                    $notification->sendUserNotifications($user, Notification::COSTS_ADDED, [$model]);  
                }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionInvoiceReady($id)
    {
        $model = $this->findModel($id);
        $model->ready_to_invoice = 1;
        $model->ready_to_invoice_user_id = Yii::$app->user->identity->id;
        $model->ready_to_invoice_date = date('Y-m-d H:i:s');
        $model->save();
                $notification = Notification::getByName(Notification::READY_TO_INVOICE);
                $users = $notification->getRecipients()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                foreach ($users as $user)
                {
                    $notification->sendUserNotifications($user, Notification::READY_TO_INVOICE, [$model]);  
                }       
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionEditName($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $tasks = ArrayHelper::map(\common\models\EventTask::find()->where(['event_id'=>$id])->asArray()->all(), 'task_id', 'task_id');
            $tasks = \common\models\Task::find()->where(['id'=>$tasks])->all();
            foreach ($tasks as $task)
            {
                $task->title = $model->name;
                $task->save();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model;
        }else{
            return $this->renderAjax('edit-name', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_model = new Event;
        $old_model->attributes = $model->attributes;
        $model->loadLinkedObjects();
        $schema_change_possible = true;
        $tasks = \common\models\Task::find()->where(['event_id'=>$model->id])->andWhere(['status'=>10])->count();
        if ($tasks)
            $schema_change_possible = false;
        if (($schema_change_possible)&&(!$model->tasks_schema_id)){
            $schema = \common\models\TasksSchema::find()->where(['type'=>2])->andWhere(['default'=>1])->one();
            if ($schema)
                $model->tasks_schema_id = $schema->id;
        }
            
        if ($model->load(Yii::$app->request->post()) && $model->setDateAttributes() && $model->save()) {
            $model->linkObjects();
            if (($model->tasks_schema_id)&&($schema_change_possible)&&($old_model->tasks_schema_id!=$model->tasks_schema_id))
            {
                $model->deleteAllTasks();
                $model->copyTasks();
            }
            if ($this->datesChanged($old_model, $model)) {
                $model->changeVehicleDates($old_model);
                
                foreach ($model->users as $user) {
                    $workingTimes = EventUserPlannedWrokingTime::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $model->id])->all();
                    foreach ($workingTimes as $workingTime) {
                        if ($old_model->event_start != $model->event_start || $old_model->event_end != $model->event_end) {
                            if ($workingTime->start_time == $old_model->event_start && $workingTime->end_time == $old_model->event_end) {
                                 if ($model->event_start){
                                    $workingTime->start_time = $model->event_start;
                                    $workingTime->end_time = $model->event_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }
                        if ($old_model->packing_start != $model->packing_start || $old_model->packing_end != $model->packing_end) {
                            if ($workingTime->start_time == $old_model->packing_start && $workingTime->end_time == $old_model->packing_end) {
                                if ($model->packing_start){
                                $workingTime->start_time = $model->packing_start;
                                $workingTime->end_time = $model->packing_end;
                                $workingTime->save();
                            }else{
                                $workingTime->delete();
                            }
                            }
                        }
                        if ($old_model->montage_start != $model->montage_start || $old_model->montage_end != $model->montage_end) {
                            if ($workingTime->start_time == $old_model->montage_start && $workingTime->end_time == $old_model->montage_end) {
                                if ($model->montage_start){
                                    $workingTime->start_time = $model->montage_start;
                                    $workingTime->end_time = $model->montage_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }
                        if ($old_model->disassembly_start != $model->disassembly_start || $old_model->disassembly_end != $model->disassembly_end) {
                            if ($workingTime->start_time == $old_model->disassembly_start && $workingTime->end_time == $old_model->disassembly_end) {
                                if ($model->disassembly_start){
                                    $workingTime->start_time = $model->disassembly_start;
                                    $workingTime->end_time = $model->disassembly_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }

                    }
                    
                }
                $notification = Notification::getByName(Notification::EVENT_SCHEDULE_CHANGE);
                $users = $model->getAssignedUsers()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                if (isset($model->manager)) {
                    $notification->addUserNotification([$model->manager], ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                    Notification::sendUserNotifications($model->manager, Notification::EVENT_SCHEDULE_CHANGE, [$model]);

                }
                $eventlog = new EventLog;
                $eventlog->event_id = intval($id);
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Zmiana harmonogramu eventu.");
                $eventlog->save();
                //zmieniły się daty - sprawdzamy dostępności sprzętu
                $eventGears = EventGear::find()->where(['event_id'=>$model->id])->all();
                foreach ($eventGears as $eg)
                {
                    $count = $eg->gear->getAvailableDateChanged($model->getTimeStart(), $model->getTimeEnd(), $model->id, 'event');
                    if ($count<$eg->quantity)
                    {
                                $reverse = true;
                                $missing = $eg->quantity-$count;
                                $dateError[] = ['gear'=>$eg->gear, 'missing'=>$eg->quantity-$count];
                                $eg->quantity = $count;

                                $eg->start_time = $model->getTimeStart();
                                $eg->end_time = $model->getTimeEnd();
                                $eg->save();
                                $conflict = EventConflict::find()->where(['event_id'=>$model->id])->andWhere(['gear_id'=>$eg->gear_id])->one();
                                if (!$conflict)
                                {
                                    $conflict = new EventConflict;
                                    $conflict->event_id = $model->id;
                                    $conflict->gear_id = $eg->gear_id;
                                }
                                $conflict->quantity = $missing;
                                $conflict->added = $count;
                                $conflict->resolved = 0;
                                $conflict->save();
                                $item_ids = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$eg->gear_id])->asArray()->all(), 'id', 'id');
                                $items = EventGearItem::find()->where(['event_id'=>$model->id])->andWhere(['in', 'gear_item_id', $item_ids])->all();
                                foreach ($items as $item)
                                {
                                    $item->delete();
                                }
                    }else{
                                $eg->start_time = $model->getTimeStart();
                                $eg->end_time = $model->getTimeEnd();
                                $eg->save();                        
                    }
                }



            }else{
                $eventlog = new EventLog;
                $eventlog->event_id = intval($id);
                $eventlog->user_id = Yii::$app->user->identity->id;
                if ($old_model->description!=$model->description)
                {
                    $eventlog->content = Yii::t('app', "Zmiana opisu eventu.");
                }else{
                    $eventlog->content = Yii::t('app', "Zmiana danych podstawowych eventu.");
                     
                }
                   
                $eventlog->save();

            }
            if (($old_model->expense_entered!=$model->expense_entered)&&($model->expense_entered==1))
            {
                $notification = Notification::getByName(Notification::COSTS_ADDED);
                $users = $notification->getRecipients()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                foreach ($users as $user)
                {
                    Notification::sendUserNotifications($user, Notification::COSTS_ADDED, [$model]);
                }
            }
            if (($old_model->ready_to_invoice!=$model->ready_to_invoice)&&($model->ready_to_invoice==1))
            {
                $notification = Notification::getByName(Notification::READY_TO_INVOICE);
                $users = $notification->getRecipients()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                foreach ($users as $user)
                {
                    Notification::sendUserNotifications($user, Notification::READY_TO_INVOICE, [$model]);
                }                
            }
            
            if (Yii::$app->request->isAjax)
            {
                Yii::$app->end();
            }
            Yii::$app->session->addFlash('success', Yii::t('app', 'Zapisano!'));
            if (Yii::$app->params['companyID']=="admin")
            {
                EventUser::deleteAll(['event_id'=>$id]);
                if (isset(Yii::$app->request->post('Event')['userIds']))
                {
                    if (Yii::$app->request->post('Event')['userIds'])
                    {
                    foreach (Yii::$app->request->post('Event')['userIds'] as $user_id)
                    {
                        $eu = new EventUser();
                        $eu->event_id = $model->id;
                        $eu->user_id = $user_id;
                        $eu->save();
                    } 
                    } 
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->prepareDateAttributes();
            $model->userIds = ArrayHelper::map(EventUser::find()->where(['event_id'=>$id])->asArray()->all(), 'user_id', 'user_id');
            return $this->render('update', [
                'model' => $model,
                'schema_change_possible' => $schema_change_possible
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        foreach (EventBreaks::find()->where(['event_id' => $this->id])->all() as $break) {
            EventBreaksUser::deleteAll(['event_break_id' => $break->id]);
            $break->delete();
        }
        EventUserPlannedBreaks::deleteAll(['event_id' => $this->id]);
        EventUserPlannedWrokingTime::deleteAll(['event_id' => $this->id]);
        EventUserWorkingTime::deleteAll(['event_id' => $this->id]);
        IncomesForEvent::deleteAll(['event_id' => $this->id]);
        OutcomesForEvent::deleteAll(['event_id' => $this->id]);

        $model = $this->findModel($id);
        if (Yii::$app->request->post('ajax'))
        {
            $tasks = $model->tasks;
        }
        $resolved = $model->resolveConflictsAfterDelete();
        $model->delete();
        if (Yii::$app->request->post('ajax'))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['tasks'=>$tasks];
        }
        Yii::$app->session->setFlash('error', Yii::t('app', 'Skasowano!'));
        if ($resolved)
            return $this->render('resolved_conflicts', [
                'resolved' => $resolved,
            ]);
        else
            return $this->redirect(['index']);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null)
        {
            $model->prepareDateAttributes();
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    private function datesChanged($model1, $model2)
    {
        $return = false;
        if ($model1->event_start!=$model2->event_start)
        {
            $return = true;
        }
        if ($model1->event_end!=$model2->event_end)
        {
            $return = true;
        } 
        if ($model1->packing_start!=$model2->packing_start)
        {
            $return = true;
        }
        if ($model1->packing_end!=$model2->packing_end)
        {
            $return = true;
        } 
        if ($model1->montage_start!=$model2->montage_start)
        {
            $return = true;
        }
        if ($model1->montage_end!=$model2->montage_end)
        {
            $return = true;
        } 
        if ($model1->readiness_start!=$model2->readiness_start)
        {
            $return = true;
        }
        if ($model1->readiness_end!=$model2->readiness_end)
        {
            $return = true;
        } 
        if ($model1->practice_start!=$model2->practice_start)
        {
            $return = true;
        }
        if ($model1->practice_end!=$model2->practice_end)
        {
            $return = true;
        } 
        if ($model1->disassembly_start!=$model2->disassembly_start)
        {
            $return = true;
        }
        if ($model1->disassembly_end!=$model2->disassembly_end)
        {
            $return = true;
        } 
        return $return;       
    }

    public function actionContactList($id=null, $q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
//        if (!is_null($q)) {
            $data = Contact::getList($id, $q);
            $out['results'] = [];
            foreach ($data as $key=>$value)
            {
                $out['results'][] = [
                    'id' => $key,
                    'text' => $value,
                ];
            }

        return $out;
    }

    public function actionTabsData($id, $tab=1)
    {
        $viewMap = [
            0=>'_tabUser',
            1=>'_tabGear',
            2=>'_tabAttachment',
            3=>'_tabVehicle',
        ];
        $model = $this->findModel($id);
        $view = $viewMap[$tab];
        $html = $this->renderPartial($view, ['model'=>$model]);

        return Json::encode($html);

    }

    public function actionUpdateUser($userId, $eventId)
    {
        $output = ['output'=>'', 'message'=>''];
        $request = Yii::$app->request;
        if ($request->post('hasEditable')==1)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $roleIds = $request->post('event_role', []);

            //todo: transaction
            EventUserRole::removeAll($userId, $eventId);
            $owner = EventUserRole::findEventUser($userId, $eventId);
            foreach ($roleIds as $roleId)
            {
                $params = ['event_user_id'=>$owner->id, 'user_event_role_id'=>$roleId];
                $model = new EventUserRole($params);
                if ($model->save())
                {
                    $output['output'] .= $model->userEventRole->name.'; ';
                }
                else
                {
                    $output['message'] = current($model->getErrors());
                }
            }
            $output['output'] = UserEventRole::getRolesString($userId, $eventId);
            return $output;
        }
    }


    public function actionUpdateWorkingTime($eventId, $type)
    {
        $this->_setWorkingTimeAttributes($type);
        $output = ['output'=>'', 'message'=>''];
        $request = Yii::$app->request;
        if ($request->post('hasEditable')==1)
        {
            //todo:sprawdzanie Czy zakres nie wychodzi za event!!!

            Yii::$app->response->format = Response::FORMAT_JSON;


            $className = $this->_workingTimeClassName;

            $model = $className::findOne([$this->_workingIdAttribute=>$request->post('editableKey'), 'event_id'=>$eventId]);
            if ($model != null)
            {
                if ($model->load($request->post()))
                {
                    $value = '...';
                    if ($model->save())
                    {
                        $rel = $model->getRelation($this->_workingTimeRelationName)->one();
                        $output['output'] = $rel->getWorkingTime($eventId, false, true);
                    }
                    else
                    {
                        $output['message'] = current($model->getErrors());
                    }
                }
            }

            return $output;
        }
    }

    public function actionUpdateWorkingTimeEventGearItem($eventId, $itemId = null, $gearId, $gearGroup = null, $otherEvent = false) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $event = Event::find()->where(['id'=>$eventId])->one();
        $start = $_POST['EventGearItem']['start_time'];
        $end = $_POST['EventGearItem']['end_time'];

        if ($itemId != null) {
            $gears = EventGearItem::find()->where(['event_id' => $eventId])->andWhere(['gear_item_id' => $itemId])->all();
            foreach ($gears as $gear) {
                $gear->start_time = $start;
                $gear->end_time = $end;
                $gear->save();
            }
        }
        else {
            foreach (GearItem::find()->where(['gear_id' => $gearId])->andWhere(['group_id' => $gearGroup])->all() as $gearItem) {
                $gears = EventGearItem::find()->where(['event_id' => $eventId])->andWhere(['gear_item_id' => $gearItem->id])->all();
                foreach ($gears as $gear) {
                    $gear->start_time = $start;
                    $gear->end_time = $end;
                    $gear->save();
                }
            }
        }
        $output = $start.' - '.$end;
        if ($start == $event->getTimeStart() && $end == $event->getTimeEnd() && !$otherEvent) {
            $output = 'Cały event';
        }
        return ['output' => $output, 'message' => null, 'gear_id' => $gearId];
    }

    public function actionUpdateWorkingTimeEventGear($gear_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = $_POST['PacklistGear']['start_time'];
        $end = $_POST['PacklistGear']['end_time'];
        $gear = \common\models\PacklistGear::findOne($gear_id);
        $event = $gear->packlist->event;
        if ($start<$gear->start_time )
        {
            //sprawdzamy dostępność
            $available = $gear->gear->getAvailabe($start, $gear->start_time);
            $available = $available-$gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                return ['output' => $gear->start_time.' - '.$gear->end_time, 'message' => Yii::t('app', 'Brak dostępnych egzemplarzy w tym terminie'), 'gear_id' => $gear_id];
            }
        }
        if ($end>$gear->end_time )
        {
            //sprawdzamy dostępność
            $available = $gear->gear->getAvailabe($gear->end_time, $end);
            $available =$available- $gear->gear->getInService();
            if ($available<$gear->quantity)
            {
                return ['output' => $gear->start_time.' - '.$gear->end_time, 'message' => Yii::t('app', 'Brak dostępnych egzemplarzy w tym terminie'), 'gear_id' => $gear_id];
            }
        }        
        $gear->start_time = $start;
        $gear->end_time = $end;
        $gear->save();
        $output = $start.' - '.$end;
        if ($start == $event->getTimeStart() && $end == $event->getTimeEnd() && !$otherEvent) {
            $output = 'Cały event';
        }
        return ['output' => $output, 'message' => null, 'gear_id' => $gear_id];
    }

    public function actionUpdateWorkingTimeEventGearGroup($eventId, $group, $otherEvent = false) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $event = Event::find()->where(['id'=>$eventId])->one();
        $start = $_POST['EventGearItem']['start_time'];
        $end = $_POST['EventGearItem']['end_time'];
        $items = GearItem::find()->where(['group_id'=>$group])->all();
        foreach ($items as $item) {
            $gear = EventGearItem::find()->where(['event_id' => $eventId])->andWhere(['gear_item_id' => $item->id])->one();
            $gear->start_time = $start;
            $gear->end_time = $end;
            $gear->save();
        }
        $output = $start.' - '.$end;
        if ($start == $event->getTimeStart() && $end == $event->getTimeEnd() && !$otherEvent) {
            $output = Yii::t('app', 'Cały event');
        }
        return ['output' => $output, 'message' => null, 'gear_id' => $items[0]->gear_id];
    }

    protected function _setWorkingTimeAttributes($type)
    {
        switch ($type)
        {
            case 'vehicle':
                $this->_workingTimeClassName = EventVehicle::className();
                $this->_workingTimeRelationName = 'vehicle';
                $this->_workingIdAttribute = 'vehicle_id';
                break;
            case 'gear_item':
                $this->_workingTimeClassName = EventGearItem::className();
                $this->_workingTimeRelationName = 'gearItem';
                $this->_workingIdAttribute = 'gear_item_id';
                break;
            case 'outer_gear':
                $this->_workingTimeClassName = EventOuterGear::className();
                $this->_workingTimeRelationName = 'outerGear';
                $this->_workingIdAttribute = 'outer_gear_id';
                break;
            case 'outer_gear_model':
                $this->_workingTimeClassName = EventOuterGearModel::className();
                $this->_workingTimeRelationName = 'outerGearModel';
                $this->_workingIdAttribute = 'id';
                break;
            case 'user':
                $this->_workingTimeClassName = EventUser::className();
                $this->_workingTimeRelationName = 'user';
                $this->_workingIdAttribute = 'user_id';
                break;
        }
    }

    public function actionCheckAvailabilityForEvent($event_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return json_encode(Event::findOne(['id' => $event_id])->getAvailableUsers());
    }

    public function actionUserForm($user_id, $event_id, $update_event_user_data = 0) {
        $event = Event::find()->where(["event.id" => $event_id])->one();
        $eventUser = EventUser::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->one();
        $user = User::findOne($user_id);
        $request = Yii::$app->request;

        if ($update_event_user_data == 1 && $request->isPost) {
            $result = null;
            // zapisujemy role
            EventUserRole::deleteAll('event_user_id=' . $eventUser->id);
            if ($request->post('roles')) {
                foreach ($request->post('roles') as $role_id) {
                    $role = new EventUserRole();
                    $role->event_user_id = $eventUser->id;
                    $role->user_event_role_id = $role_id;
                    $role->create_time = date('Y-m-d H-i-s');
                    $role->update_time = date('Y-m-d H-i-s');
                    $role->save();
                }
            }
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa
            $workWholePacking = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end]);
            if ($request->post('workWholePacking')) {
                if ($workWholePacking->count() == 0) {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->packing_start;
                    $work->end_time = $event->packing_end;
                    $work->save();
                }
            }
            else {
                if ($workWholePacking->count() > 0) {
                    $workWholePacking->one()->delete();
                }
            }
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa
            $workWholeMontage = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end]);
            if ($request->post('workWholeMontage')) {
                if ($workWholeMontage->count() == 0) {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->montage_start;
                    $work->end_time = $event->montage_end;
                    $work->save();
                }
            }
            else {
                if ($workWholeMontage->count() > 0) {
                    $workWholeMontage->one()->delete();
                }
            }
            $workWholeEvent = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end]);
            if ($request->post('workWholeEvent')) {
                if ($workWholeEvent->count() == 0) {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->event_start;
                    $work->end_time = $event->event_end;
                    $work->save();
                }
            }
            else {
                if ($workWholeEvent->count() > 0) {
                    $workWholeEvent->one()->delete();
                }
            }
            $workWholeDisassembly = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end]);
            if ($request->post('workWholeDisassembly')) {
                if ($workWholeDisassembly->count() == 0) {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->disassembly_start;
                    $work->end_time = $event->disassembly_end;
                    $work->save();
                }
            }
            else {
                if ($workWholeDisassembly->count() > 0) {
                    $workWholeDisassembly->one()->delete();
                }
            }
            // koniec zapisu czasu pracy w czasie eventu/montazu/demontazu


            // zapiusujemy recznie wprowadzony czas pracy
            if ($request->post('eventRange')) {
                $time = explode(' ', $request->post('eventRange'));
                $start = $time[0] . " " . $time[1];
                $end = $time[3] . " " . $time[4];
                $canSave = true;
                foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                    if (Event::datesAreOverlaping(new DateTime($start), new DateTime($end), new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $canSave = false;
                        $result['error'] = Yii::t('app', 'Nie można zapisać czasu pracy, użytkownik pracuje już w tym okresie');
                    }
                }

                if ($canSave) {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $start;
                    $work->end_time = $end;
                    $work->save();
                }
            }

            //zapisujemy przerwę
            if ($request->post('user_break_name') && $request->post('user_break_start') && $request->post('user_break_end')) {
                $break = new EventUserPlannedBreaks();
                $break->user_id = $user_id;
                $break->event_id = $event_id;
                $break->start_time = $request->post('user_break_start');
                $break->end_time = $request->post('user_break_end');
                $break->name = $request->post('user_break_name');
                $break->icon = $request->post('user_break_icon');
                $break->save();
            }

            // zapisujemy przerwy eventowe
            $eventBreaks = EventBreaks::findAll(['event_id' => $event_id]);
            foreach ($eventBreaks as $break) {
                $this->deleteAllEventBreakUser($user_id, $break->id);
            }
            if ($request->post('selection')) {
                foreach ($request->post('selection') as $break_id) {
                    $this->actionAssignUserBreak($user_id, $break_id);
                }
            }

            return $result;
        }
        else {
            $user_roles = [];
            if (isset($_GET['role'])) {
                $user_roles[] = $_GET['role'];
            }
            if ($eventUser) {
                foreach ($eventUser->userEventRoles as $role) {
                    $user_roles[] = $role->id;
                }
            }

            $offer = $event->getAcceptedOffers();
            $no_offer = false;
            if (isset($offer['error'])) {
                $no_offer = true;
            }

            $role_list = [];
            if ($no_offer) {
                foreach (UserEventRole::find()->all() as $role) {
                    $role_list[$role->id] = $role->name;
                }
            }
            else {
                $offer = $offer[0];
                foreach ($offer->roles as $role) {
                    $role_list[$role->id] = $role->name;
                }
            }

            $query = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id]);
            $userWorkingHoursDataProvider = new ActiveDataProvider(['query' => $query]);

            $userBreaksQuery = EventUserPlannedBreaks::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id]);
            $userBreaksDataProvider = new ActiveDataProvider(['query' => $userBreaksQuery,]);

            $assignedBreks = EventBreaksUser::find()->innerJoinWith(['eventBreak'])->where(["event_breaks_user.user_id" => $user_id,
                "event_breaks.event_id" => $event_id])->indexBy('event_break_id')->all();

            $eventBreak = new EventBreaks();
            $iconList = $eventBreak->getIconsArray();
            $checked_packing = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end])->count();   
            $checked_montage = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end])->count();
            $checked_event = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end])->count();
            $checked_disassembly = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end])->count();

            $vacations = [
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];
            $plannedVacations = [
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];

            $overlapingEvents = [
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];

            $closeEvents = [
                'montage' => [],
                'event' => [],
                'disassembly' => []
            ];
            $startPacking = new DateTime($event->packing_start);
            $endPacking = new DateTime($event->packing_end);
            $startMontage = new DateTime($event->montage_start);
            $endMontage = new DateTime($event->montage_end);
            $startEvent = new DateTime($event->event_start);
            $endEvent = new DateTime($event->event_end);
            $startDisassembly = new DateTime($event->disassembly_start);
            $endDisassemlby = new DateTime($event->disassembly_end);
            foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                if ($time->event_id != $event_id) {
                    if (Event::datesAreOverlaping($startPacking, $endPacking, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['packing'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startMontage, $endMontage, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['montage'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startEvent, $endEvent, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['event'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startDisassembly, $endDisassemlby, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['disassembly'][] = Event::findOne($time->event_id);
                    }
                }
            }
            foreach (Vacation::findAll(['user_id' => $user_id]) as $time) {
                $vacation_start = new DateTime($time->start_date . " 00:00");
                $vacation_end = new DateTime($time->end_date . " 23:59");
                if (Event::datesAreOverlaping($startMontage, $endMontage, $vacation_start, $vacation_end)) {
                    if ($time->status == Vacation::STATUS_NEW) {
                        $plannedVacations['montage'][] = $time;
                    }
                    else if ($time->status == Vacation::STATUS_ACCEPTED) {
                        $vacations['montage'][] = $time;
                    }
                }
                if (Event::datesAreOverlaping($startEvent, $endEvent, $vacation_start, $vacation_end)) {
                    if ($time->status == Vacation::STATUS_NEW) {
                        $plannedVacations['event'][] = $time;
                    }
                    else if ($time->status == Vacation::STATUS_ACCEPTED) {
                        $vacations['event'][] = $time;
                    }
                }
                if (Event::datesAreOverlaping($startDisassembly, $endDisassemlby, $vacation_start, $vacation_end)) {
                    if ($time->status == Vacation::STATUS_NEW) {
                        $plannedVacations['disassembly'][] = $time;
                    }
                    else if ($time->status == Vacation::STATUS_ACCEPTED) {
                        $vacations['disassembly'][] = $time;
                    }
                }
            }

            foreach ($user->events4 as $eventModel) {
                if ($event->id == $eventModel->id) {
                    continue;
                }
                $startPacking = new DateTime($event->packing_start);
                $endPacking = new DateTime($event->packing_end);
                $startMontage = new DateTime($event->montage_start);
                $endMontage = new DateTime($event->montage_end);
                $startEvent = new DateTime($event->event_start);
                $endEvent = new DateTime($event->event_end);
                $startDisassembly = new DateTime($event->disassembly_start);
                $endDisassemlby = new DateTime($event->disassembly_end);

                $event_start = new DateTime($eventModel->getTimeStart());
                $event_end = new DateTime($eventModel->getTimeEnd());
                if ($this->checkCloseRange($event_start, $event_end, $startPacking, $endPacking)) {
                    $closeEvents['packing'][] = $eventModel;
                }
                if ($this->checkCloseRange($event_start, $event_end, $startMontage, $endMontage)) {
                    $closeEvents['montage'][] = $eventModel;
                }
                if ($this->checkCloseRange($event_start, $event_end, $startEvent, $endEvent)) {
                    $closeEvents['event'][] = $eventModel;
                }
                if ($this->checkCloseRange($event_start, $event_end, $startDisassembly, $endDisassemlby)) {
                    $closeEvents['disassembly'][] = $eventModel;
                }
            }

            return $this->renderAjax('_ekipaForm', ['user' => \common\models\User::findOne($user_id),
                'assignedBreks' => $assignedBreks,
                'model' => $event,
                'role_list' => $role_list,
                'user_roles' => $user_roles,
                'userWorkingHoursDataProvider' => $userWorkingHoursDataProvider,
                'userBreaksDataProvider' => $userBreaksDataProvider,
                'checked_packing' => $checked_packing,
                'checked_montage' => $checked_montage,
                'checked_event' => $checked_event,
                'checked_disassembly' => $checked_disassembly,
                'iconsArray' => $iconList,
                'noOffer' => $no_offer,
                'overlapingEvents' => $overlapingEvents,
                'closeEvents' => $closeEvents,
                'vacations' => $vacations,
                'plannedVacations' => $plannedVacations,
            ]);
        }
    }

    private function checkCloseRange(DateTime $start, DateTime $end, DateTime $event_start, DateTime $event_end) {
        if (Event::datesAreOverlaping($start, $end, $event_start, $event_end)) {
            return true;
        }
        $test1 = clone($event_start)->add(new DateInterval('PT12H')) ;
        $test2 = clone($event_start)->sub(new DateInterval('PT24H'));
        $test3 = clone($event_end)->sub(new DateInterval('PT12H'));
        $test4 = clone($event_end)->add(new DateInterval('PT24H'));

        if ($test1 <= $start && $test2 >= $start) {
            return true;
        }
        if ($test3 <= $start && $test4 >= $start) {
            return true;
        }
        if ($test1 <= $end && $test2 >= $end) {
            return true;
        }
        if ($test3 <= $end && $test4 >= $end) {
            return true;
        }
        return false;
    }

    public function deleteAllEventBreakUser($user_id, $event_break_id) {
        EventBreaksUser::deleteAll(['event_break_id' => $event_break_id,
            'user_id' => $user_id]);
    }

    public function actionAssignUserBreak($user_id, $event_break_id) {
        $count = EventBreaksUser::find()->where(['user_id' => $user_id])->andWhere(['event_break_id' => $event_break_id])->count();
        if ($count == 0) {
            $model = new EventBreaksUser();
            $model->user_id = $user_id;
            $model->event_break_id = $event_break_id;
            $model->save();
        }
    }

    public function actionDeleteCustomWorkingHours($event_id, $start, $end) {
        foreach (EventUserPlannedWrokingTime::find()->where(['event_id' => $event_id])->andWhere(['start_time' => $start])->andWhere(['end_time' => $end])->all() as $time ) {
            $time->delete();
        }
    }

    public function actionVehicleForm($event_id, $vehicle_id, $update_event_vehicle_data = 0, $just_assigned = 0) {
        $request = Yii::$app->request;
        $event = Event::findOne($event_id);
        $vehicle = Vehicle::findOne($vehicle_id);
        $overlapingEvents = [
            'packing' =>[],
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];
        if ($update_event_vehicle_data == 1) {
            $event_vehicle = EventVehicle::findOne(['event_id' => $event_id, 'vehicle_id' => $vehicle_id]);
            if (!$event_vehicle) {
                $event_vehicle = new EventVehicle();
                $event_vehicle->event_id = $event_id;
                $event_vehicle->vehicle_id = $vehicle_id;
                $event_vehicle->save();
            }
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa
            $workWholePacking = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end]);
            if ($request->post('workWholePacking')) {
                if ($workWholePacking->count() == 0) {
                    $work = new EventVehicleWorkingHours();
                }else{
                    $work = $workWholePacking->one();
                }
                    $work->vehicle_id = $vehicle_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->packing_start;
                    $work->end_time = $event->packing_end;
                    $work->vehicle_model_id = $request->post('vehicles-packing');
                    $work->save();
            }
            else {
                if ($workWholePacking->count() > 0) {
                    $workWholePacking->one()->delete();
                }
            }
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa
            $workWholeMontage = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end]);
            if ($request->post('workWholeMontage')) {
                if ($workWholeMontage->count() == 0) {
                    $work = new EventVehicleWorkingHours();
                }else{
                    $work = $workWholeMontage->one();
                }
                    $work->vehicle_id = $vehicle_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->montage_start;
                    $work->end_time = $event->montage_end;
                    $work->vehicle_model_id = $request->post('vehicles-montage');
                    $work->save();
                
            }
            else {
                if ($workWholeMontage->count() > 0) {
                    $workWholeMontage->one()->delete();
                }
            }
            $workWholeEvent = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end]);
            if ($request->post('workWholeEvent')) {
                if ($workWholeEvent->count() == 0) {
                    $work = new EventVehicleWorkingHours();
                }else{
                    $work = $workWholeEvent->one();
                }
                    $work->vehicle_id = $vehicle_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->event_start;
                    $work->end_time = $event->event_end;
                    $work->vehicle_model_id = $request->post('vehicles-event');
                    $work->save();
                
            }
            else {
                if ($workWholeEvent->count() > 0) {
                    $workWholeEvent->one()->delete();
                }
            }
            $workWholeDisassembly = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end]);
            if ($request->post('workWholeDisassembly')) {
                if ($workWholeDisassembly->count() == 0) {
                    $work = new EventVehicleWorkingHours();
                }else{
                    $work = $workWholeDisassembly->one();
                }
                    $work->vehicle_id = $vehicle_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->disassembly_start;
                    $work->end_time = $event->disassembly_end;
                    $work->vehicle_model_id = $request->post('vehicles-disassembly');
                    $work->save();
            }
            else {
                if ($workWholeDisassembly->count() > 0) {
                    $workWholeDisassembly->one()->delete();
                }
            }
            if ($request->post('eventRange')) {
                $time = explode(' ', $request->post('eventRange'));
                $start = $time[0] . " " . $time[1];
                $end = $time[3] . " " . $time[4];
                $work = new EventVehicleWorkingHours();
                $work->vehicle_id = $vehicle_id;
                $work->event_id = $event_id;
                $work->start_time = $start;
                $work->end_time = $end;
                $work->vehicle_model_id = $request->post('vehicles-all');
                return $work->save();
            }
        }
        else {
            if ($just_assigned == 1) {
                $checked_packing = true;
                $checked_montage = true;
                $checked_event = true;
                $checked_disassembly = true;
            }
            else {
                $checked_packing = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end])->count();
                $checked_montage = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end])->count();
                $checked_event = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end])->count();
                $checked_disassembly = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end])->count();
            }
            $query = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id]);
            $vehicleWorkingHoursDataProvider = new ActiveDataProvider(['query' => $query]);
            $startPacking = new DateTime($event->packing_start);
            $endPacking = new DateTime($event->packing_end);
            $startMontage = new DateTime($event->montage_start);
            $endMontage = new DateTime($event->montage_end);
            $startEvent = new DateTime($event->event_start);
            $endEvent = new DateTime($event->event_end);
            $startDisassembly = new DateTime($event->disassembly_start);
            $endDisassemlby = new DateTime($event->disassembly_end);
            foreach ($vehicle->getEventVehicleWorkingHours()->all() as $time) {
                if ($time->event_id != $event_id) {
                    if (Event::datesAreOverlaping($startPacking, $endPacking, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['packing'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startMontage, $endMontage, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['montage'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startEvent, $endEvent, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['event'][] = Event::findOne($time->event_id);
                    }
                    if (Event::datesAreOverlaping($startDisassembly, $endDisassemlby, new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $overlapingEvents['disassembly'][] = Event::findOne($time->event_id);
                    }
                }
            }
            $vm= [];
            if ($checked_packing)
                $vm[1] = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end])->one()->vehicle_model_id;
            else
                $vm[1] = null;
            if ($checked_montage)
                $vm[2] = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end])->one()->vehicle_model_id;
            else
                $vm[2] = null;
            if ($checked_event)
                $vm[3] = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end])->one()->vehicle_model_id;
            else
                $vm[3] = null;
            if ($checked_disassembly)
                $vm[4] = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end])->one()->vehicle_model_id;
            else
                $vm[4] = null;
            return $this->renderAjax('_flotaForm',
                [
                    'vehicle' => $vehicle,
                    'vm'=>$vm,
                    'checked_packing' => $checked_packing,
                    'checked_montage' => $checked_montage,
                    'checked_event' => $checked_event,
                    'checked_disassembly' => $checked_disassembly,
                    'model' => $event,
                    'vehicleWorkingHoursDataProvider' => $vehicleWorkingHoursDataProvider,
                    'overlapingEvents' => $overlapingEvents
                ]);
        }

    }

    protected function preparePDF($event, $sort){
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf', [
            'model' => $event,
            'settings' => $settings,
            'sort'=>$sort
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
            'settings' => $settings
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
                'options' => ['title' => 'PackingLista-'.$event->name],
                'filename' => 'packing-list-'.Inflector::slug($event->name).'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        return $pdf;
    }

    public function actionSendEventNotifications($id) {
        $model = $this->findModel($id);
        $model->sendNotifications();
    }

    public function actionSendAllEventsNotifications() {
        Event::sendAllNotifications();
    }

    public function actionResolveConflict($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = EventConflict::findOne($id);
        $post = Yii::$app->request->post();
        $status = $post['resolved'];
        $model->resolved = $status;
        $model->save();
        $list = [0=>Yii::t('app', 'Nierozwiązany'), 1=>Yii::t('app', 'Rozwiązany')];
        $output = ['output'=>$list[$model->resolved], 'message'=>''];
        return $output;
        exit;
    }

    public function actionDeleteConflict($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = EventConflict::findOne($id);
        $model->delete();
        exit;
    }

    public function actionConflictCalendar($conflict_id)
    {
        $conflict = EventConflict::findOne($conflict_id);
        return $this->renderAjax('_conflictCalendar', ['conflict'=>$conflict]);
    }

    public function actionAddToProjects($id)
    {
        foreach (Yii::$app->request->post('events') as $value) {
            $value = substr($value, 5, strlen($value)-5);
            $model = Event::findOne($value);
            if ($model)
            {
                if ($id)
                    $model->project_id = $id;
                else
                    $model->project_id = null;
                $model->save();
            }
            
        }
        exit;
    }

    protected function createExcel($data)
    {
         $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [

                mb_substr("Raport", 0, 31) => [   // Name of the excel sheet
                    'data' => $data,

                    // Set to `false` to suppress the title row
                    'titles' => false
                ],
            ]
        ]);
        foreach(range('A','Z') as $columnID) {
            $file->getWorkbook()->getSheet(0)->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        return $file;       
    }

    protected function prepareExcelData2($events)
    {
        $data = [];
        $data[] = [Yii::t("app", "Imię i nazwisko"), Yii::t("app", "Wydarzenie"), Yii::t("app", "Etap"), Yii::t("app", "Początek"), Yii::t("app", "Koniec")];
        foreach ($events as $event)
        {
            $users = \common\models\EventUserPlannedWrokingTime::find()->where(['event_id'=>$event->id])->all();
            foreach ($users as $u)
            {
                $data[] = [$u->user->displayLabel, $event->name, $u->eventSchedule->name, $u->start_time, $u->end_time];
            }
        }
        return $data;

    }

    protected function prepareExcelData($events)
    {
        
        $data = [Yii::t("app", "Dodano"), Yii::t("app", "Status"), Yii::t("app", "Rodzaj"), Yii::t("app", "Wydarzenie"), Yii::t("app", "Typ"), Yii::t("app", "Miesiąc księgowania"), Yii::t("app", "Project Manager"), Yii::t("app", "Klient"), Yii::t("app", "Od - do"), Yii::t("app", "Zaliczka")];
        $statuts = \common\models\Event::getStatusList();
        $types = \common\models\Event::getEventTypeList();
        $types2 = \common\models\Event::getTypeList();
        $gcat = \common\models\GearCategory::getMainList(true);
        foreach ($gcat as $key => $cat) {
            $data[] = $cat->name;
            }
        $data[] = Yii::t("app", "Inne");
        $data[] = Yii::t("app", "Transport");
        $data[] = Yii::t("app", "Obsługa");
        $data[] = Yii::t("app", "Suma koszt");
        $data[] = Yii::t("app", "Zysk bez prowizji");
        $groups = \common\models\ProvisionGroup::find()->all();
        foreach ($groups as $gp)
            {
            $data[] = $gp->name;
            }
        $data[] = Yii::t("app", "Suma prowizji");
        $data[] = Yii::t("app", "Zysk końcowy");
        $data[] = Yii::t("app", "Wartość oferty netto");
        $data[] = Yii::t("app", "VAT ofert");
        $data[] = Yii::t("app", "Wartość oferty brutto");
        $data[] = Yii::t("app", "Zapłacono");
        $data[] = Yii::t("app", "Pozostało do zapłaty");
        $data[] = Yii::t("app", "FV");
        $data2 = [$data];
        foreach ($events as $event)
        {
             if (isset($statuts[$event->status]))
            {
                $s = $statuts[$event->status];
            }else{
                $s = "-";
            }           
            if (isset($types[$event->event_type]))
            {
                $t = $types[$event->event_type];
            }else{
                $t = "-";
            }
            if (isset($types2[$event->type]))
            {
                $t2 = $types2[$event->type];
            }else{
                $t2 = "-";
            }
            if (isset($event->manager))
            {
                $m = $event->manager->displayLabel;
            }else{
                $m = "-";
            }
            if (isset($event->customer))
            {
                $c = $event->customer->name;
            }else{
                $c = "-";
            }
            $start = Yii::$app->formatter->asDateTime($event->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($event->getTimeEnd(), 'short');
                    $d = $start." - ".$end;
            $row = [$event->create_time, $s , $t, $event->name, $t2, $event->paying_date, $m, $c, $d, $event->getEventPMcost()];
            $costs = $event->getEventCosts();
            foreach ($gcat as $key => $cat) {
                if (isset($costs[$cat->name]))
                {
                    $row[] = $costs[$cat->name];
                }else{
                    $row[] = 0;
                }
                
            }
            if (isset($costs[Yii::t("app", "Inne")]))
            {
                $row[] = $costs[Yii::t("app", "Inne")];
            }else{
                $row[] = 0;
            }
             if (isset($costs[Yii::t("app", "Transport")]))
            {
                $row[] = $costs[Yii::t("app", "Transport")];
            }else{
                $row[] = 0;
            }
            if (isset($costs[Yii::t("app", "Obsługa")]))
            {
                $row[] = $costs[Yii::t("app", "Obsługa")];
            }else{
                $row[] = 0;
            }
            if (isset($costs[Yii::t("app", "Suma")]))
            {
                $row[] = $costs[Yii::t("app", "Suma")];
            }else{
                $row[] = 0;
            }    
            $val = $event->getEventValueSum();   
            $profit = $val-$costs[Yii::t("app", "Suma")];
            
            $row[] = $profit;
            $sum = 0;
            foreach ($groups as $gp)
            {
                $provs = \common\models\EventProvisionValue::find()->where(['event_id'=>$event->id, 'provision_group_id'=>$gp->id])->asArray()->all();
                if (isset($provs[Yii::t("app", "Suma")]))
                {
                    $row[] = $provs[Yii::t("app", "Suma")];
                    $sum +=$provs[Yii::t("app", "Suma")];
                }else{
                    $row[] = 0;
                }
            }
            $row[] = $sum;
            $row[] = $profit-$sum;
            $row[] = $val;
            $row[] = $val*0.23;
            $row[] = $val*1.23;
            $row[] = $event->getEventPaid();
            $row[] = $val*1.23 - $event->getEventPaid();
            $inv = "";
            foreach ($event->invoices as $invoice)
                    $inv .= $invoice->fullnumber." ";
            $row[] = $inv;
            $data2[] = $row;

        }

        return $data2;
    }

    public function actionExcel($type=1)
    {
        $model = new EventSearch();
        $post = Yii::$app->request->post();
        $model->paying_date = date("Y-m")."-01";
        $model->dateStart = date("Y-m")."-01 00:00:00";
        $model->dateEnd = date("Y-m-t")."23:59:59";
        $model->useRange = 1;
        if($model->load($post)){
            $events = $model->search($post);
            $events = $events->query->all();
            if ($type==1)
                $data = $this->prepareExcelData($events);
            else
                $data = $this->prepareExcelData2($events);
            $file = $this->createExcel($data);
            $file->send('raport_'.date("Y-m-d").'.xlsx');
            exit;

        }else{
            return $this->render('excel-report', ['model'=>$model]);
        }
    }
}
