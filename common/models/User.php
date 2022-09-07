<?php
namespace common\models;
use common\helpers\Url;

use common\helpers\ArrayHelper;
use DateInterval;
use DateTime;
use yii\base\Security;
use yii\helpers\Html;
use common\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;
use yii\db\Expression;
use common\models\base\User as BaseUser;
use Zend\Validator\Date;
use yii\data\ActiveDataProvider;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property string $first_name
 * @property string $last_name 
 * @property string $password write-only password
 */
class User extends BaseUser implements IdentityInterface
{
    const RBAC_ADMINISTARTOR = 'administrator';
    const RBAC_SUPERADMIN = 'SiteAdministrator';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    const ROLE_CUSTOMER = 5;

    const ACTIVE_IS = 1;
    const ACTIVE_IS_NOT = 0;

    const ROLE_USER = 10;
    const ROLE_PROJECT_MANAGER = 30;
    const ROLE_ADMIN = 50;
    const ROLE_SUPERADMIN = 100;

    public static $rolesMap = [
        self::ROLE_CUSTOMER => 'customer',
        self::ROLE_PROJECT_MANAGER => 'projectManager',
        self::ROLE_ADMIN => 'administrator',
        self::ROLE_SUPERADMIN => 'SiteAdministrator',
        self::ROLE_USER => 'user',
    ];

    const RATE_1H = 1;
    const RATE_8H = 8;
    const RATE_12H = 12;
    const RATE_24H = 24;
    const RATE_MONTH = 720;

    const TYPE_OUTSOURCER = 10;
    const TYPE_EMPLOYEE = 1;

    public $newPassword;
    public $send_password;
    public $skillIds;
    public $departmentIds;
    public $authAssigmentIds;


    public function checkAvability($start, $end, $event_id)
    {
        $e = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event_id])->andWhere(['user_id'=>$this->id])->andWhere(['<', 'start_time', $end])->andWhere(['>', 'end_time', $start])->count();
        if ($e)
            return false;
        else{
            $hours = Vacation::find()->where(['user_id'=>$this->id])->andWhere(['>', 'end_date', $start])->andWhere(['<', 'start_date', $end])->andWhere(['status'=>10])->count();
            if ($hours)
                return false;
        }
        return true;
    }

    public function getEventsConflictedArray($event, $schedule)
    {
            $array = [];
            $array2 = [];
        if ($schedule)
        {
            $array2[] = ['id'=>'a', 'title'=>$schedule->event->name];
            $tmp = ['title'=>"[".$schedule->name."] ".$schedule->event->name, 'id'=>$schedule->id, 'resourceId'=>'a', 'start'=>substr($schedule->start_time, 0, 10)."T".substr($schedule->start_time, 11, 8), 'end'=>substr($schedule->end_time, 0, 10)."T".substr($schedule->end_time, 11, 8), 'backgroundColor'=>"#111"] ;
            $array[] = $tmp;
        }
        $start = $event->getTimeStart();
        $end = $event->getTimeEnd();
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-10 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+10 days" ) );
            $hours = EventUserPlannedWrokingTime::find()->where(['user_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->orderBy(['event_id'=>SORT_DESC])->all();
            $ids = \common\helpers\ArrayHelper::map(EventUserPlannedWrokingTime::find()->where(['user_id'=>$this->id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->orderBy(['event_id'=>SORT_DESC])->asArray()->all(), 'event_id', 'event_id');
            $events = Event::find()->where(['id'=>$ids])->asArray()->all();

            foreach ($events as $e)
            {
                $array2[] = ['id'=>$e['id'], 'title'=>$e['name']];
            }

            $color = "#1ab394";
            $i = 0;
        $event_id = 0;
        foreach ($hours as $hour)
        {
            $t = "";
            if (isset($hour->eventSchedule->name))
            {
                if ($hour->eventSchedule->prefix)
                    $t = "[".$hour->eventSchedule->prefix."] ";
                else
                    $t = "[".mb_substr($hour->eventSchedule->name, 0, 3)."] ";
            if ($hour->eventSchedule->color)
                    $color = $hour->eventSchedule->color;
                
            }
            $tmp = ['title'=>$t.$hour->event->name, 'id'=>$hour->id, 'resourceId'=>$hour->event_id, 'start'=>substr($hour->start_time, 0, 10)."T".substr($hour->start_time, 11, 8), 'end'=>substr($hour->end_time, 0, 10)."T".substr($hour->end_time, 11, 8), 'backgroundColor'=>$color] ;
            $array[] = $tmp;

        }  
            $hours = Vacation::find()->where(['user_id'=>$this->id])->andWhere(['>', 'end_date', $checkstart])->andWhere(['<', 'start_date', $checkend])->all();
        foreach ($hours as $hour)
        {
            $color = "#f8ac59";
            if ($hour->status)
                $color = "#ed5565";
            $tmp = ['title'=>Yii::t('app', 'Urlop'), 'id'=>$hour->id, 'resourceId'=>'b', 'start'=>substr($hour->start_date, 0, 10)."T00:00:00", 'end'=>substr($hour->end_date, 0, 10)."T23:59:59", 'backgroundColor'=>$color] ;
            $array[] = $tmp;

        }  
        if ($hours)
            $array2[] = ["id"=>"b", "title"=>Yii::t('app', 'Urlopy')];
        return ['events'=>$array, 'res'=>$array2];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'create_time',
            'updatedAtAttribute' => 'update_time',
            'value' => new Expression('NOW()'),
        ];

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'departmentIds',
                'skillIds',
            ],
            'relations' => [
                'departments',
                'skills',
            ],
            'modelClasses' => [
                'common\models\Department',
                'common\models\Skill',
            ],
        ];
        $behaviors['workingTime'] = [
            'class' => \common\behaviors\WorkingTimeBehavior::className(),
            'connectionClassName' => EventUser::className(),
            'itemIdAttribute' => 'user_id',
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $roles = array_keys(self::getRoleList(true));
        $rules = [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],

            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => $roles],
            [['newPassword'], 'string', 'min' => 6, 'max' => 255],
            [['email'], 'email'],
            [['username', 'email'], 'unique'],
            [['first_name', 'last_name', 'city'], 'filter', 'filter' => 'trim'],
            [['first_name', 'last_name'], 'required'],
            [['username', 'first_name', 'last_name'], 'string', 'min' => 2, 'max' => 255],
            [['departmentIds', 'skillIds'], 'each', 'rule' => ['integer']],

        ];
        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels()
    {
        $labels = [
            'newPassword' => Yii::t('app', 'Nowe hasło'),
            'first_name' => Yii::t('app', 'Imię'),
            'last_name' => Yii::t('app', 'Nazwisko'),
            'role' => Yii::t('app', 'Rola'),
            'city' => Yii::t('app', 'Miasto Zamieszkania'),
            'email' => Yii::t('app', 'Adres e-mail'),
            'username' => Yii::t('app', 'Nazwa użytkownika'),
            'skillIds' => Yii::t('app', 'Umiejętności'),
            'departmentIds' => Yii::t('app', 'Oddziały'),
            'authAssigmentIds'=>Yii::t('app', 'Grupa uprawnień'),
            'send_password' =>Yii::t('app', 'Wyślij hasło')
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function backendFindByUsername($username)
    {
        $model = static::findByUsername($username);
        if ($model !== null && $model->role >= 0) {
            return $model;
        }
        return false;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }




    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
		if (Yii::$app->security->validatePassword($password, $this->password_hash)) {
			$this->generateLoginToken();
			return true;
		}
        return false;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getList($roles = null, $visibleInOffer = null)
    {
        $query = self::find()->where(['active' => 1]);

        $query->filterWhere([
            'role' => $roles,
	        'visible_in_offer' => $visibleInOffer,
        ]);

        $list = [];

        $models = $query->orderBy('last_name ASC')->all();
        foreach ($models as $model) {
            if ($model->active)
                $list[$model->id] = $model->last_name." ".$model->first_name;

        }

        return $list;
    }

    public static function getEmployeeRoles()
    {
        return [self::ROLE_USER, self::ROLE_PROJECT_MANAGER, self::ROLE_ADMIN];
    }

    public static function getEmployeeList()
    {
        $list = static::getList(self::getEmployeeRoles());
        return $list;
    }

    public static function getWorkerList()
    {

    }

    public static function getRoleList($all = false)
    {
        $list = [
            self::ROLE_USER => 'User',
          //  self::ROLE_CUSTOMER => 'Klient',
            self::ROLE_PROJECT_MANAGER => 'Project Manager',
         //   self::ROLE_ADMIN => 'Adminitstrator',
            self::ROLE_SUPERADMIN => 'SiteAdmin'
        ];

        if ($all == false) {
            $role = Yii::$app->user->identity->role;
            foreach ($list as $key => $name) {
                if ($key >= $role) {
                    unset($list[$key]);
                }
            }
        }

        return $list;
    }

    public function getRoleName()
    {
        $list = self::getRoleList(true);
        $index = $this->role;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function getStatusList()
    {
        $list = [
            self::STATUS_ACTIVE => Yii::t('app', 'Aktywny'),
            self::STATUS_INACTIVE => Yii::t('app', 'Nieaktywny'),
        ];
        return $list;
    }

    public static function getActiveList()
    {
        $list = [
            self::ACTIVE_IS => Yii::t('app', 'Aktywny'),
            self::ACTIVE_IS_NOT => Yii::t('app', 'Nieaktywny'),
        ];
        return $list;
    }


    public function getDisplayLabel()
    {
        return $this->first_name." ".$this->last_name;
    }

    public function getName()
    {
        return $this->first_name." ".$this->last_name;
    }

    public static function getAvaibleGroupedList($eventId)
    {
        $assigned = EventUser::find()
            ->select(['user_id'])
            ->where(['event_id' => $eventId])
            ->asArray()
            ->column();

        $models = static::find()
            ->where(['not', ['id' => $assigned]])
            ->andWhere(['<', 'role', self::ROLE_SUPERADMIN])
            ->all();

        $list = ArrayHelper::map($models, 'id', 'displayLabel');
        return $list;
    }

    public function getInitials()
    {
        $initials = mb_strtoupper(mb_substr($this->first_name, 0, 1) . '.' . mb_substr($this->last_name, 0, 1) . '.');
        return $initials;
    }

    public function getPhotoUrl()
    {
        $url = $this->loadFileUrl('photo', '@uploads/user/');
        return $url;
    }


    public function getFilePath()
    {
            return Yii::getAlias('@uploadroot/user/'.$this->photo);
    }

    public function getUserPhoto($class=""){
        if(($this->photo)&&($this->photo!="")) {
            return Html::img($this->getPhotoUrl(), array('class'=>$class));
        }else{
            return Html::img('/admin/site/generate-photo?initial='.$this->getInitials(), array('class'=>$class));
        }
        
    }

    public function getUserPhotoUrl()
    {
        if(!$this->photo) {
            return '/admin/site/generate-photo?initial='.$this->getInitials();
        }
        return $this->getPhotoUrl();
    }


    public static function getRateList()
    {
        $list = [
            self::RATE_1H => Yii::t('app', '1h'),
            self::RATE_8H => Yii::t('app', '8h'),
            self::RATE_12H => Yii::t('app', '12h'),
            self::RATE_24H => Yii::t('app', '24h'),
            self::RATE_MONTH => Yii::t('app', 'Miesięczna'),
        ];
        return $list;
    }

    public static function getVisibleStatusList()
    {
        $list = [
            1 => Yii::t('app', 'Widoczny'),
            0 => Yii::t('app', 'Niewidoczny'),
        ];
        return $list;
    }

    public function getRateName()
    {
        $list = self::getRateList();
        $index = $this->rate_type;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public static function getTypeList()
    {
        $list = [
            self::TYPE_EMPLOYEE => Yii::t('app', 'Pracownik'),
            self::TYPE_OUTSOURCER => Yii::t('app', 'Osoba z zewnątrz'),
        ];
        return $list;
    }

    public function getTypeName()
    {
        $list = self::getTypeList();
        $index = $this->type;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public function getDepartmentList($separator = ', ')
    {
        $list = ArrayHelper::map($this->departments, 'id', 'name');
        return implode($separator, $list);
    }

    public function getSkillList($separator = ', ')
    {
        $list = ArrayHelper::map($this->skills, 'id', 'name');
        return implode($separator, $list);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['role']) == true && $this->role != $changedAttributes['role']) {
            /*$auth = Yii::$app->authManager;
            $auth->revokeAll($this->id);
            $role = ArrayHelper::getValue(self::$rolesMap, $this->role, false);
            if ($role !== false && $role != 'user') {
                $r = $auth->getRole($role);
                $auth->assign($r, $this->id);
            }
    */
        }

        if ($insert==true)
        {
            $this->sendNotifications();
        }
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
        $superusers = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count();
        $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $superusers_paid = $superuser->superusers_paid;
        if (($superusers>$superusers_paid )&&($this->isSuperUser()))
        {
            //wysyłamy maila do supportu o zwiększenie liczby płatnych supruserów.
            if ((!isset($changedAttributes['last_visit']))&&(!isset($changedAttributes['login_token'])))
            {

                            $sent = Yii::$app->mailer->compose('mailNotification', [
                'title'=>$superuser->name." - ".Yii::t('app', 'Zwiększenie limitu superuser'),
                'content'=>Yii::t('app', 'Informujemy, że dodanie kolejnego konta spowodowało zwiększenie limitu kont superuser a co za tym idzie zwiększeniem opłaty wg cennika.<br/> Poprzedni limit: ').$superusers_paid."<br/>".Yii::t('app', ' Obdecny limit:').$superusers
                    ])
                ->setTo(['support@newsystems.pl', Yii::$app->user->identity->username])
                ->setBcc(['marqiz87@gmail.com'])
                    ->setFrom([Yii::$app->params['mailingEmail'] => "New Event Management"])
                ->setSubject(Yii::t('app', 'Prośba o zwiększenie limitu kont superuser'))
                ->send();
            }

        }
    }

    public function sendNotifications()
    {
        $messageMap = [
            self::ROLE_CUSTOMER => 'customerCreate',
            self::ROLE_USER => 'userCreate',
            self::ROLE_ADMIN => 'userCreate',
            self::ROLE_PROJECT_MANAGER => 'userCreate',
        ];


        $message = ArrayHelper::getValue($messageMap, $this->role, null);
        if ($message != null)
        {
            $notification = Notification::getByName($message);
            if ($notification !== null)
            {
                $notification->sendMail($this->email, Yii::t('app', 'Konto zostało utworzone'));
            }

        }
    }

    public function isAvailableInRange($start, $end) {
        $start = new DateTime($start);
        $end = new DateTime($end);

        foreach (EventUserPlannedWrokingTime::findAll(['user_id' => $this->id]) as $time) {
            $work_start = new DateTime($time->start_time);
            $work_end = new DateTime($time->end_time);

            if ($start >= $work_start && $start <= $work_end && $end > $work_end) {
                $start = $work_end;
            }

            if ($end >= $work_start && $end <= $work_end && $start < $work_start) {
                $end = $work_start;
            }

            if ($start >= $work_start && $start <= $work_end) {
                if ($end >= $work_start && $end <= $work_end) {
                    return 0;
                }
            }
        }

        return 1;
    }

    public function isAvailableInRangeWithoutThisRange($start, $end) {
        $start = new DateTime($start);
        $end = new DateTime($end);

        foreach (EventUserPlannedWrokingTime::find()->where(['user_id' => $this->id])->andWhere(
            ['or',
                ['<>', 'start_time', $start->format("Y-m-d H:i:s")],
                ['<>', 'end_time', $end->format("Y-m-d H:i:s")]
            ])->all() as $time) {

            $work_start = new DateTime($time->start_time);
            $work_end = new DateTime($time->end_time);

            if ($start >= $work_start && $start <= $work_end && $end > $work_end) {
                $start = $work_end;
            }

            if ($end >= $work_start && $end <= $work_end && $start < $work_start) {
                $end = $work_start;
            }

            if ($start >= $work_start && $start <= $work_end) {
                if ($end >= $work_start && $end <= $work_end) {
                    return 0;
                }
            }
        }

        return 1;
    }

    public function delete() {
        $this->active = 0;
        foreach ($this->notificationSmses as $sms) {
            $sms->delete();
        }
        foreach ($this->notificationMails as $mail) {
            $mail->delete();
        }
        return $this->save();
    }

    public function update($runValidation = true, $attributeNames = null) {
        parent::update($runValidation = true, $attributeNames = null);
        if ($this->phone && $this->active) {
            foreach ($this->notificationSmses as $sms) {
                if (new DateTime($sms->sending_time) > new DateTime()) {
                    $sms->updatePhoneNumber();
                }
            }
        }
        if ($this->email) {
            foreach ($this->notificationMails as $mail) {
                if ($mail->sent == 0) {
                    $mail->updateMailAddress($this->email);
                }
            }
        }
    }

    public function getPlaceholderMap()
    {
        $map = [
            'username' => $this->username,
            'imie' => $this->first_name,
            'nazwisko' => $this->last_name,
            'tel' => $this->phone,
            'mail' => $this->email,
            'link' => Html::a('link', Url::to(['/site/login'], true)),
            'password'=>$this->newPassword
        ];

        return $map;
    }

    public function getTasksQuery() {
        $query = $this->getTasks();
        return $query->orderBy(['end_time'=>SORT_ASC]);
    }

    public function getChecklist() {
        return Checklist::find()->where(['user_id'=>$this->id])->orderBy(['priority'=>SORT_ASC, 'id'=>SORT_DESC])->all();
    }

    public function getAssignedMeetings()
    {
        $query = $this->getMeetings0();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedVacations()
    {
        $query = $this->getVacations();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function eventsIn12HPeriod($mainEvent) {
        $events = [];
        $start = new DateTime($mainEvent->getTimeStart());
        $end = new DateTime($mainEvent->getTimeEnd());
        $start1 = clone($start)->sub(new DateInterval('PT12H'));
        $end1 = clone($end)->add(new DateInterval('PT24H'));
        $eventsy = EventUserPlannedWrokingTime::find()->where(['user_id'=>$this->id])->andWhere(['<', 'start_time', $end1->format("Y-m-d H:i:s")])->andWhere(['>', 'end_time', $start1->format("Y-m-d H:i:s")])->all();

        foreach ($eventsy as $event) {
            if ($event->event_id == $mainEvent->id) {
                continue;
            }
            $work_start = new DateTime($event->start_time);
            $work_end = new DateTime($event->end_time);

            if (Event::datesAreOverlaping($start, $end, $work_start, $work_end)) {
                $events[] = $event->event;
                continue;
            }

            $test1 = clone($work_start)->add(new DateInterval('PT12H')) ;
            $test2 = clone($work_start)->sub(new DateInterval('PT24H'));
            $test3 = clone($work_end)->sub(new DateInterval('PT12H'));
            $test4 = clone($work_end)->add(new DateInterval('PT24H'));

            if ($test2 <= $start && $test1 >= $start) {
                $events[] = $event->event;;
                continue;
            }
            if ($test4 <= $start && $test3 >= $start) {
                $events[] = $event->event;
                continue;
            }
            if ($test2 <= $end && $test1 >= $end) {
                $events[] = $event->event;
                continue;
            }
            if ($test4 <= $end && $test3 >= $end) {
                $events[] = $event->event;
                continue;
            }
        }
        return $events;
    }

    public function overlapingEvents(Event $event) {
        $events = [];
        $start = new DateTime($event->getTimeStart());
        $end = new DateTime($event->getTimeEnd());
        $eventsy = EventUserPlannedWrokingTime::find()->where(['user_id'=>$this->id])->andWhere(['<', 'start_time', $end->format("Y-m-d H:i:s")])->andWhere(['>', 'end_time', $start->format("Y-m-d H:i:s")])->all();
        foreach ($eventsy as $time) {
            if ($time->event_id != $event->id) {
                if (Event::datesAreOverlaping($start, $end, new DateTime($time->start_time), new DateTime($time->end_time))) {
                    $events[] = Event::findOne($time->event_id);
                }
            }
        }
        return $events;
    }
    public function overlapingVacations(Event $event) {
        $vacations = [
            'accepted' => [],
            'planned' => []
        ];
        $start = new DateTime($event->getTimeStart());
        $end = new DateTime($event->getTimeEnd());
        foreach (Vacation::find()->where(['user_id' => $this->id])->andWhere(['<', 'start_date', $end->format("Y-m-d H:i:s")])->andWhere(['>', 'end_date', $start->format("Y-m-d H:i:s")])->all() as $time) {
            $vacation_start = new DateTime($time->start_date . " 00:00");
            $vacation_end = new DateTime($time->end_date . " 23:59");
            if (Event::datesAreOverlaping($start, $end, $vacation_start, $vacation_end)) {
                if ($time->status == Vacation::STATUS_NEW) {
                    $vacations['planned'][] = $time;
                }
                else if ($time->status == Vacation::STATUS_ACCEPTED) {
                    $vacations['accepted'][] = $time;
                }
            }
        }
        return $vacations;
    }

    public static function findByLoginToken($token) {
    	return self::findOne(['login_token' => $token]);
    }

    public function generateLoginToken() {
    	$generator = new Security();
    	$token = $generator->generateRandomString(255);
    	while (self::findByLoginToken($token) !== null) {
		    $token = $generator->generateRandomString(255);
	    }
	    $this->login_token = $token;
    	$this->save();
    }

    public function getAllTaskIds()
    {
        $task_ids = ArrayHelper::map(UserTask::find()->where(['user_id'=>$this->id])->asArray()->all(), 'task_id', 'task_id');
        $query = new Query;
        $query->select('task.id')->from('task_role')->join('INNER JOIN', 'task', 'task.id=task_role.task_id')->join('INNER JOIN', 'event_user', 'task.event_id=event_user.event_id')->join('INNER JOIN', 'event_user_role', 'event_user_role.event_user_id=event_user.id and event_user_role.user_event_role_id = task_role.user_event_role_id')->where(['event_user.user_id'=>$this->id])->all();
        $command = $query->createCommand();
        $rows = ArrayHelper::map($command->queryAll(), 'id', 'id');
        $all = $rows+$task_ids;
        if ($all)
            return $all;
        else
            return [0];
    }
    public function getDoneTaskCount()
    {
        $task_ids = $this->getAllTaskIds(); 
        return TaskDone::find()->where(['IN', 'task_id', $task_ids])->andWhere(['user_id'=>$this->id])->count();
    }

    public function getNotDoneCount()
    {
        $task_ids = $this->getAllTaskIds();
        $task_status_not = ArrayHelper::map(Task::find()->where(['in', 'id', $task_ids])->andWhere(['OR', ['<>', 'status', 10], ['is', 'status', null]])->asArray()->all(), 'id', 'id');
        $task_status_yes = TaskDone::find()->where(['IN', 'task_id', $task_status_not])->andWhere(['user_id'=>$this->id])->count();
        return count($task_status_not)-$task_status_yes;
    }

    public function getAfterTimeDoneCount()
    {
        $task_ids = $this->getAllTaskIds();
        $task_status_not = ArrayHelper::map(Task::find()->where(['in', 'id', $task_ids])->andWhere(['OR', ['<>', 'status', 10], ['is', 'status', null]])->andWhere(['<', 'datetime', date('Y-m-d')])->asArray()->all(), 'id', 'id');
        $task_status_yes = TaskDone::find()->where(['IN', 'task_id', $task_status_not])->andWhere(['user_id'=>$this->id])->count();
        return count($task_status_not)-$task_status_yes;
    }

    public function createProvisions()
    {
        $sections = GearCategory::getMainList();
        $sections[] = Yii::t('app', 'Obsługa');
        $sections[] = Yii::t('app', 'Transport');
        $sections[] = Yii::t('app', 'Inne');
        UserProvision::deleteAll(['NOT IN', 'section', $sections]);
        foreach ($sections as $s)
        {
            $up = UserProvision::findOne(['section'=>$s, 'user_id'=>$this->id]);
            if (!$up)
            {
                        $up = new UserProvision();
                        $up->user_id = $this->id;
                        $up->section = $s;
                        $up->value = 0;
                        $up->type = 1;
                        $up->save();
            }
        }
        return true;
    }

    public function getEventProvisions($year, $month)
    {
        $events = Event::find()->where(['MONTH(event_start)'=>$month, 'YEAR(event_start)'=>$year])->orWhere(['MONTH(montage_start)'=>$month, 'YEAR(montage_start)'=>$year])->orWhere(['MONTH(packing_start)'=>$month, 'YEAR(packing_start)'=>$year])->orWhere(['MONTH(disassembly_start)'=>$month, 'YEAR(disassembly_start)'=>$year])->orWhere(['MONTH(montage_end)'=>$month, 'YEAR(montage_end)'=>$year])->orWhere(['MONTH(packing_end)'=>$month, 'YEAR(packing_end)'=>$year])->orWhere(['MONTH(disassembly_end)'=>$month, 'YEAR(disassembly_end)'=>$year])->orWhere(['MONTH(event_end)'=>$month, 'YEAR(event_end)'=>$year])->all();
        $sum = 0;
        $firstDayUTS = mktime (0, 0, 0, $month, 1, $year);
                $firstDay = date("Y-m-d H:i:s", $firstDayUTS);
                $lastDay = date("Y-m-t", strtotime($firstDay));
                $lastDay .=" 23:59:59";
        foreach($events as $e)
        {

            if (($e->getTimeEnd()>=$firstDay)&&($e->getTimeEnd()<=$lastDay))
            {
                if ((isset($e->eventStatut))&&($e->eventStatut->count_provision)){
                            
                    $s=   SettlementUser::find()->where(['user_id'=>$this->id, 'event_id'=>$e->id])->one();
                     if (!$s){
                        foreach ($e->getUserGProvision($this->id) as $p)
                        {
                            $sum += $p['value'];
                        }
                     }else{
                        if (($s->working_hours_data!='a:0:{}')||($s->addon_data!='a:0:{}'))
                        {
                        }else{
                            foreach ($e->getUserGProvision($this->id) as $p)
                        {
                            $sum += $p['value'];
                        }
                        }
                     }
                        
                }
            }

        }
        return $sum;
    }

    public function getExpensesFV($year, $month)
    {
        $ids = \common\helpers\ArrayHelper::map(\common\models\ExpenseUserPayment::find()->where(['user_id'=>$this->id, 'month'=>$month, 'year'=>$year])->asArray()->all(), 'expense_id', 'expense_id');
        return Expense::find()->where(['id'=>$ids])->all();
    }

    public function getEventProvisionsNon($year, $month)
    {
        $events = Event::find()->where(['MONTH(event_start)'=>$month, 'YEAR(event_start)'=>$year])->orWhere(['MONTH(montage_start)'=>$month, 'YEAR(montage_start)'=>$year])->orWhere(['MONTH(packing_start)'=>$month, 'YEAR(packing_start)'=>$year])->orWhere(['MONTH(disassembly_start)'=>$month, 'YEAR(disassembly_start)'=>$year])->orWhere(['MONTH(montage_end)'=>$month, 'YEAR(montage_end)'=>$year])->orWhere(['MONTH(packing_end)'=>$month, 'YEAR(packing_end)'=>$year])->orWhere(['MONTH(disassembly_end)'=>$month, 'YEAR(disassembly_end)'=>$year])->orWhere(['MONTH(event_end)'=>$month, 'YEAR(event_end)'=>$year])->all();
        $sum = 0;
        $firstDayUTS = mktime (0, 0, 0, $month, 1, $year);
                $firstDay = date("Y-m-d H:i:s", $firstDayUTS);
                $lastDay = date("Y-m-t", strtotime($firstDay));
                $lastDay .=" 23:59:59";
        foreach($events as $e)
        {

            if (($e->getTimeEnd()>=$firstDay)&&($e->getTimeEnd()<=$lastDay))
            {
                if ((isset($e->eventStatut))&&($e->eventStatut->count_provision)){
                            

                        
                }else{
                    $s=   SettlementUser::find()->where(['user_id'=>$this->id, 'event_id'=>$e->id])->one();
                     if (!$s){
                        foreach ($e->getUserGProvision($this->id) as $p)
                        {
                            $sum += $p['value'];
                        }
                     }else{
                        if (($s->working_hours_data!='a:0:{}')||($s->addon_data!='a:0:{}'))
                        {
                        }else{
                            foreach ($e->getUserGProvision($this->id) as $p)
                        {
                            $sum += $p['value'];
                        }
                        }
                     }
                }
            }

        }
        return $sum;
    }

    public function hasSectionProvisions()
    {
        return UserProvision::find()->where(['user_id'=>$this->id, 'event_type'=>2])->count();
    }

    public function getPayments($year, $month)
    {
        return UserPayment::find()->where(['user_id'=>$this->id, 'year'=>$year, 'month'=>$month])->asArray()->all();
    }
    public function getPaymentsSum($year, $month)
    {
        $payments = $this->getPayments($year, $month);
        $sum = 0;
        foreach ($payments as $p)
        {
            $sum +=$p['amount'];
        }
        return $sum;
    }

    public function isSuperUser($u=1)
    {
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>$u])->asArray()->all(), 'name', 'name');
        return AuthAssignment::find()->where(['item_name'=>$groups_super_user])->andWhere(['user_id'=>$this->id])->count();
         
    }

    public function getSuperUser($u=1)
    {
        $auth = ArrayHelper::map(AuthAssignment::find()->where(['user_id'=>$this->id])->asArray()->all(), 'item_name', 'item_name');

        $auth= \common\models\AuthItem::find()->where(['superuser'=>$u])->andWhere(['name'=>$auth])->one();
        
        return $auth->name;
    }

    public function getMangeCrewDivEvents($event)
    {
        $overlapping = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['<', 'start_time', $event->event_end])->andWhere(['>', 'end_time', $event->event_start])->all();
        $return = "";
        $vacation = Vacation::find()->where(['user_id'=>$this->id, 'status'=>10])->andWhere(['<', 'start_date', $event->event_end])->andWhere(['>', 'end_date', $event->event_start])->all();
        if (($overlapping)||($vacation))
        {
            foreach ($overlapping as $e)
            {
                $return .= "<small style='color:red;'>";
                $return .= $e->event->name;
                if ($e->event_schedule_id)
                {
                    $return .= " (".$e->eventSchedule->name.")";
                }
                $return .= " ".substr($e->start_time, 0, 16)." - ".substr($e->end_time, 0, 16);
                $return .= "</small>";
            }
            foreach ($vacation as $v)
            {
                $return .= "<small style='color:red;'>";
                $return .= Yii::t('app', 'Urlop');
                $return .= " ".substr($v->start_date, 0, 16)." - ".substr($v->end_date, 0, 16);
                $return .= "</small>";                
            }
            
        }else{
            $before = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['<', 'end_time', $event->event_start])->orderBy(['end_time'=> SORT_DESC])->one();
            $after = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['>', 'start_time', $event->event_end])->orderBy(['end_time'=> SORT_ASC])->one();
            if ($before){
                $return .= "<small>".Yii::t('app', 'Poprzedni: ');
                $return .= $before->event->name;
                if ($before->event_schedule_id)
                {
                    $return .= " (".$before->eventSchedule->name.")";
                }
                $return .= " do ".substr($before->end_time, 0, 16)."</small>";
            }
            if ($after){
                $return .= "<small>".Yii::t('app', 'Następny: ');
                $return .= $after->event->name;
                if ($after->event_schedule_id)
                {
                    $return .= " (".$after->eventSchedule->name.")";
                }
                $return .= " od ".substr($after->end_time, 0, 16)."</small>";
            }
        }
        return $return;
    }

        public function getMangeCrewDiv($role, $event)
    {
        $width = count($event->eventSchedules)*70;
        $return = "<div style='height:30px; min-width:".$width."px;'>";

        $width = 0;
        if ($event->eventSchedules)
            $width = 100/(count($event->eventSchedules)+1);
        $return .="<div style='height:100%;' class='manage-crew-div'><input type='checkbox'  name='schedule".$this->id."_all' class='schedule-checkbox all' data-user-id=".$this->id."  data-role-id=".$role->id."/></div>";
        foreach ($event->eventSchedules as $schedule)
        {
            if ($schedule->start_time)
            {
                $add = "";
                if ($schedule->prefix)
                    $prefix = $schedule->prefix;
                else
                    $prefix = substr($schedule->name, 0, 1);
                $color1 = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['<', 'start_time', $schedule->end_time])->andWhere(['>', 'end_time', $schedule->start_time])->count();
                $color_style = "background-color:#1ab394;";
                $vacation = Vacation::find()->where(['user_id'=>$this->id, 'status'=>10])->andWhere(['<', 'start_date', $schedule->end_time])->andWhere(['>', 'end_date', $schedule->start_time])->count();
                if (($color1)||($vacation))
                {
                    $color_style = "background-color:#cc0000;";
                    $add = " overlapping";
                }else{
                    $vacation = Vacation::find()->where(['user_id'=>$this->id, 'status'=>0])->andWhere(['<', 'start_date', $schedule->end_time])->andWhere(['>', 'end_date', $schedule->start_time])->count();
                    if ($vacation)
                        $color_style = "background-color:#ff5722;";

                    $work_start = new DateTime($schedule->start_time);
                    $work_end = new DateTime($schedule->end_time);
                    $test1 = clone($work_start)->sub(new DateInterval('PT12H'));
                    $test2 = clone($work_end)->add(new DateInterval('PT24H'));
                    $color1 = EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['<', 'start_time', $test2->format('Y-m-d H:i:s')])->andWhere(['>', 'end_time', $test1->format('Y-m-d H:i:s')])->count();
                    if (($color1))
                    {
                        $color_style = "background-color:#e69138;";
                    }
                }
                $isOnEvent = EventUserPlannedWrokingTime::find()->where(['event_id'=> $event->id])->andWhere(['user_id'=>$this->id])->andWhere(['event_schedule_id'=>$schedule->id])->one();
                $isChecked = false;
                if ($isOnEvent)
                {
                    $isChecked = EventUserRole::find()->where(['working_hours_id'=>$isOnEvent->id, 'user_event_role_id'=>$role->id])->count();
                    if ($isChecked)
                    {
                        $color_style = "background-color:#1ab394;";
                    }else{
                        $isOnOtherRole = EventUserRole::find()->where(['working_hours_id'=>$isOnEvent->id])->all();
                        if ($isOnOtherRole){
                            $color_style = "background-color:#23c6c8;";
                            $add = "";
                        }
                    }
                }
                if ($isChecked)
                    $checked = " checked";
                else
                    $checked = "";
                $return .="<div title='".$schedule->name."' style='height:100%; color:white;".$color_style."' class='manage-crew-div'>".$prefix."<input type='checkbox'  name='schedule".$this->id."_".$schedule->id."' class='schedule-checkbox".$add."' data-schedule-id=".$schedule->id."  data-user-id=".$this->id."  data-role-id=".$role->id." ".$checked."/></div>";
            }

        }
        $return .= "</div>";
        return $return;
    }

}