<?php

namespace common\models;

use Yii;
use \common\models\base\Task as BaseTask;
use common\helpers\ArrayHelper;
use common\components\Sms;
/**
 * This is the model class for table "task".
 */
class Task extends BaseTask
{
    public $roleIds;
    public $userIds;
    public $notificationRoleIds;
    public $notificationUserIds; 
    public $deadlineDateRange;
    public $teamIds;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['title'], 'required'],
            [['content'], 'string'],
            [['datetime', 'create_time', 'update_time'], 'safe'],
            [['order', 'creator_id', 'type', 'status', 'event_id', 'task_category_id', 'cyclic_type', 'only_one', 'people', 'for_event'], 'integer'],
            [['hours'], 'number'],
            [['title', 'comment'], 'string', 'max' => 255],
            [['roleIds', 'userIds', 'notificationRoleIds', 'notificationUserIds', 'teamIds'], 'each', 'rule' => ['integer']],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();


        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'roleIds',
                'userIds',
                'notificationRoleIds',
                'notificationUserIds'
            ],
            'relations' => [
                'roles',
                'users',
                'notificationRoles',
                'notificationUsers',               
            ],
            'modelClasses' => [
                'common\models\UserEventRole',
                'common\models\User',
                'common\models\UserEventRole',
                'common\models\User',
            ],
        ];
        return $behaviors;
    }
	    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('app', 'Nazwa'),
            'content' => Yii::t('app', 'Treść'),
            'datetime' => Yii::t('app', 'Deadline'),
            'order' => 'Order',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'creator_id' => 'Creator ID',
            'type' => 'Type',
            'status' => 'Status',
            'event_id' => 'Event',
                        'userIds' => Yii::t('app', 'Użytkownicy przypisani do zadania'),
                        'teamIds' => Yii::t('app', 'Zespoły przypisane do zadania'),
            'roleIds' => Yii::t('app', 'Role przypisane do zadania'),
            'notificationUserIds' => Yii::t('app', 'Potwierdzenie o wykonaniu do (użytkownicy)'),
            'notificationRoleIds' => Yii::t('app', 'Potwierdzenie o wykonaniu do (role)'),
            'cycli_type' => Yii::t('app', 'Powtarzać zadanie?'),
            'department_id'=>Yii::t('app', 'Powiązany dział'),
            'task_id'=>Yii::t('app', 'Zadanie nadrzędne'),
            'hours'=>Yii::t('app', 'Szacowany czas wykonania zadania [h]'),
            'people'=>Yii::t('app', 'Szacowana potrzeba liczba osób'),
            'for_event'=>Yii::t('app', 'Na potrzeby wydarzenia'),
        ];
    }

    public function checkStatus()
    {
        $users = $this->getAllUsers();
        $done = 10;
        foreach ($users as $user)
        {
            $d = TaskDone::find()->where(['task_id'=>$this->id])->andWhere(['user_id'=>$user->id])->one();
            if (!$d)
                $done = 0;
        }
        if (!$users)
        {
            $done = 0;
        }
        return $done;
    }

    public function createChangeUsersNote($oldUsers)
    {
        $old_ids = [];
        foreach($oldUsers as $old)
        {
            $old_ids[] = $old->id;
        }
        $users_string = "";
        foreach ($this->users as $user)
        {
            if (!in_array($user->id, $old_ids))
            {
                $users_string .= $user->displayLabel." ";
                $this->sendCreateNotification($user);
            }
        }
        if ($users_string!="")
        {
            $note = new TaskNote();
            $note->task_id = $this->id;
            $note->user_id = Yii::$app->user->id;
            $note->text = Yii::t('app', 'Do zadania przypisani zostali użytkownicy: ').$users_string;
            $note->save();
        }
    }

    public function sendDoneNotifications($user_id)
    {
        $user = User::findOne($user_id);
        $nUsers = $this->getAllNotificationUsers();
        foreach ($nUsers as $nUser)
        {
            if ($user->id!=$nUser->id)
            {
                $this->sendMailNotification($user, $nUser);
                $this->sendSMSNotification($user, $nUser);               
            }

        }
        return true;
    }

    public function sendDoneNotificationsUser($user_id)
    {
        $user = User::findOne($user_id);
        $nUsers = $this->getAllNotificationUsers();
        foreach ($nUsers as $nUser)
        {
            if ($user->id!=$nUser->id)
            {
                $this->sendMailNotification($user, $nUser);
                $this->sendSMSNotification($user, $nUser);               
            }
        }
        return true;
    }

     public function sendCreateNotifications()
     {
        $users = $this->getAllUsers();
        foreach ($users as $user)
        {
            $this->sendCreateNotification($user);
        }
     }

    public function sendCreateNotification($recipient)
    {
        $params = Yii::$app->params;
        $subject = '[New Event Management] '.Yii::t('app', 'Dodano zadanie: ').$this->title;
        if ($this->event_id)
            $subject.= " ".$this->event->displayLabel;
        if ($this->rent_id)
            $subject.= " ".$this->rent->displayLabel;
        $sent = \Yii::$app->mailer->compose('@app/views/task/notification-create-mail', [
                'model' =>  $this, 'user'=>$recipient, 'subject'=>$subject])
                ->setTo($recipient->email)
                ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
                ->setSubject($subject)
                ->send();
            Yii::info([$sent, Yii::$app->params], 'Mailer');
            return $sent;                   
    }     

    public function sendMailNotification($user, $recipient)
    {
        $params = Yii::$app->params;
        $subject = '[New Event Management] '.Yii::t('app', 'Wykonano ').$this->title;
        if ($this->event_id)
            $subject.= " ".$this->event->displayLabel;
        $sent = \Yii::$app->mailer->compose('@app/views/task/notification-mail', [
                'model' =>  $this, 'user'=>$user, 'subject'=>$subject])
                ->setTo($recipient->email)
                ->setFrom([Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),])
                ->setSubject($subject)
                ->send();
            Yii::info([$sent, Yii::$app->params], 'Mailer');
            return $sent;                   
    }

    public function sendSMSNotification($user, $recipient)
    {
        $smsEnabled = Yii::$app->params['smsEnabled'];
        $response = null;
        $subject = '[New Event Management] '.$user->displayLabel." ".Yii::t('app', 'wykonał/-a ').$this->title;
        if ($this->event_id)
            $subject.= " ".$this->event->displayLabel;
        if ($this->only_one!=1){ 
                            $users = 0;
                            $done = 0;
                            foreach ($this->getAllUsers() as $team){ 
                                $status = $this->checkStatusForUser($team->id);
                                if ($status){
                                    $done++;
                                }
                                $users++;
                            }
                            if ($users)
                                $status = intval($done/$users*100);
                            else
                                $status = 0;

                            $subject .=" ".Yii::t('app', 'Aktualny status zadania: ').$status."% (".$done."/".$users.")";}
        if (empty($user->phone)) {
            Yii::info(Yii::t('app', 'Brak numeru telefonu').' '.$recipient->username, 'sms.info');
            return false;
        }
        $model = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $smsEnabled = $model->sms;
        if ($model->sms_sender)
        {
            $sender = $model->sms_sender;
        }else{
            $sender = null;
        }

        try {
                $date = null;
            $response = Sms::load()->messages->sendSms(
                $recipient->phone,
                strip_tags($subject),
                Sms::getSender(),
                [
                    'test' => $smsEnabled ? false : true,
                    'details'=>true,
                    'date' => $date,
                    'sender'=>$sender
                ]);
        }
        catch (\Exception $e) {
            $response = $recipientr->username.'; '.$recipient->phone.'; '.$subject.'; '.$e->getMessage();
            Yii::error($response, 'sms.error');
        }
        Yii::info(ArrayHelper::toArray($response), 'sms.response');
        if (isset($response->success) && $response->success) {
        }
        else {
            //throw new \Exception(Yii::t('app', 'Nie udało się wysłać SMSa'));
        } 
    }

    public function getAllUsers()
    {
        $userIds = ArrayHelper::map(UserTask::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_id', 'user_id');
        $userIds2 = [];
        $roleIds = ArrayHelper::map(TaskRole::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_event_role_id', 'user_event_role_id');
        if ($roleIds)
        {
            $euserIds = ArrayHelper::map(EventUserRole::find()->where(['IN', 'user_event_role_id', $roleIds])->asArray()->all(), 'event_user_id', 'event_user_id');
            if ($euserIds)
                $userIds2 = ArrayHelper::map(EventUser::find()->where(['IN', 'id', $euserIds])->andWhere(['event_id'=>$this->event_id])->asArray()->all(), 'user_id', 'user_id');
        }
        
        $userIds = array_merge($userIds, $userIds2);
        $users = User::find()->where(['IN', 'id', $userIds])->all();
        return $users;
    }

    public function getAllNotificationUsers()
    {
        $userIds = ArrayHelper::map(TaskNotificationUser::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_id', 'user_id');
        $userIds2 = [];
        $roleIds = ArrayHelper::map(TaskNotificationRole::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_event_role_id', 'user_event_role_id');
        if ($roleIds)
        {
            $euserIds = ArrayHelper::map(EventUserRole::find()->where(['IN', 'user_event_role_id', $roleIds])->asArray()->all(), 'event_user_id', 'event_user_id');
            if ($euserIds)
                $userIds2 = ArrayHelper::map(EventUser::find()->where(['IN', 'id', $euserIds])->andWhere(['event_id'=>$this->event_id])->asArray()->all(), 'user_id', 'user_id');
        }
        
        $userIds = array_merge($userIds, $userIds2);
        $users = User::find()->where(['IN', 'id', $userIds])->all();
        return $users;
    }

    public function isMine($user_id)
    {
        $userIds = ArrayHelper::map(UserTask::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_id', 'user_id');
        $userIds2 = [];
        $roleIds = ArrayHelper::map(TaskRole::find()->where(['task_id'=>$this->id])->asArray()->all(), 'user_event_role_id', 'user_event_role_id');
        if ($roleIds)
        {
            $euserIds = ArrayHelper::map(EventUserRole::find()->where(['IN', 'user_event_role_id', $roleIds])->asArray()->all(), 'event_user_id', 'event_user_id');
            if ($euserIds)
                $userIds2 = ArrayHelper::map(EventUser::find()->where(['IN', 'id', $euserIds])->andWhere(['event_id'=>$this->event_id])->asArray()->all(), 'user_id', 'user_id');
        }

        $userIds = array_merge($userIds, $userIds2); 
        if (!$userIds)
        {
            return true;
        }
        if (in_array($user_id, $userIds))
        {
            return true;
        }else{
            return false;
        }
    }



    public function checkStatusForUser($user_id)
    {
        $d = TaskDone::find()->where(['task_id'=>$this->id])->andWhere(['user_id'=>$user_id])->one();
            if (!$d)
                return false;
            else 
                return true;
        
    }

    public static function getStatusFilter()
    {
        return [1=>Yii::t('app', 'Niewykonane'), 2=>Yii::t('app', 'Po terminie'), 3=>Yii::t('app', 'Wykonane')];
    }

    public static function getCyclicTypes()
    {
        return[
        0=>Yii::t('app', 'Zadanie jednorazowe'),
        1=>Yii::t('app', 'Co tydzień'),
        2=>Yii::t('app', 'Co dwa tygodnie'),
        3=>Yii::t('app', 'Co miesiąc'),
        4=>Yii::t('app', 'Co 3 miesiące'),
        5=>Yii::t('app', 'Co pół roku'),
        6=>Yii::t('app', 'Co rok'),
        ];
    }

    public function getCyclicLabel()
    {
        $types = $this->getCyclicTypes();
        return $types[$this->cyclic_type];
    }

    public function copyMe()
    {
        $clone = new Task;
        $clone->attributes = $this->attributes;
        $clone->save();
        foreach ($this->users as $user)
        {
            $tu = new UserTask;
            $tu->task_id = $clone->id;
            $tu->user_id = $user->id;
            $tu->save();
        }
        foreach ($this->notificationUsers as $user)
        {
            $tu = new TaskNotificationUser;
            $tu->task_id = $clone->id;
            $tu->user_id = $user->id;
            $tu->save();
        }
        foreach ($this->taskNotifications as $not)
        {
            $tu = new TaskNotification;
            $tu->attributes = $not->attributes;
            $tu->sent = 0;
            $tu->task_id = $clone->id;
            $tu->save();
        }
        return $clone;
    }


    public function updateCyclicDate()
    {
            $start = $this->datetime;
            $rok = substr($start, 0, 4);
            $miesiac = substr($start, 5, 2);
            $dzien = substr($start, 8, 2);
            $godzina = substr($start, 11, 2);
            
        if ($this->cyclic_type==1)
        {
            $time = mktime($godzina, 0, 0, $miesiac, $dzien+7, $rok);
        }
        if ($this->cyclic_type==2)
        {
            $time = mktime($godzina, 0, 0, $miesiac, $dzien+14, $rok);
        }
        if ($this->cyclic_type==3)
        {
            $time = mktime($godzina, 0, 0, $miesiac+1, $dzien, $rok);
        }
        if ($this->cyclic_type==4)
        {
            $time = mktime($godzina, 0, 0, $miesiac+3, $dzien, $rok);
        }
        if ($this->cyclic_type==5)
        {
            $time = mktime($godzina, 0, 0, $miesiac+6, $dzien, $rok);
        }
        if ($this->cyclic_type==6)
        {
            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok+1);
        }
        $this->datetime = date("Y-m-d H:i:s", $time);
        $this->save();
        $today = date('Y-m-d');
        while($this->datetime<$today)
        {
            $this->updateCyclicDate();
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->for_event)
        {
            if ($insert)
            {
                $expense = new EventExpense();
                $expense->task_id = $this->id;
                $expense->name = Yii::t('app', 'Zadanie').": ".$this->title;
                $expense->event_id = $this->for_event;
                $expense->amount = $this->getAllCosts();
                $expense->creator_id = Yii::$app->user->id;
                if (!$expense->save())
                    echo var_dump($expense);
            }else{
                $expense = EventExpense::find()->where(['task_id'=>$this->id])->one();          
                if (!$expense)
                {
                    $expense = new EventExpense();
                    $expense->task_id = $this->id;
                    $expense->name = Yii::t('app', 'Zadanie').": ".$this->title;
                    $expense->event_id = $this->for_event;
                    $expense->amount = $this->getAllCosts();
                    $expense->creator_id = Yii::$app->user->id;
                    $expense->save();
                }else{
                    if ((isset($changedAttributes['for_event']))&&($this->for_event!=$changedAttributes['for_event']))
                    {
                        $expense->event_id = $this->for_event;
                        $expense->save();
                    }
                }
            }

            
        }
        if (!$insert)
        {
            if ((isset($changedAttributes['title']))&&($this->title!=$changedAttributes['title']))
            {
                        Chat::sendChangeTask($this, $changedAttributes['title']);
                        $note = new TaskNote();
                        $note->task_id = $this->id;
                        $note->user_id = Yii::$app->user->id;
                        $note->text = Yii::t('app', 'Zmieniono nazwę zadania z ').$changedAttributes['title'].Yii::t('app', ' na ').$this->title;
                        $note->save();
                        $et = EventTask::find()->where(['task_id'=>$this->id])->all();
                        foreach ($et as $e)
                        {
                            $event = Event::findOne(['id'=>$e->event_id]);
                            $event->name = $this->title;
                            $event->save();
                        }
            }
        }
    }

    public function beforeDelete()
    {
        EventExpense::deleteAll(['task_id'=>$this->id]); 
        Chat::sendDeleteTaskNote($this);
        TaskNotification::deleteAll(['task_id'=>$this->id]);
        return true;
    }

    public function updateExpense()
    {
        $expense = EventExpense::find()->where(['task_id'=>$this->id])->one(); 
        if ($expense)
        {
            $expense->amount = $this->getAllCosts();
            $expense->save();
        }
    }

    public function getAllCosts()
    {
        $costs = EventUserWorkingTime::find()->where(['task_id'=>$this->id])->all();
        $total = 0;
        foreach ($costs as $cost)
        {
            $user = $cost->user;
            $rate = $user->rate_amount;
            $type = $user->rate_type;
            $hours = $cost->duration / 3600;
            if ($type == 1) {
                $salary = $hours * $rate;
            }
            else {
                $time4hPeriods = floor($hours / 4);
                $salary4hPeriods = floor($rate / $type * 4);
                $salary = $time4hPeriods * $salary4hPeriods;
            }
            $total +=$salary;
        }
        return $total;

    }

    public function getEventProdukcja()
    {
        $ids = ArrayHelper::map(EventTask::find()->where(['task_id'=>$this->id])->asArray()->all(), 'event_id', 'event_id');
        $event = Event::find()->where(['id'=>$ids])->one();
        if ($event)
            return $event;
        else
            return null;
    }


    public function prepareForCalendar()
    {
        $description = $this->title."<br/>".Yii::t('app', 'Przypisani:');
            $users = "[";
            foreach ($this->getAllUsers() as $eu)
            {
                if ($users != "[")
                    $users .=", ";
                $users .=$eu->getInitials();
                $description .= $eu->displayLabel.", ";
            }
            $description .= "<br/>".$this->content;
        $users .="]";
        $whole = false;
            if ((substr($this->from, 11, 8)==substr($this->datetime, 11, 8))&&(substr($this->from, 11, 8)=="00:00:00"))
            {
                $whole = true;
            }
        $notes = TaskNote::find()->where(['task_id'=>$this->id])->count();
        $att = TaskAttachment::find()->where(['task_id'=>$this->id])->count();
        return ['title'=> $this->title." {".$this->event->name."}", 'type'=>'task', 'id'=>$this->id, 'start'=>substr($this->from, 0, 10)."T".substr($this->from, 11, 8), 'end'=>substr($this->datetime, 0, 10)."T".substr($this->datetime, 11, 8), 'className'=>'task status-'.$this->status, 'notes'=>$notes, 'users'=>$users, 'files'=>$att, 'allDay'=>$whole, 'description'=>$description];
    }

    public function linkTeams()
    {
        if ($this->teamIds)
        {
            foreach ($this->teamIds as $id)
            {
                $users = TeamUser::find()->where(['team_id'=>$id])->all();
                foreach ($users as $u)
                {
                    $tu = UserTask::find()->where(['task_id'=>$this->id])->andWhere(['user_id'=>$u->user_id])->one();
                    if (!$tu)
                    {
                        $tu = new UserTask(['task_id'=>$this->id, 'user_id'=>$u->user_id]);
                        $tu->save();
                    }
                }
            }
        }
    }



}

