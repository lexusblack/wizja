<?php
namespace backend\controllers;

use backend\actions\UploadAction;
use backend\models\PasswordChange;
use backend\models\MaskCreator;
use backend\models\PasswordForget;
use backend\models\StatsForm;
use common\components\filters\AccessControl;
use common\models\EventUserWorkingTime;
use common\models\EventWorkingTimeRole;
use common\models\form\Dashboard;
use common\models\form\Stat;
use common\models\form\FirstUse;
use common\models\Order;
use common\models\Request;
use common\models\EventOuterGear;
use common\models\EventUser;
use common\models\EventUserRole;
use common\models\EventUserPlannedWrokingTime;
use common\models\Notification;
use common\models\Rent;
use common\models\Event;
use common\models\Project;
use common\models\RentGear;
use common\models\EventGear;
use common\models\RentGearItem;
use common\models\EventGearItem;
use common\models\User;
use common\models\Gear;
use common\models\GearItem;
use common\models\Company;
use common\models\Vehicle;
use common\models\CompanyLog;
use common\models\TaskNotification;
use common\models\Meeting;
use Yii;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use frontend\models\SignupForm;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'timer', 'order', 'confirm', 'confirmone', 'send-mail', 'first-use', 'company-stat', 'count-gears', 'forget-password', 'send-reminders', 'generate-photo', 'repair', 'not-active', 'repair-outcome', 'get-photo', 'update-data', 'send-app', 'send-reminders-event', 'update-list', 'send-report'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['calendar', 'calendar2', 'calendar-plan'],
                        'allow' => true,
                        'roles' => ['menuCalendar']
                    ],
                    [
                        'actions' => ['calendar-produkcja', 'calendar-wydruki', 'get-for-calendar'],
                        'allow' => true,
                        'roles' => ['menuCalendarProdukcja']
                    ],
                    [
                        'actions' => ['stats'],
                        'allow' => true,
                        'roles' => ['menuStats']
                    ],
                    [
                        'actions' => ['index', 'first-use', 'mask-creator'],
                        'allow' => true,
                        'roles' => ['menuCockpit']
                    ],
                    [
                        'actions' => ['logout', 'update-password'],
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'upload'=> [
                'class'=>UploadAction::className(),

            ]
        ];
    }

    public function actionSendReport()
    {
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
        $superusers = User::find()->where(['active'=>1])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->andWhere(['>', 'last_visit', date("Y-m")])->andWhere(['NOT IN', 'username', ['support@newsystems.pl']])->all();
        $content = "Zalogowanych superuserów w miesiącu ".date("Y-m").": ".count($superusers)." użytkowników.<br/><br/>";
        foreach ($superusers as $u)
        {
            $content .=$u->first_name." ".$u->last_name." (".$u->username.") ".$u->last_visit."<br/>";
        }
                    
        $mail = \Yii::$app->mailer->compose()
        ->setHtmlBody($content)
                    ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->params['mailingEmail']])
                    ->setBcc('marqiz87@gmail.com')
                    ->setTo(['patrycja@newsystems.pl', 'patrycja.barton8@gmail.com'])
                    ->setSubject(Yii::t('app', 'Raport DJAK ').date("Y-m"));            
                    if ($mail->send()){}
        exit;
    }

    public function actionSendApp()
    {
        $number = Yii::$app->request->post("phone");
        if (Yii::$app->request->post("type")=="apple")
        {
            $text ="https://itunes.apple.com/pl/app/newsystems/id1459855183?l=pl";
        }else{
            $text ="https://play.google.com/store/apps/details?id=pl.newsystems.android.app";
        }

        $response = \common\components\Sms::load()->messages->sendSms(
                $number,
                $text,
                \common\components\Sms::getSender(),
                [
                    'test' => false,
                    'details'=>true,
                    'date' => null,
                    'sender'=> \common\components\Sms::getSender()
                ]);
        exit;
    }

    public function actionUpdateList()
    {
        $this->layout = 'empty';
       
            return $this->render('update-list');
        
    }

    public function actionUpdateData()
    {
       
              $item = \common\models\AuthItem::find()->where(['name'=>'eventsEventEditStatus'])->one();
        if (!$item)
        {
            $item = new \common\models\AuthItem(['name'=>'eventsEventEditStatus', 'type'=>2]);
            $item->save();
        }
        $roles = \common\models\AuthItem::find()->where(['type'=>1])->all();
        foreach ($roles as $role)
        {
            $x = \common\models\AuthItemChild::find()->where(['child'=>'eventsEventEditStatus', 'parent'=>$role->name])->one();
            if (!$x)
            {
                $x = new \common\models\AuthItemChild(['child'=>'eventsEventEditStatus', 'parent'=>$role->name]);
                $x->save();
            }
        }
       $item = \common\models\AuthItem::find()->where(['name'=>'gearWarehouseQuantity'])->one();
        if (!$item)
        {
            $item = new \common\models\AuthItem(['name'=>'gearWarehouseQuantity', 'type'=>2]);
            $item->save();
        }
        $roles = \common\models\AuthItem::find()->where(['type'=>1])->all();
        foreach ($roles as $role)
        {
            $x = \common\models\AuthItemChild::find()->where(['child'=>'gearWarehouseQuantity', 'parent'=>$role->name])->one();
            if (!$x)
            {
                $x = new \common\models\AuthItemChild(['child'=>'gearWarehouseQuantity', 'parent'=>$role->name]);
                $x->save();
            }
        }
        $item = \common\models\AuthItem::find()->where(['name'=>'calendarDetails'])->one();
        if (!$item)
        {
            $item = new \common\models\AuthItem(['name'=>'calendarDetails', 'type'=>2]);
            $item->save();
        }
        $roles = \common\models\AuthItem::find()->where(['type'=>1])->all();
        foreach ($roles as $role)
        {
            $x = \common\models\AuthItemChild::find()->where(['child'=>'calendarDetails', 'parent'=>$role->name])->one();
            if (!$x)
            {
                $x = new \common\models\AuthItemChild(['child'=>'calendarDetails', 'parent'=>$role->name]);
                $x->save();
            }
        }
        $item = \common\models\AuthItem::find()->where(['name'=>'eventsEventEditEyeClientDetails'])->one();
        if (!$item)
        {
            $item = new \common\models\AuthItem(['name'=>'eventsEventEditEyeClientDetails', 'type'=>2]);
            $item->save();
        }
        $roles = \common\models\AuthItem::find()->where(['type'=>1])->all();
        foreach ($roles as $role)
        {
            $x = \common\models\AuthItemChild::find()->where(['child'=>'eventsEventEditEyeClientDetails', 'parent'=>$role->name])->one();
            if (!$x)
            {
                $x = new \common\models\AuthItemChild(['child'=>'eventsEventEditEyeClientDetails', 'parent'=>$role->name]);
                $x->save();
            }
        }

        $vehicles = \common\models\Vehicle::find()->all();
        foreach ($vehicles as $vehicle)
        {
            $v = new \common\models\VehicleModel();
            $v->id = $vehicle->id;
            $v->name = $vehicle->name_in_offer;
            $v->capacity = $vehicle->capacity;
            $v->volume = $vehicle->volume;
            $v->active = $vehicle->active;
            $v->save();
        }
        foreach ($vehicles as $vehicle)
        {
            $v = new \common\models\VehiclePrice();
            $v->vehicle_model_id = $vehicle->id;
            $v->name = "km";
            $v->price = $vehicle->price_km;
            $v->cost = $vehicle->cost_km;
            $v->default = 1;
            $v->unit = "km";
            $v->currency = "PLN";
            $v->save();
            $v = new \common\models\VehiclePrice();
            $v->vehicle_model_id = $vehicle->id;
            $v->name = "ryczałt";
            $v->price = $vehicle->price_city;
            $v->cost = $vehicle->cost_city;
            $v->default = 0;
            $v->unit = "d";
            $v->currency = "PLN";
            $v->save();
        }
        $roles = \common\models\UserEventRole::find()->where(['active'=>1])->all();
        foreach ($roles as $role)
        {
            $r = new \common\models\RolePrice();
            $r->role_id = $role->id;
            $r->price = $role->salary_customer;
            $r->name = "dzienna";
            $r->currency = "PLN";
            $r->default = 1;
            $r->cost = $role->salary;
            $r->cost_hour = $role->salary_hours;
            $r->unit = "d";
            $r->save();
            $r = new \common\models\RolePrice();
            $r->role_id = $role->id;
            $r->price = $role->salary_customer_hours;
            $r->name = "godzinowa";
            $r->currency = "PLN";
            $r->default = 0;
            $r->cost = $role->salary;
            $r->cost_hour = $role->salary_hours;
            $r->unit = "h";
            $r->save();
        }

        $ov = \common\models\OfferVehicle::find()->where(['type'=>NULL])->all();
        foreach ($ov as $c)
        {
            $c->type = 3;
            $c->save();
        }
        exit;
    }

    public function actionRepairOutcome()
    {
        $items = GearItem::find()->where(['active'=>1])->all();
        foreach ($items as $item)
        {
                    $item->outcomed = $item->isAvailableForOutcome2();
                    $item->update_time = date('Y-m-d H:i:s');
                    $item->save();
        }
        exit;
    }

    public function actionRepair()
    {
        /*$event =EventUserRole::find()->where(['working_hours_id'=>0])->orWhere(['working_hours_id'=>null])->all();
        foreach ($event as $e)
        {
            echo "<br/>".$e->eventUser->event_id;
            $workings = EventUserPlannedWrokingTime::find()->where(['event_id'=>$e->eventUser->event_id])->andWhere(['user_id'=>$e->eventUser->user_id])->all();
            echo " ".count($workings);
            foreach ($workings as $w)
            {
                $new = clone $e;
                $new->working_hours_id = $w->id;
                $new->save();
            }
            if ($workings)
                    $e->delete();
        }
        $gears = Gear::find()->where(['no_items'=>1])->andWhere(['active'=>1])->all();
        foreach ($gears as $gear)
        {
            $items = GearItem::find()->where(['gear_id'=>$gear->id])->andWhere(['active'=>1])->all();
            if (!$items)
            {
                $gear->no_items = 0;
                $gear->save();
                $gear->no_items = 1;
                $gear->save();               
            }

        }*/
        $incomes = \common\models\IncomesForEvent::find()->where(['packlist_id'=>NULL])->all();
        foreach ($incomes as $i)
        {
            $packlist = \common\models\Packlist::findOne(['event_id'=>$i->event_id, 'main'=>1]);
            $i->packlist_id = $packlist->id;
            $i->save();
        }
        $incomes = \common\models\OutcomesForEvent::find()->where(['packlist_id'=>NULL])->all();
        foreach ($incomes as $i)
        {
            $packlist = \common\models\Packlist::findOne(['event_id'=>$i->event_id, 'main'=>1]);
            $i->packlist_id = $packlist->id;
            $i->save();
        }
        exit;
    }

    public function actionSendRemindersEvent($id)
    {
        $newUsers = EventUser::find()->where(['new'=>1])->andWhere(['event_id'=>$id])->all();
        foreach ($newUsers as $eu)
        {
            $eu->new = 0;
            $eu->save();
                Notification::sendUserNotifications($eu->user, Notification::USER_ADDED_TO_EVENT, [$eu->event, $eu]);
        }
        $editedUsers = EventUser::find()->where(['edited'=>1])->andWhere(['event_id'=>$id])->all();
        foreach ($editedUsers as $eu)
        {
            $eu->edited = 0;
            $eu->save();
                Notification::sendUserNotifications($eu->user, Notification::EVENT_SCHEDULE_CHANGE, [$eu->event, $eu]);
        }
    }

    public function actionSendReminders()
    {
        

        $newUsers = EventUser::find()->where(['new'=>1])->all();
        foreach ($newUsers as $eu)
        {
            if ($eu->event->send_reminders)
            {
                $eu->new = 0;
                $eu->save();
                Notification::sendUserNotifications($eu->user, Notification::USER_ADDED_TO_EVENT, [$eu->event, $eu]);
            }
            
        }
        $editedUsers = EventUser::find()->where(['edited'=>1])->all();
        foreach ($editedUsers as $eu)
        {
            
            if ($eu->event->send_reminders)
            {
                $eu->edited = 0;
                $eu->save();
                Notification::sendUserNotifications($eu->user, Notification::EVENT_SCHEDULE_CHANGE, [$eu->event, $eu]);
            }
            
        }

        //wysyłka przypomnień o spotkaniu
        $meetings = Meeting::find()->where(['>', 'reminder', 0])->andWhere(['reminder_sent'=>0])->andWhere(['>', 'start_time', $date])->andWhere(['active'=>1])->all();
        //echo var_dump($meetings);
        foreach ($meetings as $meeting)
        {
            $sending_time = new DateTime($meeting->start_time);
            $sending_time->sub(new DateInterval('PT' . $meeting->reminder . 'M'));
            if ($sending_time <= new DateTime('now'))
            {
                //wysyłamy powiadomienia
                if ($meeting->remind_sms) {
                    foreach ($meeting->users as $user) {
                        if ($user->phone) {
                            $sms = new \common\models\NotificationSms();
                            $sms->setPersonalEventText($meeting->name, $meeting->start_time);
                            $sms->user_id = $user->id;
                            $sms->sending_time = null;

                            $reminder_sms = new \common\models\MeetingSmsReminder();
                            $reminder_sms->meeting_id = $meeting->id;
                            $reminder_sms->sms_id = $sms->send();;
                            $reminder_sms->save();
                        }
                    }
                }
                if ($meeting->remind_email) {
                    foreach ($meeting->users as $user) {
                        $mail = new \common\models\NotificationMail();
                        $mail->setPersonalEventText($meeting->name, $meeting->start_time);
                        $mail->user_id = $user->id;
                        $mail->sending_time = null;
                        $mail->email_address = $user->email;
                        $mail->save();

                        $mailReminder = new \common\models\MeetingMailReminder();
                        $mailReminder->mail_id = $mail->id;
                        $mailReminder->meeting_id = $meeting->id;
                        $mailReminder->save();
                    }
                }
                $meeting->reminder_sent=1;
                $meeting->save();

            }
        }
        /*
        $outcomed = GearItem::find()->where(['>', 'outcomed', 0])->all();
        $now = date('Y-m-d H:i:s');
        $stop_date = date('Y-m-d H:i:s', strtotime($now)-18000);
        $ev_count_date = date('Y-m-d H:i:s', strtotime($now)+260000);
        $notification = [];
        $og_ids = [];
        foreach ($outcomed as $item)
        {
            if ($item->gear->no_items)
            {
                                        //wyszukujemy wszystkie eventy, na które został wydany ten sprzęt?
                                        $outcomes_ids = ArrayHelper::map(\common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->asArray()->all(), 'outcome_id', 'outcome_id');
                                        $event_ids = ArrayHelper::map(\common\models\OutcomesForEvent::find()->where(['outcome_id'=>$outcomes_ids])->asArray()->all(), 'event_id', 'event_id');
                                        $rent_ids = ArrayHelper::map(\common\models\OutcomesForRent::find()->where(['outcome_id'=>$outcomes_ids])->asArray()->all(), 'rent_id', 'rent_id');
                                        $events2 = Event::find()->where(['id'=>$event_ids])->orderBy(['event_start' =>SORT_DESC])->all();
                                        $rents = Rent::find()->where(['id'=>$rent_ids])->orderBy(['start_time' =>SORT_DESC])->all();
                                        $total = 0;
                                        foreach ($events2 as $e)
                                        {
                                            $e_total = 0;
                                            if ($total<$item->outcomed)
                                            {
                                                    $o_ids = ArrayHelper::map(\common\models\OutcomesForEvent::find()->where(['event_id'=>$e['id']])->asArray()->all(), 'outcome_id', 'outcome_id');
                                                    $outcomes = \common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['outcome_id'=>$o_ids])->asArray()->all();

                                                    foreach ($outcomes as $o)
                                                    {
                                                        if (!$o['sent'])
                                                        {
                                                            $e_total+=$o['gear_quantity'];
                                                        }
                                                    } 
                                                    $i_ids = ArrayHelper::map(\common\models\IncomesForEvent::find()->where(['event_id'=>$e['id']])->asArray()->all(), 'income_id', 'income_id');
                                                    $incomes = \common\models\IncomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['income_id'=>$i_ids])->asArray()->all();
                                                    foreach ($incomes as $o)
                                                    {
                                                        $e_total-=$o['gear_quantity'];
                                                    }
                                                    if ($e_total>0)
                                                    {
                                                        $total+=$e_total;

                                                        if ($e->getTimeEnd()<$stop_date)
                                                        {
                                                            //opóżnione więcej niż 5 godzin
                                                            foreach ($outcomes as $o)
                                                            {
                                                                if (!$o['sent'])
                                                                {
                                                                    $og_ids[] = $o['id'];
                                                                }
                                                            }
                                                            if (!isset($notification[$item->gear_id]))
                                                            {
                                                                $notification[$item->gear_id]['name']= $item->gear->name;
                                                                $notification[$item->gear_id]['events']= [];
                                                                $notification[$item->gear_id]['rents']= [];
                                                            }
                                                            if (!isset($notification[$item->gear_id]['events'][$e->id])){
                                                                $notification[$item->gear_id]['events'][$e->id]['name']=$e->name;
                                                                $notification[$item->gear_id]['events'][$e->id]['count']=$e_total;
                                                                $notification[$item->gear_id]['events'][$e->id]['numbers']="";
                                                            }else{
                                                                $notification[$item->gear_id]['events'][$e->id]['count']+=$e_total;
                                                                $notification[$item->gear_id]['events'][$e->id]['numbers']="";
                                                            }

                                                            
                                                        }
                                                    }
                                            }

                                        }
                                        foreach ($rents as $e)
                                        {
                                            $e_total = 0;
                                            if ($total<$item->outcomed)
                                            {
                                                    $o_ids = ArrayHelper::map(\common\models\OutcomesForRent::find()->where(['rent_id'=>$e->id])->asArray()->all(), 'outcome_id', 'outcome_id');
                                                    $outcomes = \common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['outcome_id'=>$o_ids])->asArray()->all();
                                                    foreach ($outcomes as $o)
                                                    {
                                                        if (!$o['sent'])
                                                        {
                                                            $e_total+=$o['gear_quantity'];
                                                        }
                                                    } 
                                                    $i_ids = ArrayHelper::map(\common\models\IncomesForRent::find()->where(['rent_id'=>$e->id])->asArray()->all(), 'income_id', 'income_id');
                                                    $incomes = \common\models\IncomesGearOur::find()->where(['gear_id'=>$item->id])->andWhere(['income_id'=>$i_ids])->asArray()->all();
                                                    foreach ($incomes as $o)
                                                    {
                                                        $e_total-=$o['gear_quantity'];
                                                    }
                                                    if ($e_total>0)
                                                    {
                                                        $total+=$e_total;

                                                        if ($e->end_time<$stop_date)
                                                        {
                                                            //opóżnione więcej niż 5 godzin
                                                            foreach ($outcomes as $o)
                                                            {
                                                                if (!$o['sent'])
                                                                {
                                                                    $og_ids[] = $o['id'];
                                                                }
                                                            }
                                                            
                                                            if (!isset($notification[$item->gear_id]))
                                                            {
                                                                $notification[$item->gear_id]['name']= $item->gear->name;
                                                                $notification[$item->gear_id]['events']= [];
                                                                $notification[$item->gear_id]['rents']= [];
                                                            }
                                                            if (!isset($notification[$item->gear_id]['rents'][$e->id])){
                                                                $notification[$item->gear_id]['rents'][$e->id]['name']=$e->name;
                                                                $notification[$item->gear_id]['rents'][$e->id]['count']=$e_total;
                                                                $notification[$item->gear_id]['events'][$e->id]['numbers']="";
                                                            }else{
                                                                $notification[$item->gear_id]['rents'][$e->id]['count']+=$e_total;
                                                                $notification[$item->gear_id]['rents'][$e->id]['numbers']="";
                                                            }

                                                            
                                                        }
                                                    }
                                            }

                                        }
            }else{
                $og = \common\models\OutcomesGearOur::find()->where(['gear_id'=>$item->id])->orderBy(['id'=>SORT_DESC])->one();
                
                if (($og)&&(!$og->sent))
                {
                    $event = \common\models\OutcomesForEvent::find()->where(['outcome_id'=>$og->outcome_id])->one();
                    if ($event)
                    {
                        if ($event->event->getTimeEnd()<$stop_date)
                        {
                            $og_ids[] = $og->id;
                            //opóżnione więcej niż 5 godzin
                            if (!isset($notification[$item->gear_id]))
                            {
                                $notification[$item->gear_id]['name']= $item->gear->name;
                                $notification[$item->gear_id]['events']= [];
                                $notification[$item->gear_id]['rents']= [];
                            }
                            if (!isset($notification[$item->gear_id]['events'][$event->event_id])){
                                $notification[$item->gear_id]['events'][$event->event_id]['name']=$event->event->name;
                                $notification[$item->gear_id]['events'][$event->event_id]['count']=1;
                                $notification[$item->gear_id]['events'][$event->event_id]['numbers']=$item->number;
                            }else{
                                $notification[$item->gear_id]['events'][$event->event_id]['count']+=1;
                                $notification[$item->gear_id]['events'][$event->event_id]['numbers'].=", ".$item->number;
                            }

                            
                        }
                    }
                    $event = \common\models\OutcomesForRent::find()->where(['outcome_id'=>$og->outcome_id])->one();
                    if ($event)
                    {
                        if ($event->rent->end_time<$stop_date)
                        {
                            $og_ids[] = $og->id;
                            //opóżnione więcej niż 5 godzin
                            if (!isset($notification[$item->gear_id]))
                            {
                                $notification[$item->gear_id]['name']= $item->gear->name;
                                $notification[$item->gear_id]['events']= [];
                                $notification[$item->gear_id]['rents']= [];
                            }
                            if (!isset($notification[$item->gear_id]['rents'][$event->rent_id])){
                                $notification[$item->gear_id]['rents'][$event->rent_id]['name']=$event->rent->name;
                                $notification[$item->gear_id]['rents'][$event->rent_id]['count']=1;
                                $notification[$item->gear_id]['rents'][$event->rent_id]['numbers']=$item->number;
                            }else{
                                $notification[$item->gear_id]['rents'][$event->rent_id]['count']+=1;
                                $notification[$item->gear_id]['rents'][$event->rent_id]['numbers'].=", ".$item->number;
                            }
                        }
                    }

                }

            }

        }
                    $users = [];
                    $contents = [];
                    foreach ($notification as $key => $n)
                    {
                        $content = "";
                        $event_gear = EventGear::find()->where(['gear_id'=>$key])->andWhere(['<', 'start_time', $ev_count_date])->andWhere(['>', 'start_time', $now])->all();
                        $rent_gear = RentGear::find()->where(['gear_id'=>$key])->andWhere(['<', 'start_time', $ev_count_date])->andWhere(['>', 'start_time', $now])->all();
                        if (($event_gear)||($rent_gear))
                        {
                            //sprzęt gra w ciągu 3 dni - wysyłamy do PM maila
                            $ogs = \common\models\OutcomesGearOur::find()->where(['id'=>$og_ids])->andWhere(['gear_id'=>ArrayHelper::map(GearItem::find()->where(['gear_id'=>$key])->andWhere(['sent'=>0])->asArray()->all(), 'id', 'id')])->all();
                            foreach ($ogs as $og)
                            {
                                $og->sent = 1;
                                if ($og->save())
                                    echo "zapis";
                            }
                            $content .=$n['name']." ".Yii::t('app', 'nie wrócił na czas z wydarzenia: ');
                            foreach ($n['events'] as $event)
                            {
                                $content.=$event['name']." - ".$event['count']." ".Yii::t('app', ' szt.');
                                if ($event['numbers'])
                                    $content.=" numery: ".$event['numbers'];
                                $content .="<br/>";
                            }
                            foreach ($n['rents'] as $event)
                            {
                                $content.=$event['name']." - ".$event['count']." ".Yii::t('app', ' szt.');
                                if ($event['numbers'])
                                    $content.=" numery: ".$event['numbers'];
                                $content .="<br/>";
                            }
                            $content .=Yii::t('app', 'Ten sprzęt jest zarezerwowany na: ')."<br/>";
                            $content .="<br/><br/>";
                            foreach ($event_gear as $e)
                            {
                                $content .=$e->event->name." - ".$e->quantity." ".Yii::t('app', ' szt.')." od ".$e->start_time."<br/>";
                                if ($e->event->manager_id){
                                    $users[$e->event->manager_id] = $e->event->manager->email;
                                    if (!isset($contents[$e->event->manager_id])){
                                        $contents[$e->event->manager_id] = $content;
                                    }else{
                                        $contents[$e->event->manager_id] .= $content;
                                    }
                                }
                            }
                            foreach ($rent_gear as $e)
                            {
                                $content .=$e->rent->name." - ".$e->quantity." ".Yii::t('app', ' szt.')." od ".$e->start_time."<br/>";
                                if ($e->rent->manager_id)
                                    $users[$e->rent->manager_id] = $e->rent->manager->email;
                                if (!isset($contents[$e->rent->manager_id])){
                                        $contents[$e->rent->manager_id] = $content;
                                    }else{
                                        $contents[$e->rent->manager_id] .= $content;
                                    }
                            }
                            
                        }
                    }
                    foreach ($users as $id => $u)
                    {
                        echo $contents[$id];
                        $sent = \Yii::$app->mailer->compose('mailNotification', ['title'=>Yii::t('app', 'Sprzęt nie wrócił z wydarzenia'), 'content'=>$contents[$id]])
                        ->setFrom([
                            Yii::$app->params['mailingEmail'] => Yii::$app->settings->get('companyName', 'main'),
                        ])
                        ->setTo($u)
                        ->setSubject(Yii::t('app', 'Sprzęt nie wrócił z wydarzenia'))
                        ->send();
                    }
                    */
                date_default_timezone_set(Yii::$app->params['timeZone']);
        $date = date('Y-m-d H:i:s');
        $reminders = TaskNotification::find()->where(['sent'=>0])->all();
        foreach ($reminders as $reminder)
        {
            if ($reminder->checkSendDate($date))
            {
                echo $reminder->task->title;
                $reminder->sendReminders();
            }
        }
        exit;
    }

    public function actionGeneratePhoto($initial)
    {
        // Create a 100*30 image
        $im = imagecreate(100, 100);

        // White background and blue text
        $bg = imagecolorallocate($im, 209, 218, 222);
        $textcolor = imagecolorallocate($im, 94, 94, 94);

        // Write the string at the top left
        //imagestring($im, 5, 30, 30, $initial, $textcolor);
        $font = $_SERVER['DOCUMENT_ROOT'] . '/fonts/Roboto-Black.ttf';

        // Add some shadow to the text
        imagettftext($im, 30, 0, 10, 60, $textcolor, $font, $initial);
        // Output the image
        header('Content-type: image/png');

        imagepng($im);
        imagedestroy($im);
        exit;
    }

    public function actionGetPhoto($id)
    {
        $user = User::findOne($id);
        if ($user->photo)
        {
            $filePath = $user->getFilePath();
            return Yii::$app->response->sendFile($filePath);
        }else{
            $initial = $user->getInitials();
            // Create a 100*30 image
        $im = imagecreate(100, 100);

        // White background and blue text
        $bg = imagecolorallocate($im, 209, 218, 222);
        $textcolor = imagecolorallocate($im, 94, 94, 94);

        // Write the string at the top left
        //imagestring($im, 5, 30, 30, $initial, $textcolor);
        $font = $_SERVER['DOCUMENT_ROOT'] . '/fonts/Roboto-Black.ttf';

        // Add some shadow to the text
        imagettftext($im, 30, 0, 10, 60, $textcolor, $font, $initial);
        // Output the image
        header('Content-type: image/png');

        imagepng($im);
        imagedestroy($im);
        exit;
        }

        
    }

    public function actionCompanyStat()
    {


        /*$item = \common\models\AuthItem::find()->where(['name'=>'eventsEventEditStatus'])->one();
        if (!$item)
        {
            $item = new \common\models\AuthItem(['name'=>'eventsEventEditStatus', 'type'=>2]);
            $item->save();
        }
        $roles = \common\models\AuthItem::find()->where(['type'=>1])->all();
        foreach ($roles as $role)
        {
            $x = \common\models\AuthItemChild::find()->where(['child'=>'eventsEventEditStatus', 'parent'=>$role->name])->one();
            if (!$x)
            {
                $x = new \common\models\AuthItemChild(['child'=>'eventsEventEditStatus', 'parent'=>$role->name]);
                $x->save();
            }
        }   *   
        $events = Event::find()->where(['>', 'event_start', '2019-01-01'])->all();
        foreach ($events as $event)
        {
            $event->getGProvisions();
        }
    */
        $gears = Gear::find()->where(['active'=>1])->count();
        $gears2 = Gear::find()->where(['active'=>1])->all();
        /*foreach ($gears2 as $gear)
        {
            $price = new \common\models\GearPrice();
            $price->gear_id = $gear->id;
            $price->gears_price_id = 4;
            $price->price = $gear->price;
            $price->save();
        }*/
        $events = Event::find()->count();
        $rents = Rent::find()->count();
        $users = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->count();
        $model = Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $vehicles = Vehicle::find()->where(['active'=>1])->all();
        
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
        $superusers = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count();
        $groups_super_user2 = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>2])->asArray()->all(), 'name', 'name');
        $users_plus = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user2])->asArray()->all(), 'user_id', 'user_id')])->count();
        foreach ($vehicles as $vehicle)
        {
            if ($vehicle->reminder){
                $oc = date('Y-m-d', strtotime('-'.$vehicle->reminder.' days', strtotime($vehicle->oc_date)));
                $today = date('Y-m-d');
                $inspection = date('Y-m-d', strtotime('-'.$vehicle->reminder.' days', strtotime($vehicle->inspection_date)));
                if ($oc==$today)
                {
                    foreach ($vehicle->users as $user)
                    {
                        $time = date("Y-m-d H:i:s");
                        $subject = Yii::t('app', 'Koniec ważności OC');
                        Notification::sendUserSmsNotification($user, Yii::t('app', 'Zbliża się termin końca ubezpieczenia OC w pojeździe ').$vehicle->name, $time);
                        Notification::sendUserMailNotification($user, $subject, Yii::t('app', 'Zbliża się termin końca ubezpieczenia OC w pojeździe ').$vehicle->name);
                    }
                }
                if ($inspection==$today)
                {
                    foreach ($vehicle->users as $user)
                    {
                        $time = date("Y-m-d H:i:s");
                        $subject = Yii::t('app', 'Zbliża się data przeglądu');
                        Notification::sendUserSmsNotification($user, Yii::t('app', 'Zbliża się termin przeglądu w pojeździe ').$vehicle->name, $time);
                        Notification::sendUserMailNotification($user, $subject, Yii::t('app', 'Zbliża się termin przeglądu w pojeździe ').$vehicle->name);
                    }
                }
            }
            
        }
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT count(*) FROM information_schema.columns WHERE `TABLE_SCHEMA` = '".$model->code."'");

        $result = $command->queryScalar();
        $model->gears = $gears;
        $model->events = $events;
        $model->rents = $rents;
        $model->users = $users-$superusers-$users_plus;
        $model->columns = $result;
        $model->superusers = $superusers;
        $model->users_plus = $users_plus;
        $model->flota = count($vehicles);
        $model->checklist = \common\models\Checklist::find()->count();
        $model->wydania = \common\models\OutcomesWarehouse::find()->count();
        $model->serwis = \common\models\GearService::find()->count();
        $model->rozliczenia = \common\models\EventUserWorkingTime::find()->count();
        $model->planowanie_ekipy =  \common\models\EventUser::find()->count();
        $model->planowanie_sprzetu  =  \common\models\EventGear::find()->count();
        $model->zestawy = \common\models\GearSet::find()->count();
        $model->case = \common\models\GearGroup::find()->count();
        $model->zadania = \common\models\Task::find()->count();
        $model->magazyny = \common\models\Warehouse::find()->count();
        $model->crossrental = \common\models\CrossRental::find()->where(['owner'=>$model->code])->count();
        if (isset(\Yii::$app->params['version']))
        {
            $model->version = \Yii::$app->params['version'];
        }
        $settings = \common\models\Settings::find()->indexBy('key')->where(['section'=>'main'])->all();

        $to = $settings['companyCity']->value.", ".$settings['companyAddress']->value;
        if ($settings['companyNIP']->value)
            $model->nip = str_replace("-", "", str_replace(" ", "", $settings['companyNIP']->value));
        $to = urlencode($to);
        $apiKey= "AIzaSyAPDBOEfgjSaEHEiC8Zx3BpV5lT_cIRiBQ";  
                $data = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$to."&key=".$apiKey);
                $data = json_decode($data, true);
                if ($data['status']=="OK")
                {
                    if (isset($data['results'][0]))
                    {
                       $r = $data['results'][0];
                       $model->latitude = $r['geometry']['location']['lat'];
                       $model->longitude = $r['geometry']['location']['lng'];
                    }
                }
        $model->save();
        $users = User::find()->where(['active'=>1])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->all();
        $users = User::find()->where(['active'=>1])->andWhere(['role'=>100])->all();
        $ua = "";
        foreach ($users as $u)
        {
            $ua .=$u->id.";";
        }
        $set = \common\models\Settings::find()->where(['key'=>'crossRentalUsers'])->one();
        if (!$set)
        {
            $set = new \common\models\Settings();
            $set->active = 1;
            $set->type = "string";
            $set->section = "main";
            $set->key = 'crossRentalUsers';
        }
        $set->value = $ua;
        $set->save();
        $cl = new CompanyLog;
        $cl->company_id = $model->code;
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $cl->datetime = date('Y-m-d H:i:s');
        $cl->gears = $gears;
        $cl->events = $events;
        $cl->rents = $rents;
        $cl->users = $users;
        $cl->save();
        $statuts = ArrayHelper::map(\common\models\GearServiceStatut::find()->where(['type'=>[1,2,3]])->asArray()->all(), 'id', 'id');
            $services = \common\models\GearService::find()->where(['status'=>$statuts])->andWhere(['warehouse_from'=>null])->all();
            $serwis = \common\models\Warehouse::find()->where(['type'=>2])->one();
            $warehouse  = \common\models\Warehouse::find()->where(['type'=>1])->orderBy(['position'=>SORT_ASC])->one();
            foreach ($services as $service)
            {
                if ($service->gearItem->gear->no_items){
                    $w = \common\models\WarehouseQuantity::find()->where(['gear_id'=>$service->gearItem->gear_id])->andWhere(['>=', 'quantity', $service->quantity])->one();
                    $service->warehouse_from = $w->warehouse_id;

                }
                else{
                    if ($service->gearItem->warehouse_id)
                        $service->warehouse_from = $service->gearItem->warehouse_id;
                    else
                        $service->warehouse_from = $warehouse->id;
                }
                $service->save();

            }
       /* Yii::$app->db->createCommand("
            ALTER TABLE `packlist_outer_gear` ADD CONSTRAINT `pack_ogear2` FOREIGN KEY (`event_outer_gear`) REFERENCES `event_outer_gear`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
            ALTER TABLE `gear_group` ADD `code` VARCHAR(255) NULL AFTER `rfid_code`;
        ALTER TABLE `gear` ADD `code` VARCHAR(255) NULL AFTER `volume_case`;
        ALTER TABLE `offer_role` ADD `vat_rate` DECIMAL(10,1) NULL DEFAULT '23' AFTER `unit`;
        ALTER TABLE `offer_vehicle` ADD `vat_rate` DECIMAL(10,1) NULL DEFAULT '23' AFTER `description`;
        ALTER TABLE `packlist` ADD `blocked` INT NULL AFTER `main`;
        ALTER TABLE `todolist` ADD `position` INT NULL AFTER `update_time`;")->query();
        //Yii::$app->db->createCommand('ALTER TABLE packlist ADD blocked INT NULL DEFAULT "0" AFTER main;')->query();
        Yii::$app->db->createCommand('update gear_category set removable_all=1 where id>1;')->query(); */
        
        
        exit;
    }

    public function actionOrder($hash)
    {
        $this->layout = 'client-panel';
        $model = Order::find()->where(['hash'=>$hash])->one();
        $providerEventOuterGear = new \yii\data\ArrayDataProvider([
            'allModels' => $model->eventOuterGears,
        ]);
        return $this->render('order', [
            'model' => $model,
            'providerEventOuterGear' => $providerEventOuterGear,
        ]);
    }

    public function actionConfirm($hash)
    {
        $model = Order::find()->where(['hash'=>$hash])->one();
        $model->confirm = 1;
        foreach ($model->eventOuterGears as $gear)
        {
            $gear->confirm = 1;
            $gear->save();
        }
        $model->save();
        return $this->redirect(['order', 'hash' => $model->hash]);       
    }

    public function actionConfirmone($event_id, $outer_gear_id, $hash)
    {
        $order = Order::find()->where(['hash'=>$hash])->one();
        $model = EventOuterGear::find()->where(['event_id'=>$event_id, 'outer_gear_id'=>$outer_gear_id, 'order_id'=>$order->id])->one();
        $model->confirm = 1;
        $model->save();
        return $this->redirect(['order', 'hash' => $order->hash]);         
    }

    public function actionIndex($dialog=null)
    {
        $this->layout = 'main-panel';
        $dashboard = new Dashboard();


        return $this->render('index', [
            'dashboard' => $dashboard,
            'dialog' => $dialog
        ]);
    }

    public function actionFirstUse($dialog=null)
    {
        $this->layout = 'main-panel';
        $firstUse = new FirstUse;
        $firstUse->init();
        return $this->render('first-use', [
            'model' => $firstUse
        ]);
    }

    public function actionCalendarProdukcja()
    {
        $this->layout = 'main-panel';
        $model = new \common\models\form\Calendar2Search();
        $model->load(Yii::$app->request->post());
        $model->type = [2];
        $events2 = $model->getEventsOnCalendar();
        $events = $model->getEventsNotOnCalendar();
        $tasks = $model->getTasksOnCalendar();
        $projects = Event::getEventByProject($events);
        $eventsArray = [];
        $colors = [2=>Yii::$app->settings->get('main.produkcjaColor'), 3=>Yii::$app->settings->get('main.biuroColor'), 4=>Yii::$app->settings->get('main.grafikaColor'), 5=>Yii::$app->settings->get('main.magazynColor')];
        foreach ($events2 as $event)
        {
            $eventsArray[] = $event->prepareForCalendar();
        }
        foreach ($tasks as $event)
        {
            $eventsArray[] = $event->prepareForCalendar();
        }
        return $this->render('calendar-produkcja-group', ['events'=>$events, 'eventsArray'=>$eventsArray, 'colors'=>$colors, 'model'=>$model, 'projects'=>$projects]);
    }

    public function actionCalendarWydruki()
    {
        $this->layout = 'main-panel';
        $model = new \common\models\form\Calendar2Search();
        $model->load(Yii::$app->request->post());
        $model->type = [4];
        $events2 = $model->getEventsOnCalendar();
        $events = $model->getEventsNotOnCalendar();
        $tasks = $model->getTasksOnCalendar();
        $projects = Event::getEventByProject($events);
        $eventsArray = [];
        $colors = [2=>Yii::$app->settings->get('main.produkcjaColor'), 3=>Yii::$app->settings->get('main.biuroColor'), 4=>Yii::$app->settings->get('main.grafikaColor'), 5=>Yii::$app->settings->get('main.magazynColor')];
        foreach ($events2 as $event)
        {
            $eventsArray[] = $event->prepareForCalendar();
        }
        foreach ($tasks as $event)
        {
            $eventsArray[] = $event->prepareForCalendar();
        }
        return $this->render('calendar-produkcja', ['events'=>$events, 'eventsArray'=>$eventsArray, 'colors'=>$colors, 'model'=>$model, 'projects'=>$projects]);
    }


    public function actionCalendarPlan()
    {
        $this->layout = 'main-panel';
            $model = new \common\models\form\Calendar2Search();
        $model->load(Yii::$app->request->post());
        $colors = [2=>Yii::$app->settings->get('main.produkcjaColor'), 3=>Yii::$app->settings->get('main.biuroColor'), 4=>Yii::$app->settings->get('main.grafikaColor'), 5=>Yii::$app->settings->get('main.magazynColor')];
        return $this->render('calendar-plan', ['colors'=>$colors, 'model'=>$model]);
    }

    public function actionGetForCalendar($type = 2)
    {
        
        $d = mktime(0,0,0, date('n'), date("d")-10, date('Y'));
        $start = date("Y-m-d H:i:s", $d);
        $d2=mktime(0,0,0, date('n')+2, date("d"), date('Y'));  
        $end = date("Y-m-d H:i:s", $d2);
        $model = new \common\models\form\Calendar2Search();
        $model->type = [$type];
        $events2 = $model->getEventsOnCalendar($start, $end);
        $events = $model->getEventsNotOnCalendar($start, $end);
        $projects = Event::getEventByProject(array_merge($events, $events2));
        $colors = [2=>Yii::$app->settings->get('main.produkcjaColor'), 3=>Yii::$app->settings->get('main.biuroColor'), 4=>Yii::$app->settings->get('main.grafikaColor'), 5=>Yii::$app->settings->get('main.magazynColor')];
        return $this->renderAjax('calendar-produkcja-sidebar', ['colors'=>$colors, 'model'=>$model, 'projects'=>$projects, 'events'=>$events, 'events2'=>$events2,]);
    }

    public function actionCalendar2(){
        $this->layout = 'main-panel';
        $model = new \common\models\form\Calendar2Search();
        $projects = Project::find()->all();
        if ($model->load(Yii::$app->request->post()))
        {
            setcookie('calendar2.type', json_encode($model->type));
            setcookie('calendar2.status', json_encode($model->status));
            setcookie('calendar2.users', json_encode($model->users));
        }else{
            if (isset($_COOKIE["calendar2.type"]))
            {
                $model->type = json_decode($_COOKIE["calendar2.type"]);
            }
            if (isset($_COOKIE["calendar2.status"]))
            {
                $model->type = json_decode($_COOKIE["calendar2.status"]);
            }
            if (isset($_COOKIE["calendar2.users"]))
            {
                $model->type = json_decode($_COOKIE["calendar2.users"]);
            }
        }
        
        $events2 = $model->getEventsOnCalendar();
        $events = $model->getEventsNotOnCalendar();
        
        $colors = [2=>Yii::$app->settings->get('main.produkcjaColor'), 3=>Yii::$app->settings->get('main.biuroColor'), 4=>Yii::$app->settings->get('main.grafikaColor'), 5=>Yii::$app->settings->get('main.magazynColor'), 6=>"#990000", 7=>"#009900"];
        $eventsArray = "[";      
        foreach ($events2 as $event)
        {
            $description = $event->name."<br/>".Yii::t('app', 'Autor: ').$event->creator->displayLabel."<br/>".Yii::t('app', 'Przypisani:');
            $users = "[";
            foreach ($event->eventUsers as $eu)
            {
                if ($users != "[")
                    $users .=", ";
                $users .=$eu->user->getInitials();
                $description .= $eu->user->displayLabel.", ";
            }
            $description .= "<br/>";
            foreach ($event->requests as $request){ 
                $description .=$request->company->name." [".$request->id."] ";
            }
             $description .= "<br/>".$event->description;
            $users .="]";
            $att = count($event->attachments);
            $notes = count($event->customerNotes);
            /*$tmp = "{title: '".$event->name."', id:".$event->id.", start:'".substr($event->event_start, 0, 10)."T".substr($event->event_start, 11, 8)."', end:'".substr($event->event_end, 0, 10)."T".substr($event->event_end, 11, 8)."', color:'".$color."', borderColor:'".$event->eventStatut->color."'},";
            */
            $whole = "false";
            if ((substr($event->event_end, 11, 8)==substr($event->event_start, 11, 8))&&(substr($event->event_start, 11, 8)=="00:00:00"))
            {
                $whole = "true";
            }
            $tmp = "{title: '".$event->name."',users:'".$users."', id:".$event->id.", start:'".substr($event->event_start, 0, 10)."T".substr($event->event_start, 11, 8)."', end:'".substr($event->event_end, 0, 10)."T".substr($event->event_end, 11, 8)."', className:'typ-".$event->type." status-".$event->status."', allDay:".$whole.", notes:".$notes.", files:".$att.", description:'".$description."'},";
            $eventsArray .= $tmp;
        }
        $eventsArray .="]";
        return $this->render('calendar2', ['projects'=>$projects, 'events'=>$events, 'eventsArray'=>$eventsArray, 'colors'=>$colors, 'model'=>$model]);
    }

    public function actionCalendar($year = null, $month = null) {
    	if ($year === null) {
    		$year = date('Y');
	    }
	    if ($month === null) {
    		$month = date('m');
	    }

	    $nextMont = $month+1;
	    $nextYear = $year;
	    $prevMonth = $month-1;
	    $prevYear = $year;
	    if ($month == 12) {
	    	$nextMont = 1;
	    	$nextYear = $year+1;
	    }
	    if ($month == 1) {
	    	$prevMonth = 12;
	    	$prevYear = $year-1;
	    }

	    if (strlen($nextMont) === 1) {
	    	$nextMont = "0".$nextMont;
	    }
	    if (strlen($prevMonth) === 1) {
		    $prevMonth = "0".$prevMonth;
	    }

    	$this->layout = 'main-panel';
        return $this->render('calendar', [
        	'month' => $month,
	        'year' => $year,
	        'prevLink' => "?year=" . $prevYear . "&month=" . $prevMonth,
	        'nextLink' => "?year=" . $nextYear . "&month=" . $nextMont,
        ]);
    }

    public function actionStats($m=null, $y=null, $category_id=null)
    {
        $this->layout = 'main-panel';
        if ($m==null)
        {
            $m = date('m');
            $y = date('Y');
        }
        if ($category_id==null)
        {
            $category_id = 1;
        }
        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stat = new Stat();
        $stats = $stat->getStats($m, $y, $category_id);
        return $this->render('stats', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next, 'category_id'=>$category_id]);
    }

    public function actionLogin()
    {
        $this->layout = 'empty';
        $company = Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        Yii::$app->language = $company->language;
        if (!$company->active)
            return $this->redirect(['/site/not-active',]);

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->session->set('company',$company->type);
                    return $this->goBack();  


        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    public function actionNotActive()
    {
        $this->layout = 'empty';
        return $this->render('not-active');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionSendMail()
    {
       $this->layout = 'main-panel';
        $model = new \backend\models\SendMail();
        $model->type = 1;
        $model->priority = 1;
        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $user = Yii::$app->user->identity;
            $mail = \Yii::$app->mailer->compose('@app/views/site/mail', [
                'model' =>  $model            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>$user->email])
            ->setTo([Yii::$app->params['errorEmail']])
            ->setSubject($model->subject." [".\Yii::$app->params['companyID']."]");            
            if ($mail->send())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Email wysłany!'));
                //tworzymy wpis w bazie danych i wysyłamy maila zwrotnego
                $request = new Request;
                $request->name = $model->subject;
                $request->type = intval($model->type);
                $request->priority = intval($model->priority);
                if (\Yii::$app->params['companyID']=="admin")
                {
                    $request->company_id = $model->company;
                    $request->mail = $model->usermail;
                    $request->username = $model->username;
                }else{
                    $request->company_id = \Yii::$app->params['companyID'];
                    $request->mail = $user->email;
                    $request->user_id = $user->id;
                    $request->username = $user->displayLabel;
                }
                
                $request->text = $model->text;
                $request->link = $model->link;
                
                $request->status = 1;
                if ($request->save())
                {
                    $mail = \Yii::$app->mailer->compose('@app/views/site/mail-back', [
                    'model' =>  $request            ])
                        ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->params['mailingEmail']])
                        ->setTo([$user->email])
                        ->setSubject(Yii::t('app', 'Zgłoszenie przyjęte')." [ERR_".$request->id."]");
                    $mail->send();
                return $this->render('mail-sent', [
                        'model' => $request
                    ]);
                }
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Błąd!'));
                echo "Błąd!";
            }
            exit;
            return $this->redirect(['/',]);
        } 
        return $this->render('send-mail', [
            'model' => $model
        ]);
    }

    public function actionUpdatePassword()
    {
        $this->layout = 'empty';

        $model = new PasswordChange();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Hasło zaktualizowane'));
            $firstUse = new FirstUse;
            $firstUse->init();
            if (($firstUse->show)&&($model->username=="Administrator"))
            {
                return $this->redirect(['/site/first-use',]);
            }else{
                return $this->redirect('/site/index');
            }
        }

        return $this->render('update-password', [
            'model' => $model,
        ]);
    }

    public function actionForgetPassword()
    {
         $this->layout = 'empty';

        $model = new PasswordForget();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $error = 0;
            if ($model->getUser())
            {
                //losujemy hasło i wysyłamy maila
                $user = $model->getUser();
                $user->loadLinkedObjects();
                $token = '$N'.bin2hex(openssl_random_pseudo_bytes(8));
                $user->setPassword($token);
                $user->generateAuthKey();
                if (empty($user->last_vist) == true)
                {
                    $user->last_visit = null;
                }

                if ($user->save(false))
                {
                    $mail = \Yii::$app->mailer->compose('@app/views/site/mail-password', [
                        'model' =>  $user, 'token'=>$token])
                    ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->params['mailingEmail']])
                    ->setTo([$user->email])
                    ->setSubject(Yii::t('app', 'New Event Management - hasło tymczasowe'));            
                    if ($mail->send())
                    {
                    }
                }else{
                    $error = 1;
                }
            }else{
                $error = 2;
            }
            return $this->render('password-changed', [
                'error' => $error,
            ]);
        }

        return $this->render('forget-password', [
            'model' => $model,
        ]);       
    }

    public function actionMaskCreator()
    {
        $this->layout = 'main-panel';
        $model = new MaskCreator();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $width = $model->width;
            $height = $model->height;
            $cols = $model->cols;
            $rows = $model->rows;
            $color = $model->color;
            $total_width = $width*$cols;
            $total_height = $height*$rows;
            // Create a 100*30 image
            $im = imagecreate($total_width, $total_height);

            // White background and blue text
            if ($color == 1)
                $bg = imagecolorallocate($im, 255, 255, 255);
            if ($color == 2)
                $bg = imagecolorallocate($im, 255, 0, 0);
            if ($color == 3)
                $bg = imagecolorallocate($im, 0, 0, 255);
            if ($color == 4)
                $bg = imagecolorallocate($im, 0, 255, 0);

            $textcolor = imagecolorallocate($im, 140, 140, 140);

            // Write the string at the top left
            //imagestring($im, 5, 30, 30, $initial, $textcolor);
            $font = $_SERVER['DOCUMENT_ROOT'] . '/fonts/Roboto-Black.ttf';

            for ($i=1; $i<=$cols; $i++)
            {
                for ($j=1; $j<=$rows; $j++)
                {
                    $initial = $i."x".$j;
                    imagettftext($im, 20, 0, ($i*$width)-$width+($width-60)/2, ($j*$height)-$height+($height+30)/2, $textcolor, $font, $initial);
                    imagerectangle($im, ($i*$width)-$width, ($j*$height)-$height, ($i*$width)-1, ($j*$height)-1, $textcolor);
                }
            }
            
            // Output the image
            // Create a 100*30 image
            //header('Content-type: image/png');
            $save = Yii::getAlias('@uploadroot' . '/')."mask_creator.png";
            imagepng($im, $save);
            imagedestroy($im);
            return Yii::$app->response->sendFile(Yii::getAlias('@uploadroot' . '/')."mask_creator.png");
            //imagepng($im);
            
            exit;
        }else{
            return $this->render('mask-creator', [
            'model' => $model,
        ]);
        }
    }
}
