<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Event;
use yii\db\Expression;
use common\helpers\ArrayHelper;
use backend\modules\permission\models\BasePermission;

/**
 * EventSearch represents the model behind the search form about `common\models\Event`.
 */
class EventSearch extends Event
{
    public $year;
    public $month;
    public $task_name;
    public $task_status;
    public $dateRange;
    public $dateStart;
    public $dateEnd;
    public $useRange=0;
    public $statut2;
    public $statut3;
    public $statut4;
    public $statut5;
    public $statut6;
    public $statut7;
    public $statut8;
    public $statut9;
    public $statut10;
    public $statut11;
    public $statut12;
    public $statut13;
    public $statut14;
    public $statut15;
        public $statut1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'location_id', 'customer_id', 'contact_id', 'manager_id',   'packing_type', 'montage_type', 'readiness_type', 'practice_type', 'disassembly_type', 'level', 'project_done', 'invoice_issued', 'invoice_sent', 'transfer_booked', 'creator_id'], 'integer'],
            [['status','name', 'info', 'description', 'code', 'event_start', 'event_end', 'create_time', 'update_time', 'packing_start', 'packing_end', 'montage_start', 'montage_end', 'readiness_start', 'readiness_end', 'practice_start', 'practice_end', 'disassembly_start', 'disassembly_end', 'route_start', 'route_end', 'invoice_number', 'dateStart', 'dateRange', 'dateEnd','type', 'paying_date', 'event_type', 'statut2', 'statut3', 'statut4', 'statut5', 'statut6', 'statut7', 'statut1', 'statut8', 'statut9', 'statut10', 'statut11', 'statut12', 'statut13', 'statut14', 'statut15'], 'safe'],
            [['provision'], 'number'],
            [['year', 'month', 'task_status', 'useRange'], 'integer'],
            [['projectStatus', 'task_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Event::find();
        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Event::find()
                ->select('event.*')
                ->leftJoin('event_user', 'event_user.event_id = event.id')
                ->andWhere(['or', ['event_user.user_id'=>Yii::$app->user->id], ['manager_id' => Yii::$app->user->id]]);
            }
        // add conditions that should always apply here


        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]]
            ]); 
            return $dataProvider;
        }
        if ((!($this->month))&&((!$this->name)&&(!$this->customer_id)&&(!$this->manager_id)&&(!$this->paying_date)))
        {
            $this->month = date('n');
            $this->year = date('Y');
        }
        if ($this->name)
        {
            $this->month = null;
            $this->year = null;
        }

        if ($this->paying_date)
        {
            $this->month = null;
            $this->year = null;
        }

        if ($this->task_name)
        {
            if ($this->task_status)
            {
                if ($this->task_status==1)
                    $event_ids = ArrayHelper::map(Task::find()->where(['like', 'title', $this->task_name])->andWhere(['status'=>0])->asArray()->all(), 'event_id', 'event_id');
                else
                    $event_ids = ArrayHelper::map(Task::find()->where(['like', 'title', $this->task_name])->andWhere(['status'=>10])->asArray()->all(), 'event_id', 'event_id');
            }else{
                $event_ids = ArrayHelper::map(Task::find()->where(['like', 'title', $this->task_name])->asArray()->all(), 'event_id', 'event_id');
            }
            
            $query->andFilterWhere(['IN', 'event.id', $event_ids]);
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
        if ($this->statut12)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut12])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
        if ($this->statut13)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut13])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
        if ($this->statut14)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut14])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
        if ($this->statut15)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut15])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event.id', $ids]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'event.id' => $this->id,
            'location_id' => $this->location_id,
            'customer_id' => $this->customer_id,
            'contact_id' => $this->contact_id,
            'manager_id' => $this->manager_id,
            'event_start' => $this->event_start,
            'event_end' => $this->event_end,
            'status' => $this->status,
            'event.type' => $this->type,
            'event_type' => $this->event_type,
            
            'update_time' => $this->update_time,
            'packing_start' => $this->packing_start,
            'packing_end' => $this->packing_end,
            'montage_start' => $this->montage_start,
            'montage_end' => $this->montage_end,
            'readiness_start' => $this->readiness_start,
            'readiness_end' => $this->readiness_end,
            'practice_start' => $this->practice_start,
            'practice_end' => $this->practice_end,
            'disassembly_start' => $this->disassembly_start,
            'disassembly_end' => $this->disassembly_end,
            'packing_type' => $this->packing_type,
            'montage_type' => $this->montage_type,
            'readiness_type' => $this->readiness_type,
            'practice_type' => $this->practice_type,
            'disassembly_type' => $this->disassembly_type,
            'level' => $this->level,
            'provision' => $this->provision,
            'project_done' => $this->project_done,
            'invoice_issued' => $this->invoice_issued,
            'invoice_sent' => $this->invoice_sent,
            'transfer_booked' => $this->transfer_booked,
            'creator_id' => $this->creator_id,
        ]);

        if ((isset($this->paying_date))&&($this->paying_date!=""))
        {
            if (is_array($this->paying_date))
            {
                $pp = $this->paying_date;
                foreach ($this->paying_date as $d)
                {
                    
                    if (strlen($d)==6)
                    {
                        for ($i=1; $i<13; $i++)
                        {
                            $pp[] = date("Y-m-d", mktime(0, 0, 0, $i, 1, substr($d,0,4)));
                        }
                    }
                }
                //$this->paying_date = $pp;
                $query->andFilterWhere(['in', 'paying_date', $pp]);
            }
        }

        $query
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'route_start', $this->route_start])
            ->andFilterWhere(['like', 'route_end', $this->route_end])
            ->andFilterWhere(['like', 'invoice_number', $this->invoice_number])
            ->andFilterWhere(['like', 'create_time', $this->create_time]);

        if (\Yii::$app->params['companyID']!="newsytem")
        {
                if (($this->useRange)&&($this->dateStart!=""))
                {
                    $date_start = $this->dateStart;
                    $date_end =   $this->dateEnd;
                            $query->andWhere(
                                ['<', 'event_start', $date_end]
                            )->andWhere(['>', 'event_end', $date_start]);
                }else{
                        if (empty($this->year) == false)
                        {
                            if (empty($this->month) == false){
                                $date_start = date("Y-m-d H:i:s", mktime(0, 0, 0, $this->month, 1, $this->year));
                                $date_end= date("Y-m-d H:i:s", mktime(0, 0, 0, $this->month+1, 1, $this->year));
                            }else{
                                $date_start = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $this->year));
                                $date_end= date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $this->year+1));
                            }
                            $this->dateStart = $date_start;
                            $this->dateEnd = $date_end;
                            $query->andWhere(
                                ['<', 'event_start', $date_end]
                            )->andWhere(['>', 'event_end', $date_start]);
                        }
                }


            }else{
                if (empty($this->year) == false)
                {
                    $query->andWhere([
                        'YEAR(event_start)'=>$this->year
                    ]);
                }

                if (empty($this->month) == false)
                {
                    $query->andWhere(
                        ['MONTH(event_start)'=>$this->month]
                    );
                }
            }


        if (empty($this->name) == false)
        {
            //szukamy produkcji i wydruków podpiętych pod ten event - tylko po code
            $ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->name])->orWhere(['like', 'code', $this->name])->asArray()->all(), 'id', 'id');
            $tasks = ArrayHelper::map(Task::find()->where(['event_id'=> $ids])->asArray()->all(), 'id', 'id');
            $e_ids = ArrayHelper::map(EventTask::find()->where(['task_id'=>$tasks])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere([
                'or',
                ['id'=>$e_ids],
                ['like', 'code', $this->name],
                ['like', 'name', $this->name]
            ]);
        }
        if (empty($this->projectStatus) == false)
        {
            $query->andWhere([$this->projectStatus=>1]);
            $statuts = [
            'offer_sent',
            'offer_accepted',
            'expense_entered',
            'project_done',
            'ready_to_invoice',
            'invoice_issued',
            'invoice_sent',
            'transfer_booked',
            ];
            if ($this->projectStatus=='ready_to_invoice')
            {
                $query->andWhere(['invoice_issued'=>0]);
            }
            if ($this->projectStatus=='offer_sent')
            {
                $query->andWhere(['offer_accepted'=>0]);
            }
            if ($this->projectStatus=='offer_accepted')
            {
                $query->andWhere(['project_done'=>0]);
            }
        }
        $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]]
            ]); 

        return $dataProvider;
    }

public function search2($params)
    {
        $query = Event::find();
        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Event::find()
                ->select('event.*')
                ->leftJoin('event_user', 'event_user.event_id = event.id')
                ->andWhere(['or', ['event_user.user_id'=>Yii::$app->user->id], ['manager_id' => Yii::$app->user->id]]);
            }
        // add conditions that should always apply here


        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]]
            ]); 
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'location_id' => $this->location_id,
            'customer_id' => $this->customer_id,
            'contact_id' => $this->contact_id,
            'manager_id' => $this->manager_id,
            'event_start' => $this->event_start,
            'event_end' => $this->event_end,
            'status' => $this->status,
            'event.type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'packing_start' => $this->packing_start,
            'packing_end' => $this->packing_end,
            'montage_start' => $this->montage_start,
            'montage_end' => $this->montage_end,
            'readiness_start' => $this->readiness_start,
            'readiness_end' => $this->readiness_end,
            'practice_start' => $this->practice_start,
            'practice_end' => $this->practice_end,
            'disassembly_start' => $this->disassembly_start,
            'disassembly_end' => $this->disassembly_end,
            'packing_type' => $this->packing_type,
            'montage_type' => $this->montage_type,
            'readiness_type' => $this->readiness_type,
            'practice_type' => $this->practice_type,
            'disassembly_type' => $this->disassembly_type,
            'level' => $this->level,
            'provision' => $this->provision,
            'project_done' => $this->project_done,
            'invoice_issued' => $this->invoice_issued,
            'invoice_sent' => $this->invoice_sent,
            'transfer_booked' => $this->transfer_booked,
            'creator_id' => $this->creator_id,
        ]);

        $query
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'route_start', $this->route_start])
            ->andFilterWhere(['like', 'route_end', $this->route_end])
            ->andFilterWhere(['like', 'invoice_number', $this->invoice_number]);

        if (empty($this->year) == false)
        {
            $query->andWhere([
                'or',
                ['YEAR(event_start)'=>$this->year],
                ['YEAR(event_end)'=>$this->year],
            ]);
        }

        if (empty($this->month) == false)
        {
            $query->andWhere([
                'or',
                ['MONTH(event_start)'=>$this->month],
                ['MONTH(event_end)'=>$this->month],
            ]);
        }
        if (empty($this->name) == false)
        {
            $query->andWhere([
                'or',
                ['like', 'code', $this->name],
                ['like', 'name', $this->name]
            ]);
        }
        if (empty($this->projectStatus) == false)
        {
            $query->andWhere([$this->projectStatus=>1]);
            $statuts = [
            'offer_sent',
            'offer_accepted',
            'expense_entered',
            'project_done',
            'ready_to_invoice',
            'invoice_issued',
            'invoice_sent',
            'transfer_booked',
            ];
            if ($this->projectStatus=='ready_to_invoice')
            {
                $query->andWhere(['invoice_issued'=>0]);
            }
            if ($this->projectStatus=='offer_sent')
            {
                $query->andWhere(['offer_accepted'=>0]);
            }
            if ($this->projectStatus=='offer_accepted')
            {
                $query->andWhere(['project_done'=>0]);
            }
        }
        $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort'=> ['defaultOrder' => ['event_start'=>SORT_DESC]]
            ]); 

        return $dataProvider;
    }
}
