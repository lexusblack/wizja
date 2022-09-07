<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\models\EventLog;
use common\models\Event;
use common\models\EventBreaks;
use common\models\EventBreaksUser;
use common\models\EventUser;
use common\models\EventUserPlannedBreaks;
use common\models\EventUserPlannedWrokingTime;
use common\models\EventUserRole;
use common\models\User;
use common\models\Vacation;
use DateInterval;
use DateTime;
use Yii;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use common\models\UserSearch;
use yii\web\Response;

class CrewController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['assign-user'],
                    'roles' => ['eventsEventEditEyeCrewDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['manage', 'manage-ajax', 'change-working-hours', 'manage-working-hours', 'is-available', 'assign-user-to-role', 'assign-user-to-role2', 'is-working-in-close-range', 'assign-user-to-whole-event', 'update-working-time', 'add-event-role', 'copy-event-role', 'update-event-role', 'delete-event-role', 'conflict-calendar', 'change-dates', 'change-dates2'],
                    'roles' => ['eventsEventEditEyeCrewManage'],
                ]
            ]
        ];

        return $behaviors;
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

    public function actionChangeDates($user_id, $event_id)
    {
        $post = Yii::$app->request->post();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = str_replace("T"," ",$post['start']);
        $end = str_replace("T"," ",$post['end']);        
        if ($post['type']=='event')
        {
            //szukamy czy nie nachodzi na inne
            $hour = \common\models\EventUserPlannedWrokingTime::findOne($event_id);
            $user = \common\models\User::findOne($user_id);
            $canSave = $user->checkAvability($start, $end, $hour->event_id);
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

    public function actionConflictCalendar($user_id, $event_id, $schedule_id=null, $role_id=null)
    {
        $user = \common\models\User::findOne($user_id);
        $event = \common\models\Event::findOne($event_id);
        if ($schedule_id)
                $schedule = \common\models\EventSchedule::findOne($schedule_id);
            else
                $schedule = null;
        return $this->renderAjax('_conflictCalendar', ['user'=>$user, 'event'=>$event, 'schedule'=>$schedule, 'role_id'=>$role_id]);
    }

    public function actionDeleteEventRole($event_id, $schedule, $role_id)
    {
        $model = \common\models\EventOfferRole::findOne(['event_id'=>$event_id, 'user_role_id'=>$role_id, 'schedule'=>$schedule]);
        $model->delete();
        exit;
    }

    public function actionUpdateEventRole($event_id, $schedule, $role_id)
    {
        $model = \common\models\EventOfferRole::findOne(['event_id'=>$event_id, 'user_role_id'=>$role_id, 'schedule'=>$schedule]);
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add-event-role', [
            'model'=>$model,
        ]);
        }
    }

    public function actionCopyEventRole($id, $schedule, $prev)
    {
        if ($prev!="")
        {
            $roles = \common\models\EventOfferRole::find()->where(['event_id'=>$id, 'schedule'=>$prev])->all();
            foreach ($roles as $role)
            {
                $model = new \common\models\EventOfferRole();
                $model->event_id = $id;
                $model->schedule = $schedule;
                $model->quantity = $role->quantity;
                $model->user_role_id = $role->user_role_id;
                $model->save();
            }
        }
        
        exit;
    }

    public function actionAddEventRole($id, $schedule)
    {
        $model = new \common\models\EventOfferRole();
        $model->event_id = $id;
        $model->schedule = $schedule;
        $ids = \common\helpers\ArrayHelper::map(\common\models\EventOfferRole::find()->where(['event_id'=>$id, 'schedule'=>$schedule])->asArray()->all(), 'user_role_id', 'user_role_id');
        $roles = \common\helpers\ArrayHelper::map(\common\models\UserEventRole::find()->where(['active'=>1])->andWhere(['NOT IN', 'id', $ids])->asArray()->all(), 'id', 'name');
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('add-event-role', [
            'model'=>$model,
            'roles'=>$roles
        ]);
        }
    }

    public function actionUpdateWorkingTime()
    {
        $new = EventUserPlannedWrokingTime::findOne(Yii::$app->request->post("working_id"));
        $new->start_time = Yii::$app->request->post("start");
        $new->end_time = Yii::$app->request->post("end");
        $new->save();
    }
    public function actionAssignUserToRole2($id)
    {
        $user_id = Yii::$app->request->post("user_id");
        $schedule_id = Yii::$app->request->post("schedule_id");
        $role_id = Yii::$app->request->post("role_id");
        $add = Yii::$app->request->post("add");
        if ($add)
        {
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
            $schedule = \common\models\EventSchedule::findOne($schedule_id);
            $new = EventUserPlannedWrokingTime::find()->where(['user_id'=>$user_id])->andWhere(['event_id'=>$id])->andWhere(['event_schedule_id'=>$schedule_id])->one();
            if (!$new)
            {
                
                $new = new EventUserPlannedWrokingTime();
                $new->user_id = $user_id;
                $new->event_id = $id;
                $new->start_time = $schedule->start_time;
                $new->end_time = $schedule->end_time;
                $new->event_schedule_id = $schedule_id;
                $new->save();
            }
            $r = new EventUserRole();
            $r->event_user_id = $eventUSer->id;
            $r->user_event_role_id = $role_id;
            $r->working_hours_id = $new->id;
            $r->save();
            $return = ['success'=>1, 'message'=>$eventUSer->user->displayLabel.Yii::t('app', ' przypisany do etapu ').$schedule->name];
        }else{
            $time = EventUserPlannedWrokingTime::find()->where(['user_id'=>$user_id])->andWhere(['event_id'=>$id])->andWhere(['event_schedule_id'=>$schedule_id])->one();
            EventUserRole::deleteAll(['user_event_role_id'=>$role_id, 'working_hours_id'=>$time->id]);
            if (!EventUserRole::find()->where(['working_hours_id'=>$time->id])->count())
                $time->delete();
            $return = ['success'=>2, 'message'=>$time->user->displayLabel.Yii::t('app', ' usunięty z etapu')];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $return;
        exit;
    }

    public function actionManage($id, $from_date=null, $to_date=null, $role_id=null)
    {
        $model = Event::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException();
        }


        $assignedItems = $model->getUsers()->column();
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active' => 1]);
         $dataProvider->query->andWhere(['visible_in_offer' => 1]);
        $dataProvider->pagination = false;

        return $this->render('manage', [
            'model'=>$model,
            'assignedItems'=>$assignedItems,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'role_id'=>$role_id

        ]);
    }

    public function actionManageAjax($id, $from_date=null, $to_date=null, $role_id=null, $schedule=null)
    {
        $model = Event::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException();
        }


        $assignedItems = $model->getUsers()->column();
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }
        $params = Yii::$app->request->queryParams;
        if (isset($params['UserSearch']))
        {
            $params = Yii::$app->request->queryParams;
          Yii::$app->session['crewparams']=Yii::$app->request->queryParams;
          
        }else{
            $params = Yii::$app->session['crewparams'];
        }
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($params);
        $dataProvider->query->andWhere(['active' => 1]);
        $dataProvider->query->andWhere(['visible_in_offer' => 1]);
        $dataProvider->pagination = false;
        
        return $this->renderAjax('manage-ajax', [
            'model'=>$model,
            'assignedItems'=>$assignedItems,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'role_id'=>$role_id

        ]);
    }


    //todo: filter ajax
    public function actionAssignUser($id, $user_id = null, $role_id=null)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $params = Yii::$app->request->post();
        $params['itemId'] = ArrayHelper::getValue($_POST, 'itemId', ArrayHelper::getValue($_POST, 'itemid'), 0);
        $params['add'] = ArrayHelper::getValue($_POST, 'add', ArrayHelper::getValue($_POST, 'add'), 0);
        // dla usuwania pracownika GETem -> conflictuserworkinghours
        if ($user_id != null) {
            $params['itemId'] = $user_id;
        }

        $attributes = [
            'event_id'=>$id,
            'user_id'=>$params['itemId'],
        ];
        if (($params['add'] == 1 ) || ($params['add'] === true))
        {
           $eventUSer = new EventUser();
           $eventUSer->event_id = $id;
           $eventUSer->user_id = $params['itemId'];
           $eventUSer->type = 1;
           $eventUSer->create_time = date('Y-m-d H:i:s');
           $eventUSer->update_time = date('Y-m-d H:i:s');
           $eventUSer->save();
           $eventlog = new EventLog;
           $eventlog->event_id = $id;
           $eventlog->user_id = Yii::$app->user->identity->id;
           $eventlog->content = Yii::t('app', "Do eventu dodano pracownika").". (".$eventUSer->user->displayLabel.")";
           $eventlog->save();
        }
        else
        {
            EventUserPlannedBreaks::deleteAll(['user_id' => $params['itemId'], 'event_id' => $id]);
            EventUserPlannedWrokingTime::deleteAll(['user_id' => $params['itemId'], 'event_id' => $id]);
            $breaks = EventBreaks::findAll(['event_id'=>$id]);
            foreach ($breaks as $break) {
                EventBreaksUser::deleteAll(['event_break_id' => $break->id, 'user_id' => $params['itemId']]);
            }
            EventUserRole::removeAll($params['itemId'], $id);
            EventUser::remove($attributes);

            $eventlog = new EventLog;
            $eventlog->event_id = $id;
            $eventlog->user_id = Yii::$app->user->identity->id;
            $eventlog->content = Yii::t('app', "Z eventu usunięto pracownika").". (ID: ".$params['itemId'].")";
            $eventlog->save();
        }
        return $response;
    }

    public function actionAssignUserToWholeEvent($event_id, $user_id) {
        $event = Event::findOne($event_id);
        if (EventUser::find()->where(['event_id' => $event_id])->andWhere(['user_id' => $user_id])->count() == 0) {
            $eventUSer = new EventUser();
            $eventUSer->event_id = $event_id;
            $eventUSer->user_id = $user_id;
            $eventUSer->type = 2;
            $eventUSer->create_time = date('Y-m-d H:i:s');
            $eventUSer->update_time = date('Y-m-d H:i:s');
            $eventUSer->save();
           $eventlog = new EventLog;
           $eventlog->event_id = $event_id;
           $eventlog->user_id = Yii::$app->user->identity->id;
           $eventlog->content = Yii::t('app', "Do eventu dodano pracownika").". (".$eventUSer->user->displayLabel.")";
           $eventlog->save();
        }
        $this->createUserEventWorkingTime($event_id, $user_id, $event->montage_start, $event->montage_end);
        $this->createUserEventWorkingTime($event_id, $user_id, $event->event_start, $event->event_end);
        $this->createUserEventWorkingTime($event_id, $user_id, $event->disassembly_start, $event->disassembly_end);
    }

    private function createUserEventWorkingTime($event_id, $user_id, $start, $end) {
        if ($start == null || $end == null) {
            return;
        }
        if (EventUserPlannedWrokingTime::find()->where(['event_id' => $event_id])->andWhere(['user_id' => $user_id])->andWhere(['start_time' => $start])->andWhere(['end_time' => $end])->count()==0) {
            $new = new EventUserPlannedWrokingTime();
            $new->user_id = $user_id;
            $new->event_id = $event_id;
            $new->start_time = $start;
            $new->end_time = $end;
            $new->save();
        }
    }

    public function actionIsAvailable($user_id, $start, $end) {
        return User::findOne(['id' => $user_id])->isAvailableInRange($start, $end);
    }

    public function actionIsWorkingInCloseRange($user_id, $start, $end, $event_id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $isWorking = 0;
        $vacation = 0;
        $planned_vacation = 0;
        $start = new DateTime($start);
        $end = new DateTime($end);

        $planned_working_time = EventUserPlannedWrokingTime::findAll(['user_id' => $user_id]);
        if ($event_id != null) {
            $planned_working_time = EventUserPlannedWrokingTime::find()->where(['user_id' => $user_id])->andWhere(['<>', 'event_id', $event_id])->all();
        }

        foreach ($planned_working_time as $time) {
            $work_start = new DateTime($time->start_time);
            $work_end = new DateTime($time->end_time);

            $test1 = clone($work_start)->add(new DateInterval('PT12H')) ;
            $test2 = clone($work_start)->sub(new DateInterval('PT24H'));
            $test3 = clone($work_end)->sub(new DateInterval('PT12H'));
            $test4 = clone($work_end)->add(new DateInterval('PT24H'));

            if ($test1 <= $start && $test2 >= $start) {
                $isWorking = 1;
            }
            if ($test3 <= $start && $test4 >= $start) {
                $isWorking = 1;
            }
            if ($test1 <= $end && $test2 >= $end) {
                $isWorking = 1;
            }
            if ($test3 <= $end && $test4 >= $end) {
                $isWorking = 1;
            }
        }
        foreach (Vacation::findAll(['user_id' => $user_id]) as $time) {
            $vacation_start = new DateTime($time->start_date . " 00:00");
            $vacation_end = new DateTime($time->end_date . " 23:59");

            if ($vacation_start <= $start && $end <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation = 1;
                }
                if ($time->status == 0) {
                    $planned_vacation = 1;
                }
            }
            if ($vacation_start >= $start && $end >= $vacation_end) {
                if ($time->status == 10) {
                    $vacation = 1;
                }
                if ($time->status == 0) {
                    $planned_vacation = 1;
                }
            }
            if ($start >= $vacation_start && $start <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation = 1;
                }
                if ($time->status == 0) {
                    $planned_vacation = 1;
                }
            }
            if ($end >= $vacation_start && $end <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation = 1;
                }
                if ($time->status == 0) {
                    $planned_vacation = 1;
                }
            }

        }

        return [$isWorking, $vacation, $planned_vacation];
    }

    public static function getUserVacationsInPeriod($user_id, DateTime $start, DateTime $end) {
        $planned_vacation = [];
        $vacation = [];
        foreach (Vacation::findAll(['user_id' => $user_id]) as $time) {
            $vacation_start = new DateTime($time->start_date . " 00:00");
            $vacation_end = new DateTime($time->end_date . " 23:59");

            if ($vacation_start <= $start && $end <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation[] = $time;
                }
                if ($time->status == 0) {
                    $planned_vacation[] = $time;
                }
            }
            if ($vacation_start >= $start && $end >= $vacation_end) {
                if ($time->status == 10) {
                    $vacation[] = $time;
                }
                if ($time->status == 0) {
                    $planned_vacation[] = $time;
                }
            }
            if ($start >= $vacation_start && $start <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation[] = $time;
                }
                if ($time->status == 0) {
                    $planned_vacation[] = $time;
                }
            }
            if ($end >= $vacation_start && $end <= $vacation_end) {
                if ($time->status == 10) {
                    $vacation[] = $time;
                }
                if ($time->status == 0) {
                    $planned_vacation[] = $time;
                }
            }
        }
        return [$vacation, $planned_vacation];
    }

    public function actionAssignUserToRole($user_id, $event_id, $role_id, $add) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $eventUser = EventUser::find()->where(['event_id' => $event_id])->andWhere(['user_id' => $user_id])->one();
        if ($add) {
            if (count($eventUser) == 0) {
                $event = Event::findOne(['id' => $event_id]);
                $eventUser = new EventUser();
                $eventUser->event_id = $event_id;
                $eventUser->user_id = $user_id;
                $eventUser->type = 1;
                $eventUser->create_time = date("Y-m-d H:i:s");
                $eventUser->update_time = date("Y-m-d H:i:s");
                $eventUser->start_time = $event->getTimeStart();
                $eventUser->end_time = $event->getTimeEnd();
                if (!$eventUser->save()) {
                    return $eventUser->getErrors();
                }
            }
            /*
            if ($role_id != 0) {
                $eventUserRole = new EventUserRole();
                $eventUserRole->event_user_id = $eventUser->id;
                $eventUserRole->user_event_role_id = $role_id;
                $eventUserRole->create_time = date("Y-m-d H:i:s");
                $eventUserRole->update_time = date("Y-m-d H:i:s");
                return $eventUserRole->save();
            }
            */
            return 1;
        }
        else {
            if (count($eventUser) == 1) {
                $result = EventUserRole::find()->where(['event_user_id'=>$eventUser->id])->andWhere(['user_event_role_id'=>$role_id])->one()->delete();
                if (EventUserRole::find()->where(['event_user_id'=>$eventUser->id])->count() == 0) {
                    return $eventUser->delete();
                }
                return $result;
            }
        }
        return true;
    }

    public function actionChangeWorkingHours($id) {
        $model = EventUserPlannedWrokingTime::findOne($id);

        $start_time = new DateTime($_POST['start']);
        $end_time = new DateTime($_POST['end']);

        $model->start_time = $start_time->format("Y-m-d H:i:s");
        $model->end_time = $end_time->format("Y-m-d H:i:s");
        $model->save();
    }

    public function actionManageWorkingHours() {
        if($_POST['add'] === "true") {
            $model = new EventUserPlannedWrokingTime();
            $model->start_time = $_POST['start'];
            $model->end_time = $_POST['end'];
            $model->user_id = $_POST['user_id'];
            $model->event_id = $_POST['event_id'];
            $model->save();
        }
        else if ($_POST['add'] === 'false') {
            $model = EventUserPlannedWrokingTime::find()->where(['user_id' => $_POST['user_id']])->andWhere(['event_id' => $_POST['event_id']])->andWhere(['start_time' => $_POST['start']])->andWhere(['end_time' => $_POST['end']])->one();
            if ($model) {
                $model->delete();
            }
        }
    }
}
