<?php
namespace common\models\form;

use common\models\Department;
use common\models\Event;
use common\models\Rent;
use common\models\Vacation;
use common\models\Meeting;
use common\models\Personal;
use common\models\CalendarUserFilter;
use common\models\User;
use common\models\Vehicle;
use common\models\Skill;
use Yii;
use yii\helpers\ArrayHelper;

class PlanboardSearch extends \yii\base\Model
{
    const TYPE_EVENT = 'event';
    const TYPE_PERSONAL = 'personal';
    const TYPE_MEETING = 'meeting';
    const TYPE_RENT = 'rent';
    const TYPE_VACATION = 'vacation';

    public $type;
    public $manager;
    public $coordinator;
    public $department;
    public $contact;
    public $customer;
    public $user;
    public $projectStatus;

    public $rentStatus;

    protected $_events;
    protected $_rents;
    protected $_meetings;
    protected $_personals;
    protected $_vacations;
    protected $_users;
    protected $_vehicles;

    public function rules()
    {
        $rules = [
            [['manager', 'coordinator', 'department', 'contact', 'customer', 'user', 'rentStatus'], 'integer'],
            [['type', 'projectStatus'], 'string'],

        ];
        return array_merge(parent::rules(), $rules);
    }

    public static function typeList()
    {
        return [
            self::TYPE_EVENT => Yii::t('app', 'Wydarzenie'),
            self::TYPE_RENT => Yii::t('app', 'Wypożyczenie'),
            self::TYPE_MEETING => Yii::t('app', 'Spotkanie'),
            self::TYPE_PERSONAL => Yii::t('app', 'Prywatne'),
            self::TYPE_VACATION => Yii::t('app', 'Urlop'),
        ];
    }

    public static function projectStatusList()
    {
        return Event::getProjectStatusList();
    }

    public function search($params)
    {
        $this->load($params);
        $this->_setData();
    }

    protected function _setData()
    {
        switch ($this->type)
        {
            case static::TYPE_EVENT:
                $this->_setEvents();
                break;
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
            default:
                $this->_setEvents();
                $this->_setMeetings();
                $this->_setRents();
                $this->_setPersonals();
                $this->_setVacations();
                $this->_setUsers();
                $this->_setVehicles();
                break;
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
            $model->type = null;
            $model->manager_id = null;
            $model->coordinator = null;
            $model->department = null;
            $model->contact = null;
            $model->customer = null;
            $model->projectStatus = null;
            $model->rentStatus = null;
        } else {
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
    protected function _setEvents()
    {
        // $user = Yii::$app->user;

        $query = Event::find();
        $query->joinWith(['departments','users']);
        // $query->orderBy(['event.name' => SORT_ASC]);
        $models = $query->all();

        // var_dump($models);die;
        $this->_events = $models;
    }

    public function getMeetings()
    {
        return $this->_meetings;
    }
    protected function _setMeetings()
    {
        $models = Meeting::find()->all();
        $this->_meetings = $models;
    }

    public function getRents()
    {
        return $this->_rents;
    }
    protected function _setRents()
    {
        $query = Rent::find();
        $query->andFilterWhere([
            'status'=>$this->rentStatus,
        ]);
        $query = $this->_addCustomerContactQuery($query);
        $models = $query->all();


        $this->_rents = $models;
    }

    public function getVacations()
    {
        return $this->_vacations;
    }
    protected function _setVacations()
    {
        $query = Vacation::find();
        $query->andFilterWhere([
            'user_id'=>$this->user,
        ]);
        $models = $query->all();
        $this->_vacations = $models;
    }

    public function getPersonals()
    {
        return $this->_personals;
    }
    protected function _setPersonals()
    {
        $query = Personal::find();
        if (Yii::$app->user->can('SiteAdministrator') == false)
        {
            $query->andWhere([
                'user_id'=>\Yii::$app->user->id,
            ]);
        }

        $models = $query->all();
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

    public function getUsers()
    {
        return $this->_users;
    }
    protected function _setUsers()
    {
        $query = User::find()
        ->select([ 'id','first_name','last_name','type' ])
        ->with(['skills', 'vacations','eventUsers'])->asArray()->all();

        $this->_users = $query;
    }

    public function getVehicles()
    {
        return $this->_vehicles;
    }

    protected function _setVehicles()
    {
        $query = Vehicle::find()->joinWith(['events'])->all();

        $vehicles = [];

        foreach ($query as $key => $vehicle) {
            $vehicle_data = ArrayHelper::toArray($vehicle, [
                'common\models\Vehicle' => [
                    'name',
                    'registration_number',
                    'id'
                ],
            ]);
            $all_events = [];

            foreach ($vehicle->events as $key => $event) {

                if(!isset($all_events[$event->id])){
                    $all_events[] = [
                        'start_time' => $event->getTimeStart(),
                        'end_time' => $event->getTimeEnd(),
                        'event_id' => $event->id
                    ];
                }
                
            }

            $vehicles[] = [
                'vehicle' => $vehicle_data,
                'events' => $all_events
            ];

        }


        $this->_vehicles = $vehicles;
    }

    public function getUserTypes()
    {
        $new_obj_list = [];
        $new_obj_list[] = ["name" => Yii::t('app', 'Wszyscy')];
        $list = User::getTypeList();
        foreach ($list as $key => $value) {
            if ($value === Yii::t('app', "Pracownik")) {
                $value = Yii::t('app', "Pracownicy");
            }
            $new_obj_list[] = [
                'type' => $key,
                'name' => $value
            ];
        }
        return $new_obj_list;
    }

    public function getSkills()
    {
        $new_obj_list = [];
        $list = ArrayHelper::map(Skill::find()->all(), 'id', 'name');
        asort($list);
        $new_obj_list[] = ["name" => Yii::t('app', 'Umiejętności')];
        foreach ($list as $key => $value) {
            $new_obj_list[] = [
                'id' => $key,
                'name' => $value
            ];
        }
        return $new_obj_list;
    }

    public function getDepartments() {
        $new_obj_list = [];
        $list = ArrayHelper::map(Department::find()->all(), 'id', 'name');
        asort($list);
        $new_obj_list[] = ["name" => Yii::t('app', 'Działy')];
        foreach ($list as $key => $value) {
            $new_obj_list[] = [
                'id' => $key,
                'name' => $value
            ];
        }
        return $new_obj_list;
    }
}