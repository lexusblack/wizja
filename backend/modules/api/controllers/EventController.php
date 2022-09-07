<?php


namespace backend\modules\api\controllers;


use common\models\Event;
use common\models\Meeting;
use common\models\Rent;
use common\models\Attachment;
use common\models\EventUser;
use common\models\EventUserAddon;
use common\models\EventUserAllowance;
use common\models\EventUserWorkingTime;
use common\models\EventLog;
use common\models\SettlementUser;
use Yii;
use DateTime;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use backend\modules\permission\models\BasePermission;
use yii\helpers\ArrayHelper;

class EventController extends BaseController {
	public $modelClass = 'common\models\Event';

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }

    public function addEvent()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $event = new Event();
            $event->name = Yii::$app->request->post("name");
            $event->type = Yii::$app->request->post("type");
            $event->customer_id = Yii::$app->request->post("customer_id");
            $event->contact_id = Yii::$app->request->post("contact_id");
            $event->location_id = Yii::$app->request->post("location_id");
            $event->address = Yii::$app->request->post("address");
            $event->manager_id = Yii::$app->request->post("manager_id");
            $event->departmentIds = Yii::$app->request->post("departmentIds");
            $schema = \common\models\TasksSchema::find()->where(['type'=>2])->andWhere(['default'=>1])->one();
            if ($schema)
                $event->tasks_schema_id = $schema->id;

            
            if (!$event->save()){
                    throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }else{
                $event->linkObjects();
                return [
                            'name' => Yii::t('app', 'Wydarzenie'),
                            'message' => Yii::t('app', 'Dodano'),
                            'code' => 0,
                            'status' => 200,
                            'id'=>$event->id
                        ];
            }
            }
        throw new MethodNotAllowedHttpException();
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new Attachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
              /*  $eventlog = new EventLog;
                $eventlog->event_id =  $model->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Do eventu dodano załącznik").": ".$model->filename.".";
                $eventlog->save();           
                */
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }

	public function actionToday()
	{
        $user = Yii::$app->user;
        $x = 7; //number of days in the past
        $y = 300; //number of days in the future
		$past_stamp = time() - $x*24*60*60;
		$next_stamp = time() + $y*24*60*60;
		$start = date('Y-m-d', $past_stamp);
        $end = date('Y-m-d', $next_stamp);
        if (!$user->can('SiteAdministrator') && $user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Event::find()
                ->select('event.*')
                ->leftJoin('event_user', 'event_user.event_id = event.id')
                ->where(['or', ['event_user.user_id'=>Yii::$app->user->id], ['manager_id' => Yii::$app->user->id]])->andWhere(['<', 'event_start', $end])->andWhere(['>', 'event_end', $start]);
        }else{
            $query = Event::find()
            ->where(['<', 'event_start', $end])->andWhere(['>', 'event_end', $start]);
        }
        $events = $query->all();
        $eArray = [];
        foreach ($events as $e)
        {
            $e->event_start = $e->getTimeStart();
            $e->event_end = $e->getTimeEnd();
            $eArray[] = $e->toArray();
        }
        if (!$user->can('SiteAdministrator') && $user->can('eventRents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Rent::find()->where(['or', 'created_by' => Yii::$app->user->id, 'manager_id'=>Yii::$app->user->id])->andWhere(['>', 'end_time', $start])->andWhere(['<', 'start_time', $end]);
        }else{
            $query = Rent::find()->where(['>', 'end_time', $start])->andWhere(['<', 'start_time', $end]);
        }
        $rents = $query->all();
        $rArray = [];
        foreach ($rents as $e)
        {
            $rArray[] = $e->toArray();
        }
        if (!$user->can('SiteAdministrator') && $user->can('eventMeetings'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = $query = Meeting::find()->joinWith('users')->where(['or', 'created_by' => Yii::$app->user->id, 'user.id' => Yii::$app->user->id])->andWhere(['>', 'end_time', $start])->andWhere(['<', 'start_time', $end]);
        }else{
            $query = Meeting::find()->where(['>', 'end_time', $start])->andWhere(['<', 'start_time', $end]);
        }
        $meetings = $query->all();
        $mArray = [];
        foreach ($meetings as $e)
        {
            $mArray[] = $e->toArray();
        }
        return ['events'=>$eArray, 'rents'=>$rArray, 'meetings'=>$mArray];
	}

    public function actionGet($id)
    {
        $user = Yii::$app->user;
            $event = Event::findOne($id);
            if ($event)
            {
                    $tmpEvent = $event->toArray();
                    $tmpEvent['task_status'] = $event->getTaskStatus();
                    $tmpEvent['vehicles'] = [];
                    $tmpEvent['users'] = [];
                    $tmpEvent['attachments'] = [];
                    foreach ($event->eventVehicles as $v)
                    {
                        $tmpVehicle = $v->vehicle->toArray();
                        $tmpVehicle['start'] = $v->start_time;
                        $tmpVehicle['end'] = $v->end_time;
                        $tmpEvent['vehicles'][] = $tmpVehicle;
                    }
                    foreach ($event->attachments as $a)
                    {
                        $tmpAtt = $a->toArray();
                        $tmpAtt['url'] = $a->getFileUrl();
                        $tmpEvent['attachments'][] = $tmpAtt;
                    }
                    $tmpEvent['users'] =$event->getAssignedUsersByTimeArray();
                    $tmpEvent['gears'] = $event->getAssignedGearsArray();
                    $tmpEvent['outerGears'] = $event->getAssignedOuterGearsArray();
                    $tmpEvent['working_hours'] = $event->getCurrentUserWorkArray();
                    $tmpEvent['schedules'] = $event->eventSchedules;
                    if (!$tmpEvent['location'])
                    {
                        $tmpEvent['location'] = ['id'=>0, 'name'=>$tmpEvent['address'], 'address'=>$tmpEvent['address'], 'zip'=>"", 'city'=>"", 'country'=>""];
                    }
                    return ['event'=>$tmpEvent];
            }else{
                throw new BadRequestHttpException(Yii::t('app', 'Brak wydarzenia'));
            }
            
        throw new BadRequestHttpException(Yii::t('app', 'Something wrong'));
    }

    public function actionAddWorkingTime()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $event_id = Yii::$app->request->post("event_id");
            $role_id = Yii::$app->request->post('role_id');
            $department_id = Yii::$app->request->post('department_id');
            $start = Yii::$app->request->post('start');
            $end = Yii::$app->request->post('end');
            $time_type = Yii::$app->request->post('time_type');

            $workingTime = new EventUserWorkingTime([
            'user_id'=>$user->id,
            'event_id'=>$event_id,
            ]);
            $workingTime->loadLinkedObjects();
            $workingTime->role_id = $role_id;
            $workingTime->department_id = $department_id;
            $workingTime->start_time = $start;
            $workingTime->end_time = $end;
            $workingTime->type = $time_type;

                if ($workingTime->save())
                {
                    $eventlog = new EventLog;
                    $eventlog->event_id = $event_id;
                    $eventlog->user_id = Yii::$app->user->identity->id;
                    $eventlog->content = Yii::t('app', "Do eventu dodano godziny pracy.");
                    $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Godziny pracy'),
                            'message' => Yii::t('app', 'Dodano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        throw new MethodNotAllowedHttpException();
    }

    public function actionEditWorkingTime($id)
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $role_id = Yii::$app->request->post('role_id');
            $department_id = Yii::$app->request->post('department_id');
            $start = Yii::$app->request->post('start');
            $end = Yii::$app->request->post('end');
            $time_type = Yii::$app->request->post('time_type');

            $workingTime = EventUserWorkingTime::findOne($id);
            $workingTime->loadLinkedObjects();
            $workingTime->role_id = $role_id;
            $workingTime->department_id = $department_id;
            $workingTime->start_time = $start;
            $workingTime->end_time = $end;
            $workingTime->type = $time_type;

                if ($workingTime->save())
                {
                    $eventlog = new EventLog;
                    $eventlog->event_id = $event_id;
                    $eventlog->user_id = Yii::$app->user->identity->id;
                    $eventlog->content = Yii::t('app', "Zmieniono godziny pracy.");
                    $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Godziny pracy'),
                            'message' => Yii::t('app', 'Zmieniono'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        throw new MethodNotAllowedHttpException();        
    }

    public function actionDeleteWorkingTime($id)
    {
        $workingTime = EventUserWorkingTime::findOne($id);
        $workingTime->delete();
                          return [
                            'name' => Yii::t('app', 'Godziny pracy'),
                            'message' => Yii::t('app', 'Usunięto'),
                            'code' => 0,
                            'status' => 200
                        ];      
    }

    public function actionGetWorkingTime($event_id=false, $year=false, $month=false)
    {
        $user = Yii::$app->user;
        if ($event_id){
            $times = EventUserWorkingTime::find()->where(['user_id'=>$user->id])->andWhere(['event_id'=>$event_id])->all();
            $costs = EventUserAddon::find()->where(['user_id'=>$user->id])->andWhere(['event_id'=>$event_id])->all();
            $allowance = EventUserAllowance::find()->where(['user_id'=>$user->id])->andWhere(['event_id'=>$event_id])->all();
        }else{
            $date = $year.'-'.$month.'-01';
            $time = strtotime($date);
            $date2 = date("Y-m-d", strtotime("+1 month", $time));
            $times = EventUserWorkingTime::find()->where(['user_id'=>$user->id])->andWhere(['>', 'start_time', $date])->andWhere(['<', 'start_time', $date2])->all();
            $costs = EventUserAddon::find()->where(['user_id'=>$user->id])->andWhere(['>', 'start_time', $date])->andWhere(['<', 'start_time', $date2])->all();
            $allowance = EventUserAllowance::find()->where(['user_id'=>$user->id])->andWhere(['>', 'start_time', $date])->andWhere(['<', 'start_time', $date2])->all();
        }
        $timeArray = [];
        $costArray = [];
        $allowanceArray = [];
        foreach($times as $t)
        {
            $tmp['id'] = $t->id;
            $tmp['start_time'] = $t->start_time;
            $tmp['end_time'] = $t->end_time;
            $tmp['department_id'] = $t->department_id;
            $tmp['department'] = $t->department->name;
            $tmp['role_id'] = $t->role_id;
            $tmp['role'] = $t->role->name;
            $tmp['type'] = $t->type;
            $tmp['event_id'] = $t->event_id;
            $tmp['event'] = $t->event->name;
            $timeArray[] = $tmp;

        }
        foreach($costs as $t)
        {
            $tmp['id'] = $t->id;
            $tmp['start_time'] = $t->start_time;
            $tmp['end_time'] = $t->end_time;
            $tmp['name'] = $t->name;
            $tmp['info'] = $t->info;
            $tmp['amount'] = $t->amount;
            $tmp['event_id'] = $t->event_id;
            $tmp['event'] = $t->event->name;
            $costArray[] = $tmp;

        }
        foreach($allowance as $t)
        {
            $tmp['id'] = $t->id;
            $tmp['start_time'] = $t->start_time;
            $tmp['end_time'] = $t->end_time;
            $tmp['type'] = $t->type;
            $tmp['amount'] = $t->amount;
            $tmp['event_id'] = $t->event_id;
            $tmp['event'] = $t->event->name;
            $allowanceArray[] = $tmp;

        }
        return ['working_times'=>$timeArray, 'costs'=>$costArray, 'allowance'=>$allowanceArray];
    }

    public function actionMonthSettled($year=false, $month=false)
    {
        $user = Yii::$app->user;
        SettlementUser::setSettled($user->id, $year, $month, SettlementUser::STATUS_SETTLED);
                        return [
                            'name' => Yii::t('app', 'Miesiąc pracy'),
                            'message' => Yii::t('app', 'Rozliczony'),
                            'code' => 0,
                            'status' => 200
                        ];
    }

    public function actionWorkingEvents()
    {
        $user = Yii::$app->user;
        $x = 70; //number of days in the past
        $past_stamp = time() - $x*24*60*60;
        $next_stamp = time() + 24*60*60;
        $start = date('Y-m-d', $past_stamp);
        $end = date('Y-m-d', $next_stamp);
        $ids = ArrayHelper::map(Event::find()->where(['>', 'event_start', $start])->andWhere(['<', 'event_start', $end])->asArray()->all(), 'id', 'id');
        $events = EventUser::find()->where(['user_id'=>$user->id])->andWhere(['event_id'=>$ids])->all();
        $return = ['events'=>[]];
        foreach ($events as $event)
        {
            $event = $event->event;
            $return['events'][]= ['id'=>$event->id, 'name'=>$event->name, 'start'=>$event->getTimeStart(), 'end'=>$event->getTimeEnd()];
        }
        return $return;
    }

    public function actionAddCost()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost){
            $event_id = Yii::$app->request->post("event_id");
            $start = Yii::$app->request->post('start');
            $end = Yii::$app->request->post('end');
            $cost = Yii::$app->request->post('cost');
            $name = Yii::$app->request->post('name');
            $description = Yii::$app->request->post('description');
            $model = new EventUserAddon(['event_id'=>$event_id, 'user_id'=>$user->id, 'name'=>$name, 'amount'=>$cost, 'info'=>$description, 'start_time'=>$start, 'end_time'=>$end]);
                if ($model->save())
                {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $event_id;
                        $eventlog->user_id = $user->id;
                        $eventlog->content = Yii::t('app', "Do eventu dodano koszt")." ".$model->name.".";
                        $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Dodatkowy koszt'),
                            'message' => Yii::t('app', 'Dodano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        throw new MethodNotAllowedHttpException();
    }

    public function actionEditCost()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $model = EventUserAddon::findOne(Yii::$app->request->post("id"));
            if ($model)
            {
                $model->start_time = Yii::$app->request->post('start');
                $model->end_time = Yii::$app->request->post('end');
                $model->amount = Yii::$app->request->post('cost');
                $model->name = Yii::$app->request->post('name');
                $model->info = Yii::$app->request->post('description');
                if ($model->save())
                {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $model->event_id;
                        $eventlog->user_id = $user->id;
                        $eventlog->content = Yii::t('app', "Zedytowno koszt")." ".$model->name.".";
                        $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Dodatkowy koszt'),
                            'message' => Yii::t('app', 'Zedytowano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));                
            }else{
                throw new NotFoundHttpException(Yii::t('app', 'Brak kosztu'));
            }

            }
        throw new MethodNotAllowedHttpException();
    }

    public function actionDeleteCost()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $model = EventUserAddon::findOne(Yii::$app->request->post("id"));
            if ($model)
            {
                if ($model->delete())
                {
                        return [
                            'name' => Yii::t('app', 'Dodatkowy koszt'),
                            'message' => Yii::t('app', 'Usunięto'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));                
            }else{
                throw new NotFoundHttpException(Yii::t('app', 'Brak kosztu'));
            }

            }
        throw new MethodNotAllowedHttpException();
    } 

    public function actionAddAllowance()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $event_id = Yii::$app->request->post("event_id");
            $start = Yii::$app->request->post('start');
            $end = Yii::$app->request->post('end');
            $cost = Yii::$app->request->post('cost');
            $type = Yii::$app->request->post('type');
            $model = new EventUserAllowance(['event_id'=>$event_id, 'user_id'=>$user->id, 'amount'=>$cost, 'type'=>$type, 'start_time'=>$start, 'end_time'=>$end]);
                if ($model->save())
                {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $event_id;
                        $eventlog->user_id = $user->id;
                        $eventlog->content = Yii::t('app', "Do eventu dodano dietę").".";
                        $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Dieta'),
                            'message' => Yii::t('app', 'Dodano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        throw new MethodNotAllowedHttpException();
    }

    public function actionEditAllowance()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $model = EventUserAllowance::findOne(Yii::$app->request->post("id"));
            if ($model)
            {
                $model->start_time = Yii::$app->request->post('start');
                $model->end_time = Yii::$app->request->post('end');
                $model->amount = Yii::$app->request->post('cost');
                $model->type = Yii::$app->request->post('type');
                if ($model->save())
                {
                        $eventlog = new EventLog;
                        $eventlog->event_id = $model->event_id;
                        $eventlog->user_id = $user->id;
                        $eventlog->content = Yii::t('app', "Zedytowno dietę").".";
                        $eventlog->save();
                        return [
                            'name' => Yii::t('app', 'Dieta'),
                            'message' => Yii::t('app', 'Zedytowano'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));                
            }else{
                throw new NotFoundHttpException(Yii::t('app', 'Brak kosztu'));
            }

            }
        throw new MethodNotAllowedHttpException();
    }

    public function actionDeleteAllowance()
    {
        $user = Yii::$app->user;
        if (Yii::$app->request->isPost) {
            $model = EventUserAllowance::findOne(Yii::$app->request->post("id"));
            if ($model)
            {
                if ($model->delete())
                {
                        return [
                            'name' => Yii::t('app', 'Dieta'),
                            'message' => Yii::t('app', 'Usunięto'),
                            'code' => 0,
                            'status' => 200
                        ];
                }
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));                
            }else{
                throw new NotFoundHttpException(Yii::t('app', 'Brak kosztu'));
            }

            }
        throw new MethodNotAllowedHttpException();
    } 
}