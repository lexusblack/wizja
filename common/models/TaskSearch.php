<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;
use common\helpers\ArrayHelper;

/**
 * TaskSearch represents the model behind the search form about `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public $usersID;
    public $my_status;
    public $task_datetime;

    public function rules()
    {
        return [
            [['id', 'creator_id', 'type', 'status', 'usersID', 'my_status', 'task_datetime'], 'integer'],
            [['title', 'content', 'datetime', 'create_time', 'update_time'], 'safe'],
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
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }

    public function searchMine($params)
    {
        $query = Task::find();
        $task_ids = ArrayHelper::map(UserTask::find()->where(['user_id'=>Yii::$app->user->id])->all(), 'task_id', 'task_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status'=>SORT_ASC, 'datetime'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            //$query->where('0=1');
            return $dataProvider;
        }
        if ($this->status)
        {
            if ($this->status==1)
            {
                $query->andFilterWhere([
                'status' => 0
                ]);
            }
            if ($this->status==3)
            {
                $query->andFilterWhere([
                'status' => 10
                ]);
            }
            if ($this->status==2)
            {
                $query->andFilterWhere([
                'status' => 0
                ]);
                $query->andFilterWhere([
                "<", 'datetime', date('Y-m-d')
                ]);
            }
        }
        if ($this->task_datetime)
        {
            if ($this->task_datetime==1)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==2)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+14, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==3)
            {
                $time = mktime(0, 0, 0, date("m")+1  , date("d"), date("Y"));
                $datetime = date('Y-m-d', $time);
            }
                $query->andWhere(['or', ["<", 'datetime', $datetime], ['datetime'=>null]]);
        }
        if ($this->my_status)
        {
            $dones = ArrayHelper::map(TaskDone::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'task_id', 'task_id');
            $ids = ArrayHelper::map(Task::find()->where(['status'=>10])->orWhere(['in', 'id', $dones])->asArray()->all(), 'id', 'id');
            if ($this->my_status==1)
            {

                $query->andWhere(['IN', 'id', $ids]);
            }
            if ($this->my_status==2)
            {
                $query->andWhere(['NOT IN', 'id', $ids]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'creator_id' => $this->creator_id
        ]);
        if ($this->title)
        {
            $event_ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->title])->orWhere(['like', 'code', $this->title])->asArray()->all(), 'id', 'id');
            $query->andFilterWhere(['or', ['like', 'title', $this->title], ['IN', 'event_id', $event_ids]]);
        }
        if ($this->usersID)
        {
            $user = User::findOne($this->usersID);
            $ids = $user->getAllTaskIds();
            $query->andWhere(['IN', 'id', $ids]);
        }

        $query->andWhere(['IN', 'id', $task_ids])
            ->andFilterWhere(['like', 'content', $this->content]);
        return $dataProvider;
    }

    public function searchOrdered($params)
    {
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status'=>SORT_ASC, 'datetime'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->task_datetime)
        {
            if ($this->task_datetime==1)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==2)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+14, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==3)
            {
                $time = mktime(0, 0, 0, date("m")+1  , date("d"), date("Y"));
                $datetime = date('Y-m-d', $time);
            }
                $query->andFilterWhere([
                "<", 'datetime', $datetime
                ]);
        }
        if ($this->status)
        {
            if ($this->status==1)
            {
                $query->andFilterWhere([
                'status' => 0
                ]);
            }
            if ($this->status==3)
            {
                $query->andFilterWhere([
                'status' => 10
                ]);
            }
            if ($this->status==2)
            {
                $query->andFilterWhere([
                'status' => 0
                ]);
                $query->andFilterWhere([
                "<", 'datetime', date('Y-m-d')
                ]);
            }
        }

        if ($this->my_status)
        {
            $dones = ArrayHelper::map(TaskDone::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'task_id', 'task_id');
            $ids = ArrayHelper::map(Task::find()->where(['status'=>10])->orWhere(['in', 'id', $dones])->asArray()->all(), 'id', 'id');
            if ($this->my_status==1)
            {

                $query->andWhere(['IN', 'id', $ids]);
            }
            if ($this->my_status==2)
            {
                $query->andWhere(['NOT IN', 'id', $ids]);
            }
        }
        if ($this->title)
        {
            $event_ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->title])->orWhere(['like', 'code', $this->title])->asArray()->all(), 'id', 'id');
            $query->andFilterWhere(['or', ['like', 'title', $this->title], ['IN', 'event_id', $event_ids]]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'creator_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content]);
        if ($this->usersID)
        {
            $user = User::findOne($this->usersID);
            $ids = $user->getAllTaskIds();
            $query->andWhere(['IN', 'id', $ids]);
        }
        return $dataProvider;
    }

    public function searchAll($params)
    {
        $query = Task::find()->where(['is', 'event_id', null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status'=>SORT_ASC, 'datetime'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->task_datetime)
        {
            if ($this->task_datetime==1)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==2)
            {
                $time = mktime(0, 0, 0, date("m")  , date("d")+14, date("Y"));
                $datetime = date('Y-m-d', $time);
            }
            if ($this->task_datetime==3)
            {
                $time = mktime(0, 0, 0, date("m")+1  , date("d"), date("Y"));
                $datetime = date('Y-m-d', $time);
            }
                $query->andWhere([
                "<", 'datetime', $datetime
                ]);
        }
        if ($this->my_status)
        {
            $dones = ArrayHelper::map(TaskDone::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'task_id', 'task_id');
            $ids = ArrayHelper::map(Task::find()->where(['status'=>10])->orWhere(['in', 'id', $dones])->asArray()->all(), 'id', 'id');
            if ($this->my_status==1)
            {

                $query->andWhere(['IN', 'id', $ids]);
            }
            if ($this->my_status==2)
            {
                $query->andWhere(['NOT IN', 'id', $ids]);
            }
        }

        if ($this->usersID)
        {
            $user = User::findOne($this->usersID);
            $ids = $user->getAllTaskIds();
            $query->andWhere(['IN', 'id', $ids]);
        }

        if ($this->title)
        {
            $event_ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->title])->orWhere(['like', 'code', $this->title])->asArray()->all(), 'id', 'id');
            $query->andFilterWhere(['or', ['like', 'title', $this->title], ['IN', 'event_id', $event_ids]]);
        }

        if ($this->status)
        {
            if ($this->status==1)
            {
                $query->andWhere([
                'status' => 0
                ]);
            }
            if ($this->status==3)
            {
                $query->andWhere([
                'status' => 10
                ]);
            }
            if ($this->status==2)
            {
                $query->andWhere([
                'status' => 0
                ]);
                $query->andWhere([
                "<", 'datetime', date('Y-m-d')
                ]);
            }
        }

        $query->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
