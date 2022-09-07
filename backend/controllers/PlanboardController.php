<?php

namespace backend\controllers;

use common\models\Event;
use common\models\EventBreaksUser;
use common\models\EventUser;
use common\models\EventUserPlannedBreaks;
use common\models\EventUserPlannedWrokingTime;
use common\models\EventUserRole;
use common\models\EventVehicle;
use common\models\EventVehicleWorkingHours;
use common\models\form\PlanboardSearch;
use common\models\PlanboardUserEventRoleOrder;
use common\models\PlanboardUserEventRoleUsersOrder;
use common\models\PlanboardUserGeneralEventOrder;
use common\models\PlanboardVehicleOrder;
use common\models\UserEventRole;
use common\models\Vacation;
use common\models\Vehicle;
use common\models\User;
use common\models\PlanboardUserOrder;
use common\models\EventBreaks;
use DateInterval;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use yii\filters\AccessControl;
use backend\components\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


class PlanboardController extends Controller {

    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ['access' =>
            [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => 'true',
                        'roles' => ['menuPlanboard', 'eventsEventEditEyeCrewManage']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['vehicle-form' => ['post', 'get'], 'user-form' => ['post', 'get'],
                    'events' => ['post', 'get'], 'assign-user-break' => ['post'],],],];
    }

    public function actionIndex() {
        return $this->render('planboard');
    }

    public function actionUserTab() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users = User::find()->select(['id', 'first_name', 'last_name', 'type'])->with(['skills', 'departments'])->andWhere(['active' => 1])->andWhere(['visible_in_offer'=>1])->orderBy(['last_name' => SORT_ASC, 'first_name' => SORT_ASC])->asArray()->all();
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $date = new \DateTime();
        $add = new \DateInterval('P1M');
        $date->sub($add);
        foreach ($users as $index=>$val)
        {
            $users[$index]['vacations'] = \common\models\Vacation::find()->where(['user_id'=>$val['id']])->andWhere(['>', 'start_date', $date->format('Y-m-d')])->asArray()->all();
            $users[$index]['eventUsers'] = \common\models\EventUser::find()->where(['user_id'=>$val['id']])->andWhere(['>', 'start_time', $date->format('Y-m-d')])->asArray()->all();
            $users[$index]['eventUserPlannedWrokingTimes'] = \common\models\EventUserPlannedWrokingTime::find()->where(['user_id'=>$val['id']])->andWhere(['>', 'start_time', $date->format('Y-m-d')])->asArray()->all();
        }
        return $users;
    }

    public function actionVehicleTab() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = Vehicle::find()->select(['name', 'registration_number',
                'id'])->where(['active' => 1])->andWhere(['vehicle.status'=>1])->orderBy('name')->all();

        $vehicles = [];
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $date = new \DateTime();
        $add = new \DateInterval('P1M');
        $date->sub($add);
        foreach ($query as $key => $vehicle) {
            $vehicle_data = $vehicle;
            $all_events = [];
            $workingTimes = \common\models\EventVehicleWorkingHours::find()->where(['vehicle_id'=>$vehicle['id']])->andWhere(['>', 'start_time', $date->format('Y-m-d')])->asArray()->all();


            $vehicles[] = ['vehicle' => $vehicle_data, 'events' => $all_events, 'eventUserPlannedWrokingTimes' => $workingTimes];

        }

        return $vehicles;
    }

    public function actionEventBreaksForm($event_id, $update_event_breaks = 0, $delete_event_breaks = 0) {
        if ($update_event_breaks == 1 && isset($_POST['EventBreaks'])) {
            foreach ($_POST['EventBreaks'] as $key => $eventBreak) {
                echo $key . "<br>";
                $newBreak = false;
                if (isset($eventBreak['id'])) {
                    $model = EventBreaks::findOne((int)$eventBreak['id']);
                    if (!$model) {
                        $model = new EventBreaks();
                        $model->event_id = $event_id;
                        $newBreak = true;
                    }
                }
                else {
                    $model = new EventBreaks();
                    $model->event_id = $event_id;
                    $newBreak = true;
                }

                $model->name = $eventBreak['name'];
                $model->break_date_range = $eventBreak['break_date_range'];
                $model->icon = $eventBreak['icon'];
                if (!$model->save()) {
                    var_dump($model->errors);
                }
                if ($model->id && $newBreak) {
                    if (isset($eventBreak['check'])) {
                        foreach ($eventBreak['check'] as $user_id) {
                            $user_break = new EventBreaksUser();
                            $user_break->event_break_id = $model->id;
                            $user_break->user_id = $user_id;
                            $user_break->save();
                        }
                    }
                }

            }
        }
        elseif ($delete_event_breaks == 1) {
            if (isset($_POST['id'])) {
                EventBreaksUser::deleteAll(['event_break_id' => (int)$_POST['id']]);
                $model = EventBreaks::findOne((int)$_POST['id']);
                if ($model) {
                    $model->delete();
                }
            }
        }
        else {
            $eventbreak_model = new EventBreaks();
            $list = $eventbreak_model->getIconsArray($event_id);
            $models = EventBreaks::findAll(['event_id' => $event_id]);
            if (!$models) {
                $models = [new EventBreaks()];
            }

            $eventUsers = EventUser::find()->where(['event_id'=>$event_id]);
            $userDataProvider = new ActiveDataProvider([
                'query' => $eventUsers,
            ]);
            $event = \common\models\Event::findOne($event_id);

            return $this->renderAjax('planboardEventBreakeForm', [
                'models' => $models,
                'event_id' => $event_id,
                'iconsArray' => $list,
                'event' => $event,
                'userDataProvider' => $userDataProvider
            ]);
        }
    }

    public function actionEventCustomWorkingHoursForm($event_id) {
        $event = Event::findOne($event_id);

        return $this->renderAjax('planboardEventCustomWorkingHoursForm', [
            'customHours' => $event->getCustomUserWorkingHours(),
            'event' => $event
        ]);
    }

    public function actionVehicleForm($event_id, $vehicle_id, $update_event_vehicle_data = 0, $just_assigned = 0, $vehicle_model_id=null) {
        $request = Yii::$app->request;
        $event = Event::findOne($event_id);
        $vehicle = Vehicle::findOne($vehicle_id);
        if ($update_event_vehicle_data == 1) {
            $event_vehicle = EventVehicle::findOne(['event_id' => $event_id, 'vehicle_id' => $vehicle_id]);
            if (!$event_vehicle) {
                $event_vehicle = new EventVehicle();
                $event_vehicle->event_id = $event_id;
                $event_vehicle->vehicle_id = $vehicle_id;
                $event_vehicle->save();
            }
            foreach ($event->eventSchedules as $schedule)
            {
                //zapisujemy po kolei
                if ($request->post('workWhole'.$schedule->id))
                {
                    //zaznaczone
                    $working = EventVehicleWorkingHours::find()->where(['vehicle_id'=>$vehicle->id, 'event_schedule_id'=>$schedule->id])->one();
                    if (!$working)
                    {
                        //jeszcze nie było dodane więc dodajemy
                        $working = new EventVehicleWorkingHours();
                        $working->event_schedule_id = $schedule->id;
                        $working->event_id = $event->id;
                        $working->vehicle_id = $vehicle->id;

                    }
                        $working->vehicle_model_id = $request->post('vehicles-'.$schedule->id);
                        $working->start_time = $request->post('start'.$schedule->id);
                        $working->end_time = $request->post('end'.$schedule->id);
                        $working->save();
                }else{
                    //nie zaznaczone to usuwamy
                    EventVehicleWorkingHours::deleteAll(['vehicle_id'=>$vehicle->id, 'event_schedule_id'=>$schedule->id]);
                }

            }
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa

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

            $query = EventVehicleWorkingHours::find()->where(['vehicle_id' => $vehicle_id])->andWhere(['event_id' => $event_id]);
            $vehicleWorkingHoursDataProvider = new ActiveDataProvider(['query' => $query]);

            return $this->renderAjax('planboardFlotaForm',
                [
                    'vehicle' => $vehicle,
                    'model' => $event,
                    'vehicleWorkingHoursDataProvider' => $vehicleWorkingHoursDataProvider,
                ]);
        }

    }

    public function actionUserForm($user_id, $event_id, $update_event_user_data = 0, $just_assigned = 0, $role_id = 0, $in_event=false) {
        $event = Event::find()->where(["event.id" => $event_id])->one();
        $eventUser = EventUser::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->one();
        $user = User::findOne($user_id);
        $request = Yii::$app->request;

        if ($update_event_user_data == 1 && $request->isPost) {
            $result['ok'] = true;
            foreach ($event->eventSchedules as $schedule)
            {
                //zapisujemy po kolei
                if ($request->post('workWhole'.$schedule->id))
                {
                    //zaznaczone
                    $working = EventUserPlannedWrokingTime::find()->where(['user_id'=>$user->id, 'event_schedule_id'=>$schedule->id])->one();
                    if (!$working)
                    {
                        //jeszcze nie było dodane więc dodajemy
                        $working = new EventUserPlannedWrokingTime();
                        $working->event_schedule_id = $schedule->id;
                        $working->event_id = $event->id;
                        $working->user_id = $user->id;

                    }
                        $working->start_time = $request->post('start'.$schedule->id);
                        $working->end_time = $request->post('end'.$schedule->id);
                        $working->save();
                    if ($request->post('roles-'.$schedule->id)) {
                        EventUserRole::deleteAll(['working_hours_id'=>$working->id]);
                        foreach ($request->post('roles-'.$schedule->id) as $role_id) {
                                $role = new EventUserRole();
                                $role->event_user_id = $eventUser->id;
                                $role->user_event_role_id = $role_id;
                                $role->working_hours_id = $working->id;
                                $role->create_time = date('Y-m-d H-i-s');
                                $role->update_time = date('Y-m-d H-i-s');
                                $role->save();
                            

                        }
                    }
                }else{
                    //nie zaznaczone to usuwamy
                    EventUserPlannedWrokingTime::deleteAll(['user_id'=>$user->id, 'event_schedule_id'=>$schedule->id]);
                }

                }
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
                    if ($request->post('roles-additional')) {
                        foreach ($request->post('roles-additional') as $role_id) {
                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }
                }
            }
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        else {
            $offers = $event->getPlanningOffers();
            $no_offer = false;
            if (isset($offers['error'])) {
                $no_offer = true;
            }
            $role_list = [];
            foreach (UserEventRole::find()->where(['active'=>1])->all() as $role) {
                $role_list[$role->id] = $role->name;
            }

            $query = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id]);
            $userWorkingHoursDataProvider = new ActiveDataProvider(['query' => $query]);

            return $this->renderAjax('planboardEkipaForm', [
                'user' => $user,
                'model' => $event,
                'role_list' => $role_list,
                'userWorkingHoursDataProvider' => $userWorkingHoursDataProvider,
                'noOffer' => $no_offer,
                'inevent' =>$in_event
            ]);
        }
    }

public function actionUserForm2($user_id, $event_id, $update_event_user_data = 0, $just_assigned = 0, $role_id = 0, $in_event=false) {
        $event = Event::find()->where(["event.id" => $event_id])->one();
        $eventUser = EventUser::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->one();
        $user = User::findOne($user_id);
        $request = Yii::$app->request;

        if ($update_event_user_data == 1 && $request->isPost) {
            $result['ok'] = true;
            // zapisujemy rolef
            //EventUserRole::deleteAll(['event_user_id' => $eventUser->id]);
            // zapisujemy czas pracy w czasie montazu/eventu/demontazu jezeli jest lub nie ma zaznaczonego checboxa
            $workWholePacking = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end]);
            if (($request->post('workWholePacking'))) {
                $canSave = true;
                if ($workWholePacking->count() == 0) {
                
                foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                    if (Event::datesAreOverlaping(new DateTime($event->packing_start), new DateTime($event->packing_end), new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $canSave = false;
                        $result['error'] = Yii::t('app', 'Nie można zapisać czasu pracy, użytkownik pracuje już w tym okresie');
                    }
                }
                if ($canSave)
                {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->packing_start;
                    $work->end_time = $event->packing_end;
                    $work->save();                    
                }


                }else{
                    $work = $workWholePacking->one();
                }
                if ($canSave){
                    EventUserRole::deleteAll(['event_user_id' => $eventUser->id, 'working_hours_id'=>$work->id]);
                    if ($request->post('roles-packing')) {

                        foreach ($request->post('roles-packing') as $role_id) {

                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }                    
                }

            }
            else {
                if ($workWholePacking->count() > 0) {
                    $workWholePacking->one()->delete();
                }
            }

            $workWholeMontage = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end]);
            if ($request->post('workWholeMontage')) {
                $canSave = true;
                if ($workWholeMontage->count() == 0) {
                foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                    if (Event::datesAreOverlaping(new DateTime($event->montage_start), new DateTime($event->montage_end), new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $canSave = false;
                        $result['error'] = Yii::t('app', 'Nie można zapisać czasu pracy, użytkownik pracuje już w tym okresie');
                    }
                }
                if ($canSave)
                {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->montage_start;
                    $work->end_time = $event->montage_end;
                    $work->save();                    
                }
                }else{
                    $work = $workWholeMontage->one();
                }
                if ($canSave)
                {
                EventUserRole::deleteAll(['event_user_id' => $eventUser->id, 'working_hours_id'=>$work->id]);

                    if ($request->post('roles-montage')) {
                        foreach ($request->post('roles-montage') as $role_id) {
                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }
                }
            }
            else {
                if ($workWholeMontage->count() > 0) {
                    $workWholeMontage->one()->delete();
                }
            }
            $workWholeEvent = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end]);
            if ($request->post('workWholeEvent')) {
                $canSave = true;
                if ($workWholeEvent->count() == 0) {
                foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                    if (Event::datesAreOverlaping(new DateTime($event->event_start), new DateTime($event->event_end), new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $canSave = false;
                        $result['error'] = Yii::t('app', 'Nie można zapisać czasu pracy, użytkownik pracuje już w tym okresie');
                    }
                }
                if ($canSave)
                {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->event_start;
                    $work->end_time = $event->event_end;
                    $work->save();
                }
                }else{
                    $work = $workWholeEvent->one();
                }
                if ($canSave)
                {
                EventUserRole::deleteAll(['event_user_id' => $eventUser->id, 'working_hours_id'=>$work->id]);

                    if ($request->post('roles-event')) {
                        foreach ($request->post('roles-event') as $role_id) {
                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }
                }
            }
            else {
                if ($workWholeEvent->count() > 0) {
                    $workWholeEvent->one()->delete();
                }
            }
            $workWholeDisassembly = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end]);
            if ($request->post('workWholeDisassembly')) {
                $canSave = true;
                if ($workWholeDisassembly->count() == 0) {
                foreach ($user->getEventUserPlannedWrokingTimes()->all() as $time) {
                    if (Event::datesAreOverlaping(new DateTime($event->disassembly_start), new DateTime($event->disassembly_end), new DateTime($time->start_time), new DateTime($time->end_time))) {
                        $canSave = false;
                        $result['error'] = Yii::t('app', 'Nie można zapisać czasu pracy, użytkownik pracuje już w tym okresie');
                    }
                }
                if ($canSave)
                {
                    $work = new EventUserPlannedWrokingTime();
                    $work->user_id = $user_id;
                    $work->event_id = $event_id;
                    $work->start_time = $event->disassembly_start;
                    $work->end_time = $event->disassembly_end;
                    $work->save();
                }
                }else{
                    $work = $workWholeDisassembly->one();
                }
                if ($canSave)
                {
                EventUserRole::deleteAll(['event_user_id' => $eventUser->id, 'working_hours_id'=>$work->id]);

                    if ($request->post('roles-disassembly')) {
                        foreach ($request->post('roles-disassembly') as $role_id) {
                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }
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
                    if ($request->post('roles-additional')) {
                        foreach ($request->post('roles-additional') as $role_id) {
                            $role = new EventUserRole();
                            $role->event_user_id = $eventUser->id;
                            $role->user_event_role_id = $role_id;
                            $role->working_hours_id = $work->id;
                            $role->create_time = date('Y-m-d H-i-s');
                            $role->update_time = date('Y-m-d H-i-s');
                            $role->save();
                        }
                    }
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

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
        else {
            $user_roles= [];
            $user_roles[1] = [];
            $user_roles[2] = [];
            $user_roles[3] = [];
            $user_roles[4] = [];
            if (isset($_GET['role'])) {
                $user_roles[1][] = $_GET['role'];
                $user_roles[2][] = $_GET['role'];
                $user_roles[3][] = $_GET['role'];
                $user_roles[4][] = $_GET['role'];
            }
            if ($eventUser) {
                foreach ($eventUser->eventUserRoles as $role) {
                    if ($role->working)
                    {
                        $work = $role->working;
                        
                        if ($event->packing_start == $work->start_time && $event->packing_end == $work->end_time) 
                        {
                            $user_roles[1][] = $role->user_event_role_id;
                        }
                        if ($event->montage_start == $work->start_time && $event->montage_end == $work->end_time) 
                        {
                            $user_roles[2][] = $role->user_event_role_id;
                        }
                        if ($event->event_start == $work->start_time && $event->event_end == $work->end_time) 
                        {
                            $user_roles[3][] = $role->user_event_role_id;
                        }
                        if ($event->disassembly_start == $work->start_time && $event->disassembly_end == $work->end_time) 
                        {
                            $user_roles[4][] = $role->user_event_role_id;
                        }
                    }
                }
            }

            $offers = $event->getPlanningOffers();
            $no_offer = false;
            if (isset($offers['error'])) {
                $no_offer = true;
            }

            $role_list = [];
            foreach (UserEventRole::find()->where(['active'=>1])->all() as $role) {
                $role_list[$role->id] = $role->name;
            }

            $query = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id]);
            $userWorkingHoursDataProvider = new ActiveDataProvider(['query' => $query]);

            $userBreaksQuery = EventUserPlannedBreaks::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id]);
            $userBreaksDataProvider = new ActiveDataProvider(['query' => $userBreaksQuery,]);

            $assignedBreks = EventBreaksUser::find()->innerJoinWith(['eventBreak'])->where(["event_breaks_user.user_id" => $user_id,
                "event_breaks.event_id" => $event_id])->indexBy('event_break_id')->all();

            $eventBreak = new EventBreaks();
            $iconList = $eventBreak->getIconsArray();

            $eventBreaks = EventBreaks::findAll(['event_id' => $event_id]);
            $eventUsersCount = EventUser::find()->where(['event_id' => $event_id])->count();
            if ($just_assigned == 1) {
                $checked_packing = true;
                $checked_montage = true;
                $checked_event = true;
                $checked_disassembly = true;
                foreach ($eventBreaks as $break) {
                    if ($eventUsersCount == EventBreaksUser::find()->where(['event_break_id' => $break->id])->count()) {
                        $assignedBreks[$break->id] = 'default checked';
                    }
                }
            }
            else {
                $checked_packing = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->packing_start])->andWhere(['end_time' => $event->packing_end])->count();
                $checked_montage = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->montage_start])->andWhere(['end_time' => $event->montage_end])->count();
                $checked_event = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->event_start])->andWhere(['end_time' => $event->event_end])->count();
                $checked_disassembly = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['event_id' => $event_id])->andWhere(['start_time' => $event->disassembly_start])->andWhere(['end_time' => $event->disassembly_end])->count();
            }

            $vacations = [
            'packing' => [],
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];
            $plannedVacations = [
            'packing' => [],
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];

            $overlapingEvents = [
            'packing' => [],
                'montage' => [],
                'event' => [],
                'disassembly' => [],
            ];

            $closeEvents = [
            'packing' => [],
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

            return $this->renderAjax('planboardEkipaForm', [
                'user' => $user,
                'assignedBreks' => $assignedBreks,
                'model' => $event,
                'role_list' => $role_list,
                'user_roles' => $user_roles,
                'userWorkingHoursDataProvider' => $userWorkingHoursDataProvider,
                'userBreaksDataProvider' => $userBreaksDataProvider,
                'checked_packing' =>$checked_packing,
                'checked_montage' => $checked_montage,
                'checked_event' => $checked_event,
                'checked_disassembly' => $checked_disassembly,
                'iconsArray' => $iconList,
                'noOffer' => $no_offer,
                'overlapingEvents' => $overlapingEvents,
                'closeEvents' => $closeEvents,
                'vacations' => $vacations,
                'plannedVacations' => $plannedVacations,
                'inevent' =>$in_event
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

    public function actionAssignUserBreak($user_id, $event_break_id) {
        $count = EventBreaksUser::find()->where(['user_id' => $user_id])->andWhere(['event_break_id' => $event_break_id])->count();
        if ($count == 0) {
            $model = new EventBreaksUser();
            $model->user_id = $user_id;
            $model->event_break_id = $event_break_id;
            $model->save();
        }
    }

    public function actionDeleteUserEventBreak($user_id, $event_break_id) {
        $this->deleteAllEventBreakUser($user_id, $event_break_id);

    }

    public function deleteAllEventBreakUser($user_id, $event_break_id) {
        EventBreaksUser::deleteAll(['event_break_id' => $event_break_id,
            'user_id' => $user_id]);
    }

    public function actionEvents($start = null, $end = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->getEvents($start, $end);
    }

    protected function getEvents($sort_start, $sort_end) {
        $orderModel = PlanboardUserOrder::find()->indexBy('event_id')->where(['user_id' => Yii::$app->user->identity->id])->all();
        exit;
        $query = Event::find();
        $query->joinWith(['departments', 'users', 'eventBreaks']);
        $models = $query->all();
        $events = [];
        /*$event_colors = ['event_base_bg' => Yii::$app->settings->get('main.eventBaseColor'),
            'event_line_bg' => Yii::$app->settings->get('main.eventLineColor'),
            'packing' => Yii::$app->settings->get('main.partyColor'),
            'montage' => Yii::$app->settings->get('main.montageColor'), 'readiness' => 'olive', 'practice' => 'orange',
            'event' => Yii::$app->settings->get('main.partyColor'),
            'disassembly' => Yii::$app->settings->get('main.disassemblyColor'),];*/
        if ($models === null) {
            return;
        }

        foreach ($models as $k => $model) {
            $key = 'events_' . $k;
            // $key = sizeof($events);
            /* @var $model Event; */
            $start = $eventStart  = $model->getStartTimeForCalendar();
            $end = $eventEnd = $model->getTimeEnd();


            $event_border_left = "event-border-left";
            $event_border_right = "event-border-right";
            if (strtotime($start) < strtotime($sort_start)) {
                $event_border_left = $start;
            }
            if (strtotime($end) > strtotime($sort_end)) {
                $event_border_right = $start;
            }

            if ($start == null || $end == null) {
                continue;
            }

            $start = strtotime($start);
            $end = strtotime($end);

            if (isset($sort_start) && isset($sort_end)) {
                $start_time = strtotime($sort_start);
                $end_time = strtotime($sort_end . " 23:59:59");
                if ($start_time > $end) {
                    continue;
                }
                if ($start > $end_time) {
                    continue;
                }
            }

            $full_time = $end - $start;

            $left = 0;
            $right = 0;
            $packing = '';
            $montage = '';
            // $readiness = '';
            // $practice = '';
            $disassembly = '';

            $title = (string)$model->name;
            $event_box = '';
            if ($model->manager) {
                $title .= ' [' . $model->manager->getInitials() . ']';
            }

            if ($full_time > 0) {
                $a = $start;
                $b = strtotime(date('Y-m-d 00:00:00', $a));

                $x = $end;
                $y = strtotime(date('Y-m-d 00:00:00', $x + 24 * 60 * 60 - 1));

                $time_line = $y - $b;
                $left_time = $a - $b;
                $right_time = $y - $x;

                $today = strtotime(date('Y-m-d 00:01'));
                $nextWeek = strtotime("+1 week");

                // $weekNumberStart = date('W', $a);
                // $weekNumberEnd = date('W', $x);
                // if ($weekNumberEnd == $weekNumberStart) {
                if ($a >= $today && $a < $nextWeek && $x > $today && $x <= $nextWeek) {
                    $left = $left_time * 100 / $time_line;
                    $right = $right_time * 100 / $time_line;
                }
                else {
                    //jeśli w innych tygodniach
                    $dayStart = date('N', $a);
                    $dividerStart = 8 - $dayStart;

                    $dayEnd = date('N', $x);
                    $dividerEnd = $dayEnd;

                    $secondsOfDay = 3600 * 24;

                    $left = $left_time / $secondsOfDay * 100;
                    $left = $left / $dividerStart;

                    $right = ($right_time / $secondsOfDay) * 100;
                    $right = $right / $dividerEnd;


                }


                $secondsOfDay = 3600 * 24;


                if (!empty($model->montage_start)) {

                    $montageSize = strtotime($model->montage_end) - strtotime($model->montage_start); //ilość godzin
                    $mS = ($montageSize * 100) / $full_time;

                    $left_dist = strtotime($model->montage_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    $montage = Html::tag('div', '&nbsp;M', ['style' => ['background-color' => $event_colors["montage"],
                            'position' => 'absolute', 'width' => $mS . '%', 'left' => $left_dist . '%',
                            // 'border-right' => '1px solid #898989',
                            'border-left' => '1px solid #898989', 'font-size' => '7px', 'color' => '#867f77',
                            'height' => '100%', 'top' => '0', 'box-sizing' => 'border-box',],
                            'data' => ['start' => $model->montage_start, 'end' => $model->montage_end,]]) . ' ';
                }

                if (!empty($model->readiness_start)) {
                    $event_start = $model->readiness_start;
                }
                else {
                    if (!empty($model->practice_start)) {
                        $event_start = $model->practice_start;
                    }
                    else {
                        $event_start = $model->event_start;
                    }
                }


                if (!empty($model->event_end)) {


                    $eventSize_in_time = strtotime($model->event_end) - strtotime($model->event_start); //ilość godzin

                    $a = strtotime($event_start);
                    $x = strtotime($model->event_end);
                    $eWS = date('W', $a);
                    $eWE = date('W', $x);
                    $dS = date('N', $a);

                    $dE = date('N', $x);


                    if ($a >= $today && $a < $nextWeek && $x > $today && $x <= $nextWeek) {
                        $eventSize = ($eventSize_in_time * 100) / $full_time;
                    }
                    else {
                        $eventSize = abs($eventSize_in_time * 100 / ((($dE - $dS + 1) * $secondsOfDay - 6 * 3600)));
                    }

                    //                    if ($weekNumberEnd != $weekNumberStart) {
                    //                        $eventSize = $eventSize_in_time * 100 / ((($dE - $dS + 1) * $secondsOfDay - 6 * 3600));
                    //                    }
                    //                    else {
                    //                        $eventSize = ($eventSize_in_time * 100) / $full_time;
                    //
                    //                    }

                    $left_dist = strtotime($event_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    //                    if ($a < $today) {
                    //                        $left_dist = 0;
                    //                    }

                    if ($eventSize_in_time > 0) {
                        $event_box = Html::tag('div', '&nbsp;E',
                                [
                                    'class' => 'event',
                                    'style' => [
                                        'background-color' => $event_colors["event"],
                                        'position' => 'absolute',
                                        'width' => $eventSize . '%',
                                        'left' => $left_dist . '%',
                                        'z-index' => '10',
                                        // 'border-right' => '1px solid #898989',
                                        'border-left' => '1px solid #898989',
                                        'font-size' => '7px', 'color' => '#867f77',
                                        'height' => '100%',
                                        'top' => '0',
                                        'box-sizing' => 'border-box',
                                    ],
                                    'data' => [
                                        'start' => $model->event_start,
                                        'end' => $model->event_end,
                                    ]
                                ]) . ' ';
                    }
                }

                if (!empty($model->disassembly_start)) {

                    $disassemblySize = strtotime($model->disassembly_end) - strtotime($model->disassembly_start); //ilość godzin
                    $disassemblySize = ($disassemblySize * 100) / $full_time;

                    $left_dist = strtotime($model->disassembly_start) - $start;
                    $left_dist = $left_dist * 100 / $full_time;

                    if ($disassemblySize > 0) {
                        $disassembly = Html::tag('div', '&nbsp;D', ['style' => ['background-color' => $event_colors["disassembly"],
                            'position' => 'absolute', 'width' => $disassemblySize . '%', 'left' => $left_dist . '%',
                            // 'border-right' => '1px solid #898989',
                            'border-left' => '1px solid #898989', 'font-size' => '7px', 'color' => '#867f77',
                            'height' => '100%', 'top' => '0', 'box-sizing' => 'border-box',],
                            'data' => ['start' => $model->disassembly_start, 'end' => $model->disassembly_end,]

                        ]);
                    }

                }
            }

            $info = $model->getTooltipContent();

            $colors = ArrayHelper::map($model->departments, 'color', 'color');
            $departaments_boxes = '<div class="departament_box" style="width:7px; float:left; padding:2px 0 0 10px; position:relative; z-index:11;">';

            if (!empty($colors)) {
                foreach ($colors as $color) {

                    $departaments_boxes .= Html::tag('div', '&nbsp;', ['class' => 'departament_circle',
                        'style' => ['background' => $color, 'width' => '5px', 'height' => '5px',
                            'border-radius' => '50%', 'margin-bottom' => '1px',],]);

                }

            }
            $departaments_boxes .= '</div>';

            $event_users = $model->eventUsers;;
            $assigned_users = $model->users;
            $event_id = $model->id;

            $assigned_users_count = 0;
            foreach ($event_users as $event_user) {
                if (count($event_user->eventUserRoles)) {
                    $assigned_users_count += count($event_user->eventUserRoles);
                }
                else {
                    $assigned_users_count++;
                }
            }

            $crew_needed_count = 0;
            $offer_error = false;
            if (!isset($model->getPlanningOffers()['error'])) {
                $crew_needed = $model->getCrewNeeded();
                $crew_count_text = "";
                if ($crew_needed) {
                    foreach ($crew_needed as $key => $value) {
                        $crew_needed_count += $value["quantity"];
                    }
                    $crew_count_text = Html::tag('span', '(' . $assigned_users_count . "/" . $crew_needed_count . ')');
                }
            }
            else {
                $offer_error = true;
                $crew_needed_count .= " ".Yii::t('app', "Brak oferty");
            }
            $class_error = '';
            if ($assigned_users_count < $crew_needed_count || $offer_error) {
                $class_error = 'error';
            }
            $title .= " <span class='" . $class_error . "'>" . $assigned_users_count . "/" . $crew_needed_count . "</span>";
            $title_box = Html::tag('div', $title, ['class' => 'title', 'style' => [// 'background' => $backgroud,
                'position' => 'relative', 'z-index' => '11', 'color' => '#000', 'font-size' => '10px',
                'line-height' => '15px', 'padding' => '6px 3px 0',]]);
            $logged_user = Yii::$app->user->identity->id;


            // osoby w widoku generalnym
            $assigned_users_html = '<div class="toggle_box_general">
                    <div class="space-div '.$event_border_left.' '.$event_border_right.'"> </div>
                    <ul id="sortable_users_list_' . $event_id . '" class="fc_assigned_users sortable_users event-ekipa-list ' . $event_border_left . ' ' . $event_border_right . '" data-id="' . $event_id . '" data-eventstart="'.$eventStart.'" data-eventend="'.$eventEnd.'" >';

            // sortowanie userów w widoku generalnym
            unset($user_order);
            $user_order = [];
            foreach ($assigned_users as $user) {
                $order_number = 0;
                $order = PlanboardUserGeneralEventOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $model->id])->andWhere(['user_event' => $user->id])->one();
                if ($order) {
                    $order_number = $order->order_key;
                }
                $user_order[$order_number][] = $user;
            }
            ksort($user_order);
            unset($assigned_users);
            $assigned_users = [];
            foreach ($user_order as $order => $users) {
                foreach ($users as $user) {
                    $assigned_users[] = $user;
                }
            }

            $workingHours = [];
            $breaksHours = [];
            // wyświetlanie userów w widoku generalnym
            foreach ($assigned_users as $_model_key => $user) {
                $user_id = $user->id;

                $event_user = \common\models\EventUser::findOne(['user_id' => $user_id, 'event_id' => $event_id]);
                $orange_color = "";
                if (EventUserRole::find()->where(['event_user_id'=>$event_user->id])->count()>1) {
                    $orange_color = "user_orange_color";
                }

                $workingHours[$user_id] = [];
                $working = EventUserPlannedWrokingTime::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $event_user->event_id])->all();
                foreach ($working as $work) {
                    $workingHours[$user_id][] = [$work->start_time, $work->end_time];
                }

                $breaksHours[$user_id] = [];
                $breaks = EventUserPlannedBreaks::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $event_user->event_id])->all();
                foreach ($breaks as $break) {
                    $breaksHours[$user_id][] = [$break->start_time, $break->end_time, EventBreaks::ICONS[$break->icon]];
                }
                $eventBreaks = EventBreaks::findAll(['event_id' => $event_user->event_id]);
                foreach ($eventBreaks as $eventBreak) {
                    if (EventBreaksUser::find()->where(['event_break_id' => $eventBreak->id])->andWhere(['user_id' => $user->id])->count() == 1) {
                        $breaksHours[$user_id][] = [$eventBreak->start_time, $eventBreak->end_time, EventBreaks::ICONS[$eventBreak->icon]];
                    }
                }

                $userDepartment = "<div class='user-department-box'>";
                $colors = ArrayHelper::map($user->departments, 'color', 'color');
                if ($colors) {
                    foreach ($colors as $color) {
                        $userDepartment .= Html::tag('div', '&nbsp;', ['class' => 'departament_circle',
                            'style' => ['background' => $color, 'width' => '3px', 'height' => '3px',
                                'border-radius' => '50%', 'margin-bottom' => '1px', 'margin-top' => '1px',
                                'margin-left' => '2px',],]);
                    }
                }
                $userDepartment .= "</div>";
                $userDep[$user->id] = $userDepartment;

                $assigned_users_html .= Html::beginTag('li', ['class' => 'fc_user',
                    'data' => [
                        'userid' => $user->id,
                        'start' => $event_user->start_time,
                        'end' => $event_user->end_time,
                        'workinghours' => $workingHours[$user_id],
                        'breakhours' => $breaksHours[$user_id],
                        'eventstart' => $eventStart,
                        'eventend' => $eventEnd,
                        'eventid' => $event_id,
                    ]
                ]);
                $assigned_users_html .= $userDepartment;

                $assigned_users_html .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                $assigned_users_html .= '<div class="time_bg"></div>';
                $assigned_users_html .= "<span class='user_span ".$orange_color."'>".$user->last_name . ' ' . $user->first_name."</span>";
                $assigned_users_html .= Html::endTag('div');
                $assigned_users_html .= Html::endTag('li');
            }

            $assigned_users_html .= '</ul></div>';


            $assigned_vehicles = $model->getAssignedVehicles();
            $vehicles = $assigned_vehicles->getModels();

            if (isset($vehicle_arr)) {
                unset($vehicle_arr);
            }
            $vehicle_arr = [];
            if ($vehicles != null) {
                foreach ($vehicles as $vehicle) {
                    $order = PlanboardVehicleOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $model->id])->andWhere(['vehicle_id' => $vehicle->id]);
                    if ($order->count() == 1) {
                        $vehicle_arr[$order->one()->order_key][] = $vehicle;
                    }
                    else {
                        $vehicle_arr[0][] = $vehicle;
                    }
                }
            }

            ksort($vehicle_arr);
            unset($vehicles);
            $vehicles = [];
            foreach ($vehicle_arr as $index => $veh_arr) {
                foreach ($veh_arr as $veh) {
                    $vehicles[] = $veh;
                }
            }


            $assigned_vehicles_html = '<div class="vehicle_box">
                <ul id="sortable_vehicles_list" class="fc_assigned_vehicles sortable_vehicles event-flota-list ' . $event_border_left . ' ' . $event_border_right . '" data-id="' . $model->id . '" data-eventstart="'.$eventStart.'" data-eventend="'.$eventEnd.'" >';

            $vehicles2 =$model->getAssignedVehiclesByTime();
            echo var_dump($vehicles2);
            exit;
            foreach ($vehicles2 as $veh_id=>$veh_array)
            {
                $total = 0;
                $added = 0;
                foreach ($veh_array as $v)
                {
                    $name = $v['label'];
                    $total += $v['quantity'];
                    $added += $v['added'];
                }
                $assigned_vehicles_html .= Html::beginTag('li',
                    [
                        'style' => 'background-color: #aaa; overflow: hidden; text-overflow: ellipsis;',
                        'data' => [
                            'carid' => $value->id,
                            'eventstart' => $eventStart,
                            'eventend' => $eventEnd,
                            'workinghours' => $working_hours,
                        ]
                    ]);
                $assigned_vehicles_html .= '<strong>'.$name.'</strong> '.$added."/".$total;
                $assigned_vehicles_html .= '</li>';
            }

            // samochody przypisane do eventu
            foreach ($vehicles as $_model_key => $value) {
                $working_hours = [];
                foreach (EventVehicleWorkingHours::find()->where(['event_id' => $event_id])->andWhere(['vehicle_id' => $value->id])->all() as $workinTime) {
                    $working_hours[] = [$workinTime->start_time, $workinTime->end_time ];
                }
                $assigned_vehicles_html .= Html::beginTag('li',
                    [
                        'class' => 'fc_vehicles',
                        'data' => [
                            'carid' => $value->id,
                            'eventstart' => $eventStart,
                            'eventend' => $eventEnd,
                            'workinghours' => $working_hours,
                        ]
                    ]);
                $assigned_vehicles_html .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                $assigned_vehicles_html .= '<div class="time_bg" style="left: 0; right: 0; position: absolute; height: 100%; top: 0;"></div><span class="user_span">';
                $assigned_vehicles_html .= $value->name . ' (' . $value->registration_number . ')</span>';
                $assigned_vehicles_html .= Html::endTag('div');
                $assigned_vehicles_html .= '</li>';
            }

            $assigned_vehicles_html .= '</ul></div>';


            $custom_order = 0;

            if (isset($orderModel[$model->id])) {
                $custom_order = $orderModel[$model->id]->order_key;
            }


            $crew_details_view = "  <div class='toggle_box_details'>
                                    <div class='space-div ".$event_border_left." ".$event_border_right."''> </div>
                                    <div class='user-role-sortable'>";

            if (isset($event_roles)) {
                unset($event_roles);
            }
            $event_roles[] = [];
            foreach ($event_users as $event_user) {
                $roles = $event_user->eventUserRoles;
                foreach ($roles as $role) {

                    $orange_color = "";
                    if (EventUserRole::find()->where(['event_user_id'=>$event_user->id])->count()>1) {
                        $orange_color = "user_orange_color";
                    }

                    $order = PlanboardUserEventRoleOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $model->id])->andWhere(['user_event_role' => $role->user_event_role_id])->one();
                    $order_number = 0;
                    if ($order) {
                        $order_number = $order->order_key;
                    }
                    $event_roles[$order_number][$role->user_event_role_id][] = ["<span class='user_span ".$orange_color."'>".$role->eventUser->user->first_name . " " . $role->eventUser->user->last_name."</span>",
                        $role->eventUser->user->id];
                }
                if (count($roles) == 0) {
                    $order = PlanboardUserEventRoleOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $model->id])->andWhere(['user_event_role' => 0])->one();
                    $order_number = 0;
                    if ($order) {
                        $order_number = $order->order_key;
                    }
                    $user = User::findOne(['id' => $event_user->user_id]);
                    $event_roles[$order_number][0][] = ["<span class='user_span'>".$user->last_name . " " . $user->first_name."</span>", $user->id];
                }
            }
            ksort($event_roles);
            $sort_array = $event_roles;
            unset($event_roles);
            $event_roles[] = [];
            foreach ($sort_array as $array_order) {
                foreach ($array_order as $role_id => $array_of_users) {
                    // sortowanie osób wg. zapisanej kolejności (jeżeli jest, zapisuje się po przeciągnięciu)
                    // user = [first_name, last_name, id]
                    unset($user_order);
                    $user_order[] = [];
                    foreach ($array_of_users as $user) {
                        $order = PlanboardUserEventRoleUsersOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $model->id])->andWhere(['role_id' => $role_id])->andWhere(['event_user' => $user[1]])->one();
                        $order_number = 0;
                        if ($order) {
                            $order_number = $order->order_key;
                        }
                        $user_order[$order_number][] = $user;
                    }
                    ksort($user_order);
                    unset($array_of_users);
                    $array_of_users = [];
                    foreach ($user_order as $order_number => $user_array) {
                        foreach ($user_array as $user) {
                            $array_of_users[] = $user;
                        }
                    }
                    $event_roles[$role_id] = $array_of_users;
                }
            }

            // ******** widok szczegółowy eventu (pracownicy w podziale na departmenty)  ************
            $crew_needed = $model->getAssignedUsersByTime2();
            foreach ($event_roles as $role_id => $users) {
                if (!empty($users)) {
                    if ($role_id != 0) {
                        $role = UserEventRole::findOne(['id' => $role_id]);
                        $user_count = 0;
                        $users_array = [];
                        foreach  ($users as $u)
                        {
                            if (!in_array($u[1], $users_array))
                            {
                                $users_array[] = $u[1];
                                $user_count++;
                            }
                        }
                        /*
                        if (!isset($crew_needed[$role_id])) {
                            $crew_needed[$role_id] = $model->getCrewNeeded2($role_id)[$role_id];
                        }*/
                        $crew_details_view .= "
                        <ul data-eventid='" . $model->id . "' data-role='" . $role_id . "' class='role-div " . $event_border_left . " " . $event_border_right . "' style='border:1px solid black;'>
                            <li style='background-color:#aaa; overflow: hidden; text-overflow: ellipsis;'><strong>" . $role->name . "</strong> " . $user_count . "/" . $crew_needed[$role_id]["quantity"] . "</li>
                        <li>";

                        $crew_details_view .= "
                        <ul data-id='" . $model->id . "' data-role='" . $role_id . "'  class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' > ";


                        foreach ($users as $user) {
                            $event_user = EventUser::findOne(['user_id' => $user[1], 'event_id' => $event_id]);
                            $crew_details_view .= Html::beginTag('li',
                                [
                                    'class' => 'fc_user',
                                    'data' => [
                                        'role' => $role_id,
                                        'userid' => $user[1],
                                        'start' => $event_user->start_time,
                                        'end' => $event_user->end_time,
                                        'workinghours' => $workingHours[$user[1]],
                                        'breakhours' => $breaksHours[$user[1]],
                                        'eventstart' => $eventStart,
                                        'eventend' => $eventEnd,
                                        'eventid' => $event_id,
                                    ]
                                ]);
                            if (isset($userDep[$user[1]]) && $userDep[$user[1]]) {
                                $crew_details_view .= $userDep[$user[1]];
                            }
                            $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                            $crew_details_view .= '<div class="time_bg"></div>';
                            $crew_details_view .= $user[0];
                            $crew_details_view .= Html::endTag('div');
                            $crew_details_view .= Html::endTag('li');
                        }
                        $crew_details_view .= "</ul></li></ul>";
                    }
                    else {
                        $crew_details_view .= "
                            <ul class='role-div " . $event_border_left . " " . $event_border_right . "'>
                                <li style='background-color:#aaa; overflow: hidden; text-overflow: ellipsis;'><strong>".Yii::t('app', "Nieprzypisane")."</strong> " . count($users) . "/" . 0 . " </li>
                            <li>
                            <ul data-id='" . $model->id . "' data-role='0' class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' > ";

                        foreach ($users as $user) {
                            $event_user = EventUser::findOne(['user_id' => $user[1], 'event_id' => $event_id]);

                            $crew_details_view .= Html::beginTag('li',
                                [
                                    'class' => 'fc_user',
                                    'data' => [
                                        'role' => 0,
                                        'userid' => $user[1],
                                        'start' => $event_user->start_time,
                                        'end' => $event_user->end_time,
                                        'workinghours' => $workingHours[$user[1]],
                                        'breakhours' => $breaksHours[$user[1]],
                                        'eventstart' => $eventStart,
                                        'eventend' => $eventEnd,
                                        'eventid' => $event_id,
                                    ]
                                ]);
                            if (isset($userDep[$user[1]]) && $userDep[$user[1]]) {
                                $crew_details_view .= $userDep[$user[1]];
                            }
                            $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                            $crew_details_view .= '<div class="time_bg"></div>';
                            $crew_details_view .= $user[0];
                            $crew_details_view .= Html::endTag('div');
                            $crew_details_view .= Html::endTag('li');
                        }
                        $crew_details_view .= "</ul></li></ul>";
                    }
                }
            }

            if (!$offer_error) {
                $offers = $model->getPlanningOffers();
                foreach ($offers as $offer) {
                    foreach ($offer->roles as $role) {
                        if (!isset($event_roles[$role->id])) {
                            $crew_details_view .= "
                            <ul class='role-div " . $event_border_left . " " . $event_border_right . "'>
                                <li style='background-color:#aaa; overflow: hidden; text-overflow: ellipsis;'><strong>" . $role->name . "</strong> 0/" . $crew_needed[$role->id]["quantity"] . " </li>
                            <li>
                            <ul data-id='" . $model->id . "' data-role='" . $role->id . "' class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' style='min-height:10px; border-bottom:1px solid black;' ></ul></li></ul> ";
                        }
                    }
                }
            }
            $crew_details_view .= "</div>";

            $spaceDiv = '<div class="space-between-events"></div>';

            $events[] = [
                'id' => $model->id,
                'order' => $custom_order,
                'info' => $info,
                'title' => $title_box,
                'departaments' => $departaments_boxes,
                'event' => $event_box,
                'start' => date('Y-m-d H:i:s', $start),
                'end' => date('Y-m-d H:i:s', $end),
                'left' => $left,
                'right' => $right,
                'line_bg' => $event_colors["event_line_bg"],
                'base_bg' => $event_colors["event_base_bg"],
                'montage' => $montage,
                'disassembly' => $disassembly,
                'users' => $assigned_users_html,
                'vehicles' => $assigned_vehicles_html,
                'crew_details' => $crew_details_view,
                'event_space' => $spaceDiv,
            ];
        }

        return $events;
    }

    public function actionUpdateOrder() {
        $data = isset($_POST["data"]) ? $_POST["data"] : [];

        $user_id = Yii::$app->user->identity->id;
        foreach ($data as $key => $value) {

            $orderModel = PlanboardUserOrder::find()->where(['user_id' => $user_id, 'event_id' => $value['id']])->one();
            if (!$orderModel) {
                $orderModel = new PlanboardUserOrder();
                $orderModel->user_id = $user_id;
                $orderModel->event_id = (int)$value['id'];
            }

            $orderModel->order_key = (int)$value["order"];

            $orderModel->save();
        }
    }

    public function actionDeleteAllOrderEventRole($event_id) {
        $user_id = Yii::$app->user->id;
        PlanboardUserEventRoleOrder::deleteAll(['event_id' => $event_id, 'user_id' => $user_id]);
    }

    public function actionUpdateOrderEventRole($event_id, $role_id, $order_key) {
        $user_id = Yii::$app->user->id;
        $order = new PlanboardUserEventRoleOrder();
        $order->user_id = $user_id;
        $order->event_id = $event_id;
        $order->user_event_role = $role_id;
        $order->order_key = $order_key;
        $order->save();
    }

    public function actionDeleteAllOrderEventRoleUsers($event_id, $role_id) {
        $user_id = Yii::$app->user->id;
        PlanboardUserEventRoleUsersOrder::deleteAll(['event_id' => $event_id, 'user_id' => $user_id,
            'role_id' => $role_id]);
    }

    public function actionUpdateOrderEventRoleUser($event_id, $role_id, $event_user, $order_key) {
        $user_id = Yii::$app->user->identity->id;
        $order = new PlanboardUserEventRoleUsersOrder();
        $order->user_id = $user_id;
        $order->event_id = $event_id;
        $order->role_id = $role_id;
        $order->event_user = $event_user;
        $order->order_key = $order_key;
        $order->save();
    }

    public function actionDeleteAllOrderEventGeneralUser($event_id) {
        $user_id = Yii::$app->user->id;
        PlanboardUserGeneralEventOrder::deleteAll(['event_id' => $event_id, 'user_id' => $user_id]);
    }

    public function actionUpdateOrderVehicle($event_id, $vehicle_id, $order_key) {
        $user_id = Yii::$app->user->id;
        $order = new PlanboardVehicleOrder();
        $order->user_id = $user_id;
        $order->event_id = $event_id;
        $order->vehicle_id = $vehicle_id;
        $order->order_key = $order_key;
        $order->save();
    }

    public function actionDeleteAllVehicleOrder($event_id) {
        $user_id = Yii::$app->user->identity->id;
        PlanboardVehicleOrder::deleteAll(['event_id' => $event_id, 'user_id' => $user_id]);
    }

    public function actionUpdateOrderEventGeneralUser($event_id, $event_user, $order_key) {
        $user_id = Yii::$app->user->id;
        $order = new PlanboardUserGeneralEventOrder();
        $order->user_id = $user_id;
        $order->event_id = $event_id;
        $order->user_event = $event_user;
        $order->order_key = $order_key;
        $order->save();
    }

    public function actionDeleteUserWorkingHours($id) {
        return EventUserPlannedWrokingTime::deleteAll(['id' => $id]);
    }

    public function actionDeleteUserBreak($id) {
        return EventUserPlannedBreaks::deleteAll(['id' => $id]);
    }

    public function actionDeleteVehicleWorkingHours($id) {
        return EventVehicleWorkingHours::deleteAll(['id' => $id]);
    }

    public function actionIsUserAssignedToEvent($user_id, $event_id) {
        return EventUser::find()->where(['user_id'=>$user_id])->andWhere(['event_id'=>$event_id])->count();
    }

    public function actionTest() {
        return $this->render('test');
    }



    // ************** Nowy planboard **************** //
    // ************** Nowy planboard **************** //
    // ************** Nowy planboard **************** //
    // ************** Nowy planboard **************** //
    // ************** Nowy planboard **************** //


    public function actionGetEvents($start, $end) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // array of events (as array)
        $events = $this->getEventsForVis($start, $end, json_decode(Yii::$app->request->post('ids')));
        foreach ($events as $i => $event) {
            $eventModel = Event::findOne(['id' => $event['id']]);
            $events[$i]['start'] = $eventModel->getStartTimeForCalendar();
            $events[$i]['end'] = $eventModel->getTimeEnd();
            $events[$i]['tooltip'] = $eventModel->getTooltipContent();


            $workingHours = [];
            $breaksHours = [];
            foreach ($eventModel->users as $_model_key => $user) {
                $user_id = $user->id;

                $event_user = EventUser::findOne(['user_id' => $user_id, 'event_id' => $eventModel->id]);
                $orange_color = "";
                if (EventUserRole::find()->where(['event_user_id' => $event_user->id])->count() > 1) {
                    $orange_color = "user_orange_color";
                }

                $workingHours[$user_id] = [];
                $working = EventUserPlannedWrokingTime::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $event_user->event_id])->all();
                foreach ($working as $work) {
                    $workingHours[$user_id][] = [$work->start_time, $work->end_time];
                }

                $breaksHours[$user_id] = [];
                $breaks = EventUserPlannedBreaks::find()->where(['user_id' => $user->id])->andWhere(['event_id' => $event_user->event_id])->all();
                foreach ($breaks as $break) {
                    $breaksHours[$user_id][] = [$break->start_time, $break->end_time, EventBreaks::ICONS[$break->icon]];
                }
                $eventBreaks = EventBreaks::findAll(['event_id' => $event_user->event_id]);
                foreach ($eventBreaks as $eventBreak) {
                    if (EventBreaksUser::find()->where(['event_break_id' => $eventBreak->id])->andWhere(['user_id' => $user->id])->count() == 1) {
                        $breaksHours[$user_id][] = [$eventBreak->start_time, $eventBreak->end_time,
                            EventBreaks::ICONS[$eventBreak->icon]];
                    }
                }
            }

            $events[$i]['content'] =
                "<div class='event_time_wrapper' data-eventid='".$eventModel->id."' data-start='".$events[$i]['start']."' data-end='".$events[$i]['end']."'>" .
                $this->getEventHeaderForVis($event, $eventModel) .
                $this->getEventTitle($eventModel) .
                $this->getEventDepartmentCircles($eventModel) .
                "</div>" .
                "<div class='event_content'>" .
                "<div class='toggle_box_general'>".
                $this->getEventGeneralBox($eventModel, $events[$i]['start'], $events[$i]['end'], $workingHours, $breaksHours) .
                "</div>" .
                "<div class='toggle_box_details'>" .
                $this->getEventDetailsBox($eventModel, $events[$i]['start'], $events[$i]['end'], $workingHours, $breaksHours) .
                "</div>" .
                "<div class='vehicle_box'>" .
                $this->getEventVehicleBox($eventModel, $events[$i]['start'], $events[$i]['end']) .
                "</div>" .
                "</div>";

            $events[$i]['order'] = 999;
            $order_row = PlanboardUserOrder::find()->where(['user_id' => Yii::$app->user->id])->andWhere(['event_id' => $event['id']])->one();
            if ($order_row) {
                $events[$i]['order'] = $order_row->order_key;
            }
        }

        return $events;
    }

    public function getEventTitle(Event $eventModel) {
        $title = $eventModel->name;
        if ($eventModel->manager) {
            $title .= ' [' . $eventModel->manager->getInitials() . ']';
        }

        $event_users = $eventModel->eventUsers;;
        $assigned_users_count = 0;
        foreach ($event_users as $event_user) {
            if (count($event_user->eventUserRoles)) {
                $assigned_users_count += count($event_user->eventUserRoles);
            }
            else {
                $assigned_users_count++;
            }
        }

        $class_error = '';
        $crew_needed_count = 0;
        if (!isset($eventModel->getPlanningOffers()['error'])) {
            $crew_needed = $eventModel->getCrewNeeded();
            if ($crew_needed) {
                foreach ($crew_needed as $key => $value) {
                    $crew_needed_count += $value["quantity"];
                }
            }
        }
        else {
            $class_error = 'error';
            //$crew_needed_count .= " ".Yii::t('app', "Brak oferty");
        }
        if ($assigned_users_count < $crew_needed_count) {
            $class_error = 'error';
        }

        $title .= " <span class='" . $class_error . "'>" . $assigned_users_count . "/" . $crew_needed_count . "</span>";
        return "<div class='event_title'>".$title."</div>";
    }

    public function getEventDepartmentCircles(Event $event) {
        $colors = ArrayHelper::map($event->departments, 'color', 'color');
        $departaments_boxes = '<div class="departament_box" style="width:7px; float:left; padding:2px 0 0 10px; position:relative; z-index:11;">';

        if (!empty($colors)) {
            foreach ($colors as $color) {

                $departaments_boxes .= Html::tag('div', '&nbsp;', ['class' => 'departament_circle',
                    'style' => ['background' => $color, 'width' => '5px', 'height' => '5px',
                        'border-radius' => '50%', 'margin-bottom' => '1px',],]);

            }

        }
        $departaments_boxes .= '</div>';
        return $departaments_boxes;
    }

    public function getEventHeaderForVis($event, $eventModel) {
        $start = $eventModel->getStartTimeForCalendar();
        $end = $eventModel->getTimeEnd();
        $whole_event_period = abs(strtotime($end) - strtotime($start));
        $was_montage = false;
        $was_event = false;
        $was_packing = false;
        $left_border_class = 'left_border';

        $bar = '';
        foreach ($event['schedules'] as $schedule)
        {
            if ($schedule['start_time'])
            {
                $period = abs(strtotime($schedule['start_time']) - strtotime($schedule['end_time']));
                $percents = $period / $whole_event_period * 100;
                $left_margin = abs(strtotime($start) - strtotime($schedule['start_time'])) / $whole_event_period * 100;
                $bar .= "<div style='width: ".$percents."%; left: ".$left_margin."%; background-color:".$schedule['color']."' class='time_period packing_period left_border'><span class='event_timeline_letter'>".$schedule['prefix']."</span></div>";
            }
        }
        /*
        if ($event['packing_start'] && $event['packing_end']) {
            $was_packing = true;
            $packing_period = abs(strtotime($event['packing_start']) - strtotime($event['packing_end']));
            $packing_percents = $packing_period / $whole_event_period * 100;
            $bar .= "<div style='width: ".$packing_percents."%;' class='time_period packing_period left_border'><span class='event_timeline_letter'>P</span></div>";
        }

        if ($event['montage_start'] && $event['montage_end']) {
            $was_montage = true;
            $left_border = $left_border_class;
            if ($was_packing && $event['packing_end'] == $event['montage_start']) {
                $left_border = '';
            }
            $left_margin = abs(strtotime($start) - strtotime($event['montage_start'])) / $whole_event_period * 100;

            $montage_period = abs(strtotime($event['montage_start']) - strtotime($event['montage_end']));
            $montage_percents = $montage_period / $whole_event_period * 100;
            $bar .= "<div style='width: ".$montage_percents."%; left: ".$left_margin."%' class='time_period montage_period ".$left_border."'><span class='event_timeline_letter'>M</span></div>";
        }
        if ($event['event_start'] && $event['event_end']) {
            $was_event = true;
            $left_border = $left_border_class;
            if ($was_montage && $event['montage_end'] == $event['event_start']) {
                $left_border = '';
            }
            $event_period = abs(strtotime($event['event_start']) - strtotime($event['event_end']));
            $event_percents = $event_period / $whole_event_period * 100;
            $left_margin = abs(strtotime($start) - strtotime($event['event_start'])) / $whole_event_period * 100;
            $bar .= "<div style='width: ".$event_percents."%; left: ".$left_margin."%;' class='time_period event_period ".$left_border."'><span class='event_timeline_letter'>E</span></div>";
        }
        if ($event['disassembly_start'] && $event['disassembly_end']) {
            $disessembly_period = abs(strtotime($event['disassembly_start']) - strtotime($event['disassembly_end']));
            $left_border = $left_border_class;
            if ($was_montage && $event['montage_end'] == $event['disassembly_start']) {
                $left_border = '';
            }
            if ($was_event && $event['event_end'] == $event['disassembly_start']) {
                $left_border = '';
            }
            $disessembly_percents = $disessembly_period / $whole_event_period * 100;
            $left_margin = abs(strtotime($start) - strtotime($event['disassembly_start'])) / $whole_event_period * 100;
            $bar .= "<div style='width: ".$disessembly_percents."%; left: ".$left_margin."%;' class='time_period disessembly_period ".$left_border."'><span class='event_timeline_letter'>D</span></div>";
        }
        */

        return $bar;
    }

    private function getEventsForVis($start, $end, $ids = null) {
        $date_start = $start;
        $date_end =   $end;
        $start = new DateTime($start . "00:00");
        $end = new DateTime($end . "23:59");
        if (!$ids)
            $ids = [];
        $events_to_return = [];
        
        $statuts = \common\helpers\ArrayHelper::map(\common\models\EventStatut::find()->where(['show_in_plantimeline'=>1])->asArray()->all(), 'id', 'id');
        $events = Event::find()->where(['status'=>$statuts])->andWhere(['NOT IN', 'id', $ids])->andWhere([
                                'or',
                                ['and', ['<', 'event_start', $date_end], ['>', 'event_end', $date_start]],
                                ['and', ['<', 'packing_start', $date_end], ['>', 'packing_end', $date_start]],
                                ['and', ['<', 'montage_start', $date_end], ['>', 'montage_end', $date_start]],
                                ['and', ['<', 'disassembly_start', $date_end], ['>', 'disassembly_end', $date_start]],
                            ])->all();
        foreach ($events as $event) {
            $event_start = new DateTime($event->getTimeStart());
            $event_end = new DateTime($event->getTimeEnd());
            $e = ArrayHelper::toArray($event);
            $e['schedules'] = [];
            foreach ($event->eventSchedules as $s)
            {
                $e['schedules'][] = ArrayHelper::toArray($s);
            }
            if ($event->getTimeStart() == null || $event_end == $event->getTimeEnd()) {
                continue;
            }
            if ($event_start <= $start && $event_end >= $end) {
                $events_to_return[] = $e;
                continue;
            }
            if ($event_start >= $start && $event_end <= $end) {
                $events_to_return[] = $e;
                continue;
            }
            if ($event_start <= $start && $event_end >= $start) {
                $events_to_return[] = $e;
                continue;
            }
            if ($event_start <= $end && $event_end >= $end) {
                $events_to_return[] = $e;
                continue;
            }
        }

        return $events_to_return;
    }

    private function getEventDetailsBox(Event $event, $eventStart, $eventEnd, $workingHours, $breaksHours) {
        $crew_details_view = "<div class='user-role-sortable'>";
        $workingHours2 = [];
        $users2 = [];
        $logged_user = Yii::$app->user->identity->id;
        //if (!isset($event->getPlanningOffers()['error'])) {
            $crew_needed = $event->getAssignedUsersByTime2();
        //}

        if (isset($event_roles)) {
            unset($event_roles);
        }
        $event_roles[] = [];
        foreach ($event->eventUsers as $event_user) {
            $roles = $event_user->eventUserRoles;
            foreach ($roles as $role) {

                $orange_color = "";
                if (EventUserRole::find()->where(['event_user_id'=>$event_user->id])->count()>1) {
                    $orange_color = "user_orange_color";
                }
                if ($event_user->confirm)
                {
                    $orange_color .=" user_confirm_plantimeline";
                }

                $order = PlanboardUserEventRoleOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $event->id])->andWhere(['user_event_role' => $role->user_event_role_id])->one();
                $order_number = 0;
                if ($order) {
                    $order_number = $order->order_key;
                }
                $event_roles[$order_number][$role->user_event_role_id][] = ["<span class='user_span ".$orange_color."'>".$role->eventUser->user->first_name . " " . $role->eventUser->user->last_name."</span>",
                    $role->eventUser->user->id];
            }
            if (count($roles) == 0) {
                $order = PlanboardUserEventRoleOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $event->id])->andWhere(['user_event_role' => 0])->one();
                $order_number = 0;
                if ($order) {
                    $order_number = $order->order_key;
                }
                $user = User::findOne(['id' => $event_user->user_id]);
                $event_roles[$order_number][0][] = ["<span class='user_span'>".$user->last_name . " " . $user->first_name."</span>", $user->id];
            }
        }
        ksort($event_roles);
        $sort_array = $event_roles;
        unset($event_roles);
        $event_roles[] = [];
        foreach ($sort_array as $array_order) {
            foreach ($array_order as $role_id => $array_of_users) {
                // sortowanie osób wg. zapisanej kolejności (jeżeli jest, zapisuje się po przeciągnięciu)
                // user = [first_name, last_name, id]
                unset($user_order);
                $user_order = [];
                foreach ($array_of_users as $user) {
                    $order = PlanboardUserEventRoleUsersOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $event->id])->andWhere(['role_id' => $role_id])->andWhere(['event_user' => $user[1]])->one();
                    $order_number = 0;
                    if ($order) {
                        $order_number = $order->order_key;
                    }
                    $user_order[$order_number][] = $user;
                }
                ksort($user_order);
                unset($array_of_users);
                $array_of_users = [];
                foreach ($user_order as $order_number => $user_array) {
                    foreach ($user_array as $user) {
                        $array_of_users[] = $user;
                    }
                }
                $event_roles[$role_id] = $array_of_users;
            }
        }

        // ******** widok szczegółowy eventu (pracownicy w podziale na departmenty)  ************
        foreach ($event_roles as $role_id => $users) {
            $users2 = [];
            
            //if (!empty($users)) {
                if ($role_id != 0) {
                    $role = UserEventRole::findOne(['id' => $role_id]);
                    $quantity = 0;
                    $workers_counter = 0; 
                    $work_content = "";
                    $periods = [];
                    if (isset($crew_needed[$role_id])){
                    foreach ($crew_needed[$role_id] as $key => $work)
                    {
                        $quantity+=$work['quantity'];
                        $workers_counter+=$work['added'];
                            $work_content .=$work['schedule']->prefix.": ".$work['added']."/".$work['quantity']." ";
                            $periods[$key]['label'] = $work['added']."/".$work['quantity'];
                            $periods[$key]['start'] = $work['schedule']->start_time;
                            $periods[$key]['end'] = $work['schedule']->end_time;
                            if ($work['added']>=$work['quantity'])
                                $periods[$key]['color'] = "#1ab394";
                            else
                                $periods[$key]['color'] = "#ed5565";
                        
                    }
                    }
                    if (!$workers_counter)
                        $workers_counter = count($users);
                    $crew_details_view .= "
                        <ul data-eventid='" . $event->id . "' data-role='" . $role_id . "' class='role-div' style='border:1px solid black; margin-bottom:2px;'>
                            <li style='background-color:#aaa; overflow: hidden;text-overflow: ellipsis;'><a href='/admin/crew/manage-ajax?id=".$event->id."&role_id=".$role_id."' class='assign-users-button' style='color:black' data-eventid=".$event->id."><strong>" . $role->name . "</strong> " . $workers_counter . "/" . $quantity . " <i class='fa fa-plus'></i></a></li>
                        ";
                    $crew_details_view .= Html::beginTag('li',
                            [
                                'class' => 'fc_periods',
                                'style' =>'height:16px; position:relative;',
                                'data' => [
                                    'role' => $role_id,
                                    'start' => $event_user->start_time,
                                    'end' => $event_user->end_time,
                                    'eventstart' => $eventStart,
                                    'eventend' => $eventEnd,
                                    'eventid' => $event->id,
                                ]
                            ]);

                        $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                        $crew_details_view .="<div class='user-department-box'></div>";
                        $crew_details_view .= $this->generateTimeLine2($eventStart, $eventEnd, $periods);
                        $crew_details_view .= Html::endTag('div');
                        $crew_details_view .= Html::endTag('li');
                    //$crew_details_view .= '<li>'.$work_content."</li>";
                    $crew_details_view .= "<li><ul data-id='" . $event->id . "' data-role='" . $role_id . "'  class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' > ";
                    foreach ($users as $user)
                    {
                            $event_user = EventUser::findOne(['user_id' => $user[1], 'event_id' => $event->id]);
                            $event_user_role = EventUserRole::find()->where(['event_user_id'=>$event_user->id])->andWhere(['user_event_role_id'=>$role_id])->all();
                            $workingHours2[$user[1]] = [];
                            $workings = [];
                            foreach ($event_user_role as $work) {
                                
                                $users2[$user[1]] = $user;
                                    if (isset($work->working)){
                                        $workings[] = $work->working->id;
                                        $workingHours2[$user[1]][] = [$work->working->start_time, $work->working->end_time, null, null];

                                    }

                                }
                            $event_user_role = EventUserRole::find()->where(['event_user_id'=>$event_user->id])->andWhere(['<>', 'user_event_role_id',$role_id])->all();
                             foreach ($event_user_role as $work) {
                                
                                $users2[$user[1]] = $user;
                                    if (isset($work->working)){
                                        if (!in_array($work->working->id, $workings))
                                            $workingHours2[$user[1]][] = [$work->working->start_time, $work->working->end_time, null, $work->userEventRole->name];

                                    }

                                }                           
                    }

                    foreach ($users2 as $user) {
                        $event_user = EventUser::findOne(['user_id' => $user[1], 'event_id' => $event->id]);
                        $crew_details_view .= Html::beginTag('li',
                            [
                                'class' => 'fc_user',
                                'data' => [
                                    'role' => $role_id,
                                    'userid' => $user[1],
                                    'start' => $event_user->start_time,
                                    'end' => $event_user->end_time,
                                    'workinghours' => $workingHours2[$user[1]],
                                    'breakhours' => $breaksHours[$user[1]],
                                    'eventstart' => $eventStart,
                                    'eventend' => $eventEnd,
                                    'eventid' => $event->id,
                                ]
                            ]);

                        $crew_details_view .=  $this->getUserDepartments(User::findOne($user[1]));
                        $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                        $crew_details_view .= $this->generateTimeLine($eventStart, $eventEnd, $workingHours2[$user[1]], $breaksHours[$user[1]]);
                        $crew_details_view .= $user[0];
                        $crew_details_view .= Html::endTag('div');
                        $crew_details_view .= Html::endTag('li');
                    }
                    $crew_details_view .= "</ul></li></ul>";
               // }

            }
        }

        $crew_details_view .= "
                            <ul data-eventid='" . $event->id . "' data-role='0' class='role-div'  style='border:1px solid black; margin-bottom:2px;'>
                                <li  style='background-color:#aaa; overflow: hidden; text-overflow: ellipsis;'><strong>".Yii::t('app', "Nieprzypisane")."</strong> " . count($event_roles[0]) . "/" . 0 . " </li>
                            <li>

                            <ul data-id='" . $event->id . "' data-role='0' class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' > ";
        foreach ($event_roles[0] as $user) {
            $event_user = EventUser::findOne(['user_id' => $user[1], 'event_id' => $event->id]);

            $crew_details_view .= Html::beginTag('li',
                [
                    'class' => 'fc_user',

                    'data' => [
                        'role' => 0,
                        'userid' => $user[1],
                        'start' => $event_user->start_time,
                        'end' => $event_user->end_time,
                        'workinghours' => $workingHours[$user[1]],
                        'breakhours' => $breaksHours[$user[1]],
                        'eventstart' => $eventStart,
                        'eventend' => $eventEnd,
                        'eventid' => $event->id,
                    ]
                ]);

            $crew_details_view .=  $this->getUserDepartments(User::findOne($user[1]));
            $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
            $crew_details_view .= '<div class="time_bg"></div>';
            $crew_details_view .= $user[0];
            $crew_details_view .= Html::endTag('div');
            $crew_details_view .= Html::endTag('li');
        }
        $crew_details_view .= "</ul></li></ul>";
                foreach ($crew_needed as $role_id => $val) {
                        $quantity = 0;
                        $work_content = "";
                        $workers_counter = 0;
                        $periods = [];
                    if (!isset($event_roles[$role_id])) {
                        $role = UserEventRole::findOne(['id' => $role_id]);
                    foreach ($crew_needed[$role_id] as $key => $work)
                    {
                        
                        $quantity+=$work['quantity'];
                        $workers_counter+=$work['added'];
                            $work_content .=$work['schedule']->prefix.": ".$work['added']."/".$work['quantity']." ";
                            $periods[$key]['label'] = $work['added']."/".$work['quantity'];
                            $periods[$key]['start'] = $work['schedule']->start_time;
                            $periods[$key]['end'] = $work['schedule']->end_time;
                            if ($work['added']>=$work['quantity'])
                                $periods[$key]['color'] = "#1ab394";
                            else
                                $periods[$key]['color'] = "#ed5565";
                        
                    }
                    $crew_details_view .= "
                        <ul data-eventid='" . $event->id . "' data-role='" . $role_id . "' class='role-div' style='border:1px solid black; margin-bottom:2px;'>
                            <li style='background-color:#aaa; overflow: hidden;text-overflow: ellipsis;'><a href='/admin/crew/manage-ajax?id=".$event->id."&role_id=".$role_id."' class='assign-users-button' style='color:black' data-eventid=".$event->id."><strong>" . $role->name . "</strong> " . $workers_counter . "/".$quantity."<i class='fa fa-plus'></i></a></li>
                        ";
                    $crew_details_view .= Html::beginTag('li',
                            [
                                'class' => 'fc_periods',
                                'style' =>'height:16px; position:relative;',
                                'data' => [
                                    'role' => $role_id,
                                    'start' => $eventStart,
                                    'end' => $eventEnd,
                                    'eventstart' => $eventStart,
                                    'eventend' => $eventEnd,
                                    'eventid' => $event->id,
                                ]
                            ]);

                        $crew_details_view .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                        $crew_details_view .="<div class='user-department-box'></div>";
                        $crew_details_view .= $this->generateTimeLine2($eventStart, $eventEnd, $periods);
                        $crew_details_view .= Html::endTag('div');
                        $crew_details_view .= Html::endTag('li');
                        $crew_details_view .= "<li><ul data-id='" . $event->id . "' data-role='" . $role->id . "' class='fc_assigned_users sortable_users sortable_users_details event-ekipa-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' style='min-height:10px;' ></ul></li></ul> ";
                    }
                }
        
        $crew_details_view .= "</div>";
        return $crew_details_view;
    }

    private function getEventGeneralBox(Event $event, $eventStart, $eventEnd, $workingHours, $breaksHours) {
        $logged_user = Yii::$app->user->identity->id;

        // osoby w widoku generalnym
        $assigned_users_html = '<div class="space-div "> </div>
                    <ul id="sortable_users_list_' . $event->id . '" class="sortable_users event-ekipa-list " data-id="' . $event->id . '" data-eventstart="'.$eventStart.'" data-eventend="'.$eventEnd.'" >';

        // sortowanie userów w widoku generalnym
        unset($user_order);
        $user_order = [];
        foreach ($event->users as $user) {
            $order_number = 0;
            $order = PlanboardUserGeneralEventOrder::find()->where(['user_id' => $logged_user])->andWhere(['event_id' => $event->id])->andWhere(['user_event' => $user->id])->one();
            if ($order) {
                $order_number = $order->order_key;
            }
            $user_order[$order_number][] = $user;
        }
        ksort($user_order);
        unset($assigned_users);
        $assigned_users = [];
        foreach ($user_order as $order => $users) {
            foreach ($users as $user) {
                $assigned_users[] = $user;
            }
        }

        // wyświetlanie userów w widoku generalnym
        foreach ($assigned_users as $_model_key => $user) {
            $user_id = $user->id;

            $event_user = EventUser::findOne(['user_id' => $user_id, 'event_id' => $event->id]);
            $orange_color = "";
            if (EventUserRole::find()->where(['event_user_id'=>$event_user->id])->count()>1) {
                $orange_color = "user_orange_color";
            }

            $assigned_users_html .= Html::beginTag('li', ['class' => 'fc_user',
                'data' => [
                    'userid' => $user->id,
                    'start' => $event_user->start_time,
                    'end' => $event_user->end_time,
                    'workinghours' => $workingHours[$user_id],
                    'breakhours' => $breaksHours[$user_id],
                    'eventstart' => $eventStart,
                    'eventend' => $eventEnd,
                    'eventid' => $event->id,
                ]
            ]);
            $assigned_users_html .= $this->getUserDepartments($user);

            $assigned_users_html .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
            $assigned_users_html .= $this->generateTimeLine($eventStart, $eventEnd, $workingHours[$user_id], $breaksHours[$user_id]);
            $assigned_users_html .= "<span class='user_span ".$orange_color."'>".$user->last_name . ' ' . $user->first_name."</span>";
            $assigned_users_html .= Html::endTag('div');
            $assigned_users_html .= Html::endTag('li');
        }

        $assigned_users_html .= '</ul>';
        return $assigned_users_html;
    }

    private function getUserDepartments(User $user) {
        $userDepartment = "<div class='user-department-box'>";
        $colors = ArrayHelper::map($user->departments, 'color', 'color');
        if ($colors) {
            foreach ($colors as $color) {
                $userDepartment .= Html::tag('div', '&nbsp;', ['class' => 'departament_circle',
                    'style' => ['background' => $color, 'width' => '3px', 'height' => '3px',
                        'border-radius' => '50%', 'margin-bottom' => '1px', 'margin-top' => '1px',
                        'margin-left' => '2px',],]);
            }
        }
        $userDepartment .= "</div>";
        return $userDepartment;
    }

    private function generateTimeLine($eventStart, $eventEnd, $workingHours, $breaksHours = null) {
        $bar =  '<div class="time_bg">';
        $bar .= $this->generateTimePeriod($eventStart, $eventEnd, $workingHours, 'work_period');
        if ($breaksHours) {
            $bar .= $this->generateTimePeriod($eventStart, $eventEnd, $breaksHours, 'break_period');
        }
        $bar .= "</div>";
        return $bar;
    }

    private function generateTimeLine2($eventStart, $eventEnd, $periods) {
        $bar =  '<div class="time_bg">';
        $bar .= $this->generateTimePeriod2($eventStart, $eventEnd, $periods);
        $bar .= "</div>";
        return $bar;
    }

    private function generateTimePeriod($eventStart, $eventEnd, $periods, $class) {
        $eventStart = new DateTime($eventStart);
        $eventEnd = new DateTime($eventEnd);
        $whole_event_period = abs($eventStart->getTimestamp() - $eventEnd->getTimestamp());
        $bar = '';
        foreach ($periods as $period) {
            $start = new DateTime($period[0]);
            $end = new DateTime($period[1]);
            if ($start < $eventStart) {
                $start = $eventStart;
            }
            if ($end > $eventEnd) {
                $end = $eventEnd;
            }
            $icon = null;
            if (isset($period[2]) && !is_null($period[2])) {
                $icon = "<span class='glyphicon glyphicon-".$period[2]."'></span>";
            }
            if (isset($period[3]) && !is_null($period[3])) {
                $class2 = $class." second-work-period";
                if ($whole_event_period<(40*3600))
                {
                if ((abs($start->getTimestamp() - $eventStart->getTimestamp()) / $whole_event_period * 100)>40){
                    $icon = $period[3];
                }
                }else{
                   if ((abs($start->getTimestamp() - $eventStart->getTimestamp()) / $whole_event_period * 100)>20){
                    $icon = $period[3];
                }                 
                }

                
            }else{
                $class2 = $class;
            }

            if ($eventStart == $period[0]) {
                $work_period = abs($start->getTimestamp() - $end->getTimestamp());
                $work_percents = $work_period / $whole_event_period * 100;
                $bar .= "<div style='width: ".$work_percents."%; left: 0;' class='time_period ".$class2."'>".$icon."</div>";
            }
            else {
                $work_period = abs($start->getTimestamp() - $end->getTimestamp());
                $work_percents = $work_period / $whole_event_period * 100;
                $left_margin = abs($start->getTimestamp() - $eventStart->getTimestamp()) / $whole_event_period * 100;
                $bar .= "<div style='width: ".$work_percents."%; left: ".$left_margin."%; ' class='time_period ".$class2."'>".$icon."</div>";
            }
        }
        return $bar;
    }

    private function generateTimePeriod2($eventStart, $eventEnd, $periods) {
        $eventStart = new DateTime($eventStart);
        $eventEnd = new DateTime($eventEnd);
        $whole_event_period = abs($eventStart->getTimestamp() - $eventEnd->getTimestamp());
        $bar = '';
        foreach ($periods as $period) {
            if ($period['start'])
            {
                $start = new DateTime($period['start']);
                $end = new DateTime($period['end']);
                if ($start < $eventStart) {
                    $start = $eventStart;
                }
                if ($end > $eventEnd) {
                    $end = $eventEnd;
                }


                if ($eventStart == $period['start']) {
                    $work_period = abs($start->getTimestamp() - $end->getTimestamp());
                    $work_percents = $work_period / $whole_event_period * 100;
                    $bar .= "<div style='width: ".$work_percents."%; left: 0; text-align:center; font-size:9px; padding-top:2px; background-color:".$period['color']." border-right:1px solid white;' class='time_period'>".$period['label']."</div>";
                }
                else {
                    $work_period = abs($start->getTimestamp() - $end->getTimestamp());
                    $work_percents = $work_period / $whole_event_period * 100;
                    $left_margin = abs($start->getTimestamp() - $eventStart->getTimestamp()) / $whole_event_period * 100;
                    $bar .= "<div style='text-align:center; background-color:".$period['color']."; font-size:9px;  border-right:1px solid white; padding-top:2px; width: ".$work_percents."%; left: ".$left_margin."%;' class='time_period'>".$period['label']."</div>";
                }
            }
        }
        return $bar;
    }

    private function getEventVehicleBox(Event $event, $eventStart, $eventEnd) {

        $assigned_vehicles_html = '<ul class="sortable_vehicles event-flota-list " data-eventid="' . $event->id . '" data-eventstart="'.$eventStart.'" data-eventend="'.$eventEnd.'" >';
        $vehicles2 =$event->getAssignedVehiclesByModel();
            foreach ($vehicles2 as $veh_id=>$veh_array)
            {
                $added = 0;
                $quantity = 0;
                $work_content  ="";
                $periods = [];
                $vehs = [];
                /*
                $assigned_vehicles_html .= Html::beginTag('li',
                    [
                        'style' => 'background-color: #aaa; overflow: hidden; text-overflow: ellipsis;',
                    ]);
                $assigned_vehicles_html .= '<strong>'.$name.'</strong> '.$added."/".$total;
                $assigned_vehicles_html .= '</li>';*/
                    foreach ($veh_array as $key => $work)
                    {
                        $quantity+=$work['quantity'];
                        $added += $work['added'];
                        $name = $work['label'];
                        foreach ($work['vehicles'] as $veh)
                        {
                            $vehs[$veh->vehicle_id] = $veh;
                        }
                            $work_content .=$work['schedule']->prefix.": ".$work['added']."/".$work['quantity']." ";
                            $periods[$key]['label'] = $work['added']."/".$work['quantity'];
                            $periods[$key]['start'] = $work['schedule']->start_time;
                            $periods[$key]['end'] = $work['schedule']->end_time;
                            if ($work['added']>=$work['quantity'])
                                $periods[$key]['color'] = "#1ab394";
                            else
                                $periods[$key]['color'] = "#ed5565";
                    }
                    $assigned_vehicles_html.= "
                        <ul data-eventid='" . $event->id . "' data-vehicle='" . $veh_id. "' class='role-div' style='border:1px solid black; margin-bottom:2px;'>
                            <li style='background-color:#aaa; overflow: hidden;text-overflow: ellipsis;'><a href='/admin/vehicle/manage-ajax?id=".$event->id."&vehicle_id=".$veh_id."' class='assign-vehicles-button' style='color:black' data-eventid=".$event->id."><strong>" . $name . "</strong> " . $added . "/" . $quantity . " <i class='fa fa-plus'></i></a></li>
                        ";
  
                    $assigned_vehicles_html .= Html::beginTag('li',
                            [
                                'class' => 'fc_periods',
                                'style' =>'height:16px; position:relative;',
                                'data' => [
                                    'role' => $veh_id,
                                    'start' => $eventStart,
                                    'end' => $eventEnd,
                                    'eventstart' => $eventStart,
                                    'eventend' => $eventEnd,
                                    'eventid' => $event->id,
                                ]
                            ]);

                        $assigned_vehicles_html.= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                        $assigned_vehicles_html.="<div class='user-department-box'></div>";
                        $assigned_vehicles_html .= $this->generateTimeLine2($eventStart, $eventEnd, $periods);
                        $assigned_vehicles_html.= Html::endTag('div');
                        $assigned_vehicles_html .= Html::endTag('li');
                    $assigned_vehicles_html .= "<li><ul data-eventid='" . $event->id . "' data-vehicle='" . $veh_id . "'  class='fc_assigned_vehicles sortable_vehicles  event-vehicle-list ' data-eventstart='.$eventStart.' data-eventend='.$eventEnd.' > ";
                    foreach ($vehs as $_model_key => $value) {
                        $value = $value->vehicle;
                    $working_hours = [];
                    foreach (EventVehicleWorkingHours::find()->where(['event_id' => $event->id])->andWhere(['vehicle_id' =>  $_model_key])->all() as $workinTime) {
                        $working_hours[] = [$workinTime->start_time, $workinTime->end_time ];
                    }
                        $assigned_vehicles_html .= Html::beginTag('li',
                                [
                                    'class' => 'fc_vehicles',
                                    'data' => [
                                        'carid' => $value->id,
                                        'eventid' =>  $event->id,
                                        'eventstart' => $eventStart,
                                        'eventend' => $eventEnd,
                                        'workinghours' => $working_hours,
                                    ]
                                ]);
                            $assigned_vehicles_html .= Html::beginTag('div', ['class' => 'sortable_item_wrap']);
                            $assigned_vehicles_html .= $this->generateTimeLine($eventStart, $eventEnd, $working_hours);
                            $assigned_vehicles_html .= "<span class='user_span'>" . $value->name . ' (' . $value->registration_number . ')</span>';
                            $assigned_vehicles_html .= Html::endTag('div');
                            $assigned_vehicles_html .= '</li>';
                    }
                    $assigned_vehicles_html .= '</ul></li>';
                
                $assigned_vehicles_html .= '</ul>';
            }
        
        return $assigned_vehicles_html;
    }


    public function actionGetSkills() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = new PlanboardSearch();
        return $search->getSkills();
    }

    public function actionGetUserTypes() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = new PlanboardSearch();
        return $search->getUserTypes();
    }

    public function actionGetDepartments() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = new PlanboardSearch();
        return $search->getDepartments();
    }

}
