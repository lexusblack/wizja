<?php
namespace common\models\form;

use backend\modules\permission\models\BasePermission;
use common\models\Event;
use common\models\Rent;
use common\models\Vacation;
use common\models\Meeting;
use common\models\Personal;
use common\models\EventASResult;
use common\models\CalendarUserFilter;
use common\helpers\ArrayHelper;
use Yii;

class CalendarSearch extends \yii\base\Model
{
    const TYPE_EVENT = 'event';
    const TYPE_PERSONAL = 'personal';
    const TYPE_MEETING = 'meeting';
    const TYPE_RENT = 'rent';
    const TYPE_VACATION = 'vacation';

    public $type = [];
    public $name;
    public $manager_id;
    public $coordinator;
    public $department;
    public $contact;
    public $customer;
    public $user;
    public $projectStatus;
    public $user_filter;
    public $start;
    public $end;
    public $statut2;
    public $statut3;
    public $statut4;
    public $statut5;
    public $statut6;
    public $statut7;
    public $statut1;
    public $rentStatus;
    public $statut8;
    public $statut9;
    public $statut10;
    public $statut11;

    protected $_events;
    protected $_rents;
    protected $_meetings;
    protected $_personals;
    protected $_vacations;

    public function rules()
    {
        $rules = [
//            [['type', 'manager', 'coordinator', 'department', 'contact', 'client'], 'each', 'rule'=>'integer'],
            [['manager_id', 'user_filter', 'coordinator', 'department', 'contact', 'customer', 'user', 'statut2', 'statut3', 'statut4', 'statut5', 'statut6', 'statut7', 'statut1', 'statut8', 'statut9', 'statut10', 'statut11', 'name'], 'safe'],
            [['type',], 'each', 'rule'=>['string']],
            [['projectStatus','rentStatus'], 'each', 'rule'=>['integer']],

        ];
        return array_merge(parent::rules(), $rules);
    }

    public static function typeList()
    {
        $list = [];
        $user = Yii::$app->user;
        if ($user->can('calendarEvents'))
        {
            $list[self::TYPE_EVENT] = Yii::t('app', 'Wydarzenie');
            $list['produkcja'] = Yii::t('app', 'Produkcja');
            $list['biuro'] = Yii::t('app', 'Prace biurowe');
            $list['grafika'] = Yii::t('app', 'Prace graficzne');
            $list['magazyn'] = Yii::t('app', 'Prace magazynowe');
            $list = \common\models\Event::getTypeList();

        }
        if ($user->can('calendarRents'))
        {
            $list[self::TYPE_RENT] = Yii::t('app', 'Wypożyczenie');
        }
        if ($user->can('calendarMeetings'))
        {
            $list[self::TYPE_MEETING] = Yii::t('app', 'Spotkanie');
        }
        $list[self::TYPE_PERSONAL] = Yii::t('app', 'Wydarzenie prywatne');
        if ($user->can('calendarVacations'))
        {
            $list[self::TYPE_VACATION] = Yii::t('app', 'Urlop');
        }


        return $list;
    }

    public static function projectStatusList()
    {
        return Event::getStatusList();
    }

    public function search($params)
    {
        $this->load($params);
        $paramsForFilter = ["CalendarUserFilter" => isset($params["CalendarSearch"]) ? $params["CalendarSearch"] : []];
        $this->saveFilterForCurrentUser($paramsForFilter);
//        if ($this->department != null)
//        {
//            $this->type = static::TYPE_EVENT;
//        }

        $this->_setData();

    }

    protected function _setData()
    {

        if (($this->type)&&($this->type!=""))
        {
            $setEvents = false;
            foreach ($this->type as $type)
            {
                if (is_numeric($type))
                {
                    $setEvents = true;
                   
                }
                switch ($type)
                {
                case static::TYPE_RENT:
                    $this->_setRents();
                    break;
                case static::TYPE_MEETING:
                    $this->_setMeetings();
                    break;
                case static::TYPE_PERSONAL:
                    $this->_setPersonals();
                    break;
                case static::TYPE_VACATION:
                    $this->_setVacations();
                    break;
                }

            }
            if ($setEvents)
                 $this->_setEvents();
        }else{
            $this->_setEvents();
                $this->_setMeetings();
                $this->_setRents();
                $this->_setPersonals();
                $this->_setVacations();
        }
        

    }

    public function saveFilterForCurrentUser($params){
        $user_id = Yii::$app->user->identity->id;
        $model = CalendarUserFilter::findOne(['user_id' => $user_id]);
        if(!$model) {
           $model = new CalendarUserFilter();
           $model->user_id = $user_id;
        }
        
        if($params == ["CalendarUserFilter" => []]){
           /* $model->type = null;
            $model->manager_id = null;
            $model->coordinator = null;
            $model->department = null;
            $model->contact = null;
            $model->customer = null;
            $model->projectStatus = null;
            $model->rentStatus = null;
            $model->user_filter = null; */
        } else {
            if ($params['CalendarUserFilter']['type'])
                $params['CalendarUserFilter']['type'] = implode(",", $params['CalendarUserFilter']['type']);
            if ($params['CalendarUserFilter']['projectStatus'])
                $params['CalendarUserFilter']['projectStatus'] = implode(",", $params['CalendarUserFilter']['projectStatus']);
            if ($params['CalendarUserFilter']['rentStatus'])
                $params['CalendarUserFilter']['rentStatus'] = implode(",", $params['CalendarUserFilter']['rentStatus']);
            $model->load($params);
        }
        $model->save();
    }

    public function loadFilterParamsForCurrentUser(){
        $user_id = Yii::$app->user->identity->id;
        $model = CalendarUserFilter::findOne(['user_id' => $user_id]);
        $arr = [];
        if($model){
            foreach ($model->getAttributes() as $key => $value) {
                $arr[$key] = isset($value) ? $value : "";
                if (($key == 'type')||($key == 'projectStatus')||($key == 'rentStatus'))
                {
                    if ($value!="")
                        $arr[$key] = explode(",", $value);
                    else
                        $arr[$key] = null;
                }
            }
            unset($arr["id"]);
            unset($arr["user_id"]);
        }
        return $arr;
    }

    public function cleanFilter(){
        $user_id = Yii::$app->user->identity->id;
        $model = CalendarUserFilter::findOne(['user_id' => $user_id]);
        $model->delete();
    }

    public function getEvents()
    {
        return $this->_events;
    }
    protected function _setEvents() {

        $user = Yii::$app->user;
        if (!$user->can('menuCalendar') || !$user->can('calendarEvents')) {
            return;
        }

        if ($user->can('calendarEvents'.BasePermission::SUFFIX[BasePermission::MINE]) && !$user->can('calendarEvents'.BasePermission::SUFFIX[BasePermission::ALL])) {
            $this->user = $user->id;
            //$this->manager_id = $user->id;
        }

        if ($this->user_filter != null && $this->user == null) {
            $this->user = $this->user_filter;
        }

        $query = Event::find();
        if ($this->user != null) {
            $query->select('event.*');
            $query->leftJoin('event_user', 'event_user.event_id = event.id');
            $query->andWhere(['or', ['event_user.user_id' => $this->user], ['event.manager_id' => $this->user]]);
        }

        if ($this->manager_id != null) {
            $query->andWhere(['manager_id' => $this->manager_id]);
        } 

        if ($this->name != null)
        {
            $query->andWhere(['LIKE', 'name', $this->name]);
        }

        if ($this->department != null) {
            $query->innerJoinWith('departments');
            $query->andFilterWhere(['department.id'=>$this->department]);
        }

        if ($this->statut1)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut1])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut2)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut2])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut3)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut3])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut4)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut4])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut5)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut5])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut6)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut6])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut7)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut7])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut8)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut8])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut9)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut9])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }

        if ($this->statut10)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut10])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
                if ($this->statut11)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut11])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
        $date_start = $this->start;
        $date_end = $this->end;
        $query->andWhere(['<', 'event_start', $date_end])->andWhere(['>', 'event_end', $date_start]);


        $query = $this->_addCustomerContactQuery($query);

        if (!empty($this->projectStatus)) {
                $query->andWhere(['event.status'=>$this->projectStatus]);
            
        }

        if (($this->type)&&($this->type!=""))
        {
            /*$types = [];
            foreach ($this->type as $type)
            {
                if ($type =='event')
                    $types[] = 1;
                if ($type =='biuro')
                    $types[] = 3;
                if ($type =='grafika')
                    $types[] = 4;
                if ($type =='produkcja')
                    $types[] = 2;
                if ($type =='magazyn')
                    $types[] = 5;
            }*/
            $query->andWhere(['event.type'=>$this->type]);
        }

        $models = $query->all();
        $this->_events = $models;
    }

    public function getMeetings()
    {
        return $this->_meetings;
    }
    protected function _setMeetings() {
        $user = Yii::$app->user;
        $models = null;
        if (!$user->can('calendarMeetings')) {
            return;
        }
        if ($user->can('calendarMeetings'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $models = Meeting::find()->joinWith('users')->where(['user.id' => $user->id])->andWhere(['meeting.active'=>1]);
        }
        if ($user->can('calendarMeetings'.BasePermission::SUFFIX[BasePermission::ALL])) {
            $models = Meeting::find()->where(['active'=>1]);
        }
        if ($this->customer != null) {
            $models->andWhere(['customer_id' => $this->customer]);
        }

        if ($this->user_filter != null) {
            $models->joinWith('users')->where(['user.id'=>$this->user_filter]);
        }
        $models->andWhere(['<', 'start_time', $this->end]);
        $models->andWhere(['>', 'end_time', $this->start]);
        $this->_meetings = $models->all();
    }

    public function getRents()
    {
        return $this->_rents;
    }
    protected function _setRents()
    {
        $user = Yii::$app->user;
        if (!$user->can('calendarRents')) {
            return;
        }
        if ($user->can('calendarRents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Rent::find()->andWhere(['created_by' => $user->id]);
        }
        if ($user->can('calendarRents'.BasePermission::SUFFIX[BasePermission::ALL])) {
            $query = Rent::find();
        }
        if ($this->name != null)
        {
            $query->andWhere(['LIKE', 'name', $this->name]);
        }
            $query->andFilterWhere([
                'status'=>$this->rentStatus,
            ]);
            $query->andFilterWhere([
                'manager_id'=>$this->manager_id,
            ]);
            $query = $this->_addCustomerContactQuery($query);
            $query->andWhere(['<', 'start_time', $this->end]);
            $query->andWhere(['>', 'end_time', $this->start]);
            $models = $query->all();
        $this->_rents = $models;
    }

    public function getVacations()
    {
        return $this->_vacations;
    }
    protected function _setVacations() {
        $user = Yii::$app->user;
        $models = [];
        
        if (!$user->can('calendarVacations')) {
            return;
        }

        if ($user->can('calendarVacations'.BasePermission::SUFFIX[BasePermission::MINE])) {
            //$this->user = $user->id;
            $models = Vacation::find()->andWhere([
                'user_id'=>$user->id,
            ])->all();
        }
        
        if ($user->can('calendarVacations'.BasePermission::SUFFIX[BasePermission::ALL])) {
            $models = Vacation::find();
            if ($this->user_filter != null) {
                $models->andWhere(['user_id'=>$this->user_filter]);
            }
            $models->andWhere(['<', 'start_date', $this->end]);
            $models->andWhere(['>', 'end_date', $this->start]);
            $models = $models->all();
        }
        $this->_vacations = $models;
    }

    public function getPersonals()
    {
        return $this->_personals;
    }
    protected function _setPersonals() {
        $models = [];
        if (Yii::$app->user->can('eventsMeetingsPrivate')) {
            $models = Personal::find()->andWhere(['user_id'=>\Yii::$app->user->id])->all();
        }

        $this->_personals = $models;
    }


    protected function _addCustomerContactQuery($query)
    {
        if ($this->customer !=null || $this->contact != null)
        {
            $query->andFilterWhere([
                'customer_id'=>$this->customer,
                'contact_id' => $this->contact,

            ]);
        }
        return $query;
    }
}