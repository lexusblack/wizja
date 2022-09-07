<?php
namespace common\models\form;

use backend\modules\permission\models\BasePermission;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\Event;
use common\models\Note;
use common\models\EventGearItem;
use common\models\EventUser;
use common\models\Meeting;
use common\models\Personal;
use common\models\Rent;
use common\models\Task;
use common\models\UserNotification;
use common\models\Invoice;
use common\models\Expense;
use common\models\GearService;
use common\models\EventExpense;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Yii;

class Dashboard extends Model
{
    public $user;

    protected $_todayEvents = [];
    protected $_upcomingEvents = [];
    protected $_departmentEvents = [];


    public function getTodayEvents()
    {
        if ($this->_todayEvents == null)
        {
            $today = date('Y-m-d');
            $todayStart = $today.' 00:00:00';
            $todayEnd = $today.' 23:59:00';

            $this->_setEventsForDates($todayStart, $todayEnd, '_todayEvents');

        }

        $dataProvider = new ArrayDataProvider([
            'allModels'=>$this->_todayEvents,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getUpcomingEvents()
    {
        if ($this->_upcomingEvents == null)
        {
            $start = date('Y-m-d 00:00:00', strtotime('+1 day'));
            if (Yii::$app->params['companyID']=="imagination")
            {
                $end = date('Y-m-d 23:59:00', strtotime($start.'+1 month'));
            }else{
                $end = date('Y-m-d 23:59:00', strtotime($start.'+1 week'));
            }
            $this->_setEventsForDates($start, $end, '_upcomingEvents');

        }

        $dataProvider = new ArrayDataProvider([
            'allModels'=>$this->_upcomingEvents,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getDepartmentEvents()
    {
        if ($this->_departmentEvents == null)
        {
            $start = date('Y-m-d 00:00:00', strtotime('+1 day'));
            if (Yii::$app->params['companyID']=="imagination")
            {
                $end = date('Y-m-d 23:59:00', strtotime($start.'+1 month'));
            }else{
                $end = date('Y-m-d 23:59:00', strtotime($start.'+1 week'));
            }
            $this->_setDepartmentEventsForDates($start, $end, '_departmentEvents');

        }

        $dataProvider = new ArrayDataProvider([
            'allModels'=>$this->_departmentEvents,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getChecklist()
    {
        /** @var \common\models\User $user */
        $user = Yii::$app->user->getIdentity();
        return $user->getChecklist();
    }

    protected function _setEventsForDates($start, $end, $attribute)
    {
        $user = Yii::$app->user;
        $statuts = \common\helpers\ArrayHelper::map(\common\models\EventStatut::find()->where(['show_in_dashboard'=>1])->asArray()->all(), 'id', 'id');
        if (!$user->isSuperAdmin) {
            $this->user = $user->id;
        }
        if ($user->can('cockpitEvents'.BasePermission::SUFFIX[BasePermission::ALL]) && $attribute == '_upcomingEvents') {
            $this->user = null;
        }
        if ($user->can('cockpitToday'.BasePermission::SUFFIX[BasePermission::ALL]) && $attribute == '_todayEvents') {
            $this->user = null;
        }

        $query = Event::find()
            ->select('event.*')
            ->leftJoin('event_user', 'event_user.event_id = event.id')
            ->where('packing_start<=:end OR montage_start<=:end OR readiness_start<=:end OR practice_start<=:end OR disassembly_start<=:end OR event_start<=:end', [':end'=>$end])
            ->andWhere('packing_end>=:start OR montage_end>=:start OR readiness_end>=:start OR practice_end>=:start OR disassembly_end>=:start OR event_end>=:start', [':start'=>$start])->andWhere(['status'=>$statuts]);

        if ($this->user != null) {
            $query->andWhere(['or', ['event_user.user_id'=>$this->user], ['manager_id' => $this->user]]);
        }


        $events = $query->all();
        $this->{$attribute} = array_merge($this->{$attribute}, $this->_preapreEvents($events));

        $queryR = Rent::find()->where([
            'and',
            ['<=', 'start_time', $end],
            ['>=', 'end_time', $start],
        ])->andWhere(['status'=>$statuts]);
        if ($this->user != null) {
            $query->andWhere(['or', ['created_by'=>$this->user], ['manager_id' => $this->user]]);
        }

        if ($this->user != null)
        {
            $query->innerJoinWith('users')
                ->andFilterWhere([
                    'user.id'=>$this->user,
                ]);
        }
          $rents = $queryR->all();
        $this->{$attribute} = array_merge($this->{$attribute}, $this->_preapreEvents($rents));

        $personal = Personal::find()->where([
            'and',
            ['<=', 'start_time', $end],
            ['>=', 'end_time', $start],
        ])
            ->andWhere(['user_id'=>\Yii::$app->user->id])
//            ->asArray()
            ->all();
        $this->{$attribute} = array_merge($this->{$attribute}, $this->_preapreEvents($personal));

         $queryM = Meeting::find()->where([
            'and',
            ['<=', 'start_time', $end],
            ['>=', 'end_time', $start],
        ]);

        if ($this->user != null)
        {
            $queryM->innerJoinWith('users')
                ->andFilterWhere([
                    'user.id'=>$this->user,
                ]);
        }

//            ->asArray()
        $meeting = $queryM->all();
        $this->{$attribute} = array_merge($this->{$attribute}, $this->_preapreEvents($meeting));

        ArrayHelper::multisort($this->{$attribute}, 'dateFrom', [SORT_ASC]);
    }

    protected function _setDepartmentEventsForDates($start, $end, $attribute)
    {
        $departments = \Yii::$app->user->identity->getDepartments()->asArray()->column();
        $statuts = \common\helpers\ArrayHelper::map(\common\models\EventStatut::find()->where(['show_in_dashboard'=>1])->asArray()->all(), 'id', 'id');

        $events = Event::find()
            ->where('packing_start<=:end OR montage_start<=:end OR readiness_start<=:end OR practice_start<=:end OR disassembly_start<=:end OR event_start<=:end', [':end'=>$end])
            ->andWhere('packing_end>=:start OR montage_end>=:start OR readiness_end>=:start OR practice_end>=:start OR disassembly_end>=:start OR event_end>=:start', [':start'=>$start])
//            ->asArray()
                ->innerJoinWith('eventDepartments')
            ->andWhere(['department_id'=>$departments])->andWhere(['status'=>$statuts])
            ->all();
        $this->{$attribute} = array_merge($this->{$attribute}, $this->_preapreEvents($events));

        ArrayHelper::multisort($this->{$attribute}, 'dateFrom', [SORT_ASC]);
    }

    protected function _preapreEvents($models)
    {
        $formatter = \Yii::$app->formatter;
        $format = 'php:d-m-Y H:i';

        $events = [];
        foreach ($models as $model)
        {
            $data = [];
            $packTime = null;
            $disassembyTime = null;
            $dateRange = '';
            $dateFrom = null;
            $dateTo = null;
			$url = null;
            switch ($model->getClassType())
            {
                case 'event':
                    $packTime = $formatter->asDatetime($model->packing_start, $format);
                    $disassembyTime = $formatter->asDatetime($model->disassembly_start, $format);
                    $dateRange = Yii::t('app', "Od").": ".$formatter->asDatetime($model->getTimeStart(), $format).'<br/>'.Yii::t('app', 'Do').': '.$formatter->asDatetime($model->getTimeEnd(), $format);
                    $dateFrom = $model->getTimeStart();
                    $dateTo = $model->getTimeEnd();
                    $url = Url::toRoute(['/event/view'])."?id=".$model->id;
                    break;
                case 'rent':
                    $packTime = $formatter->asDatetime($model->deliver_time, $format);
                    $disassembyTime = $formatter->asDatetime($model->return_time, $format);
                    $dateRange = Yii::t('app', "Od").": ".$formatter->asDatetime($model->getTimeStart(), $format).'<br/>'.Yii::t('app', 'Do').': '.$formatter->asDatetime($model->getTimeEnd(), $format);
                    $dateFrom = $model->getTimeStart();
                    $dateTo = $model->getTimeEnd();
	                $url = Url::toRoute(['/rent/view'])."?id=".$model->id;
                    break;
                default:
                    $dateRange = Yii::t('app', "Od").": ".$formatter->asDatetime($model->start_time, $format).'<br/>'.Yii::t('app', 'Do').': '.$formatter->asDatetime($model->end_time, $format);
                    $dateFrom = $model->start_time;
                    $dateTo = $model->end_time;
                    break;
            }
			if ($model->getClassType() == 'meeting') {
				$url = Url::toRoute(['/meeting/view'])."?id=".$model->id;
			}

            if ($url) {
	            $url = $this->getServerProtocol() .  Yii::$app->getRequest()->serverName . $url;
            }

            $data = [
                'id' => $model->id,
                'name' => $model->name,
                'type' => get_class($model)::getClassTypeLabel(),
                'pack' => $packTime,
                'disassembly' =>$disassembyTime,
                'dateRange' => $dateRange,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
	            'url' => $url,
            ];

            $events[] = $data;
        }

        return $events;
    }

    public static function getUserMessages() {
        $list = [];
        $models = UserNotification::getListForUser();
        foreach ($models as $model) {
            /* @var $model UserNotification */
            $url = null;
	        $id = null;
	        if ($model->target_class == 'common\models\Event') {
				$url =  Url::to(['/event/view', 'id'=>$model->target_id]);
				$id = $model->target_id;
			}
			if ($model->target_class == 'common\models\Rent') {
				$url =  Url::to(['/rent/view', 'id'=>$model->target_id]);
				$id = $model->target_id;
			}
			if ($model->target_class == 'common\models\EventUser') {
				$tmp = EventUser::findOne($model->target_id);
				if ($tmp) {
					$id = $tmp->event->id;
					$url = Url::to( [ '/event/view', 'id' => $tmp->event->id ] );
				}
			}
	        if ($model->target_class == 'common\models\EventGearItem') {
		        $tmp = EventGearItem::findOne($model->target_id);
		        if ($tmp) {
			        $id = $tmp->event->id;
			        $url = Url::to( [ '/event/view', 'id' => $tmp->event->id ] );
		        }
	        }

	        if (!$url && $data = unserialize($model->data)) {
				if ((isset($data['event'])) && (isset($data['event']['id']))) {
					$url = Url::to( [ '/event/view', 'id' => $data['event']['id'] ] );
					$id =  $data['event']['id'];
				}
		        if ((isset($data['rent'])) && (isset($data['rent']['id']))) {
			        $url = Url::to( [ '/rent/view', 'id' => $data['rent']['id'] ] );
			        $id =  $data['rent']['id'];
		        }
	        }

            $list[] = [
            	'id' => $model->id,
            	'target_id' => $id,
                'title'=>$model->title,
                'content'=>$model->getParsedContent(),
	            'contentNoHtml' => str_replace('(link)', '', strip_tags($model->getParsedContent())),
	            'url' => $url,
            ];
        }

        return $list;
    }

    public function getMessages() {
        return self::getUserMessages();
    }

    public function getTasks()
    {
        /** @var \common\models\User $user */
        $user = \Yii::$app->user->getIdentity();
        $dataProvider = new ActiveDataProvider([
            'query' =>$user->getTasksQuery(),
            'pagination'=>false,
        ]);

        return $dataProvider;

    }

    public function getTasksCreated()
    {
        $date = date("y-m-d",strtotime("-1 month"));
        $user = \Yii::$app->user->getIdentity();
        $tasks = Task::find()->where(['creator_id'=> $user->id])->andWhere(['or', ['is', 'status', null], ['>', 'end_time', $date]])->orderBy(['end_time'=>SORT_ASC])->all();

        return $tasks;
    }


    public function getStatus()
    {
        $user = \Yii::$app->user->getIdentity();
        $events = Event::find()->where(['ready_to_invoice'=>1])->andWhere(['<', 'invoice_issued', 1])->count();
        $rents = Rent::find()->where(['status'=>30])->count();
        $invoices = Invoice::find()->where(['paid'=>0])->andWhere(['<', 'paymentdate', date('Y-m-d')])->andWhere(['>', 'date', '2017-10-01'])->count();
        $expenses = Expense::find()->where(['paid'=>0])->andWhere(['<', 'paymentdate', date('Y-m-d')])->andWhere(['>', 'date', '2017-10-01'])->count();
        $gears = GearService::find()->where(['in', 'status', [0,3,5]])->count();
        $event_expense = EventExpense::find()->where(['is', 'expense_id', null])->andWhere(['>', 'create_time', '2017-10-01'])->count();
        return ['read_to_invoice'=>$events, 'rent_to_invoice'=>$rents, 'late_invoice'=>$invoices, 'late_expenses'=>$expenses, 'service'=>$gears, 'event_expense'=>$event_expense];
    }

    public function getNews()
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d 23:59:00', strtotime($start.'-1 week'));
        return Note::find()->where(['>', 'datetime', $end])->andWhere(['note_id'=>null])->andWhere(['in_feed'=>1])->orderBy(['datetime'=>SORT_DESC])->all();
    }

	private function getServerProtocol() {
		if( isset($_SERVER['HTTPS']) ) {
			return "https://";
		}
		return "http://";
	}
}