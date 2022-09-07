<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventReport;
use \common\helpers\ArrayHelper;

/**
 * common\models\EventReportSearch represents the model behind the search form about `common\models\EventReport`.
 */
 class EventReportSearch extends EventReport
{
    public $year;
    public $month;
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
        public $statut1;
    public $statut11;
    public $statut12;
    public $statut13;
    public $statut14;
    public $statut15;
    public function rules()
    {
        return [
            [['id', 'event_id', 'manager_id', 'customer_id'], 'integer'],
            [['name', 'code', 'event_start', 'event_end', 'location', 'paying_date', 'dateStart', 'dateRange', 'dateEnd', 'status', 'event_model_id', 'event_type_id', 'statut2', 'statut3', 'statut4', 'statut5', 'statut6', 'statut7', 'statut1', 'statut8', 'statut9', 'statut10', 'statut11', 'statut12', 'statut13', 'statut14', 'statut15'], 'safe'],
            [['total_value', 'total_cost', 'total_provision', 'total_predicted_cost', 'total_predicted_provision'], 'number'],
            [['year', 'month', 'useRange'], 'integer'],
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
        $query = EventReport::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

            if ($this->useRange)
                {
                    $date_start = $this->dateStart;
                    $date_end =   $this->dateEnd;
                            $query->andWhere(['<', 'event_start', $date_end])->andWhere(['>', 'event_end', $date_start]);
                }else{
                        if ((empty($this->year) == false)&&($this->year!=""))
                        {
                            if ((empty($this->month) == false)&&($this->month!="")){
                                $date_start = date("Y-m-d H:i:s", mktime(0, 0, 0, $this->month, 1, $this->year));
                                $date_end= date("Y-m-d H:i:s", mktime(0, 0, 0, $this->month+1, 1, $this->year));
                            }else{
                                $date_start = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $this->year));
                                $date_end= date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $this->year+1));
                            }
                            $this->dateStart = $date_start;
                            $this->dateEnd = $date_end;
                            $query->andWhere(['<', 'event_start', $date_end])->andWhere(['>', 'event_end', $date_start]);
                        }
                }
            if (!$this->dateStart)
            {
                $this->dateStart="2021-01-01";
                $this->dateEnd = "2022-01-01";
                $query->andWhere(['<', 'event_start', $this->dateEnd])->andWhere(['>', 'event_end', $this->dateStart]);
            }
        if ($this->statut1)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut1])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut2)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut2])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut3)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut3])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut4)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut4])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut5)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut5])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut6)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut6])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut7)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut7])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut8)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut8])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut9)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut9])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        if ($this->statut10)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut10])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }
        if ($this->statut11)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut11])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }
        if ($this->statut12)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut12])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }
        if ($this->statut13)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut13])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }
        if ($this->statut14)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut14])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }
        if ($this->statut15)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut15])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'event_id' => $this->event_id,
            'manager_id' => $this->manager_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'total_value' => $this->total_value,
            'total_cost' => $this->total_cost,
            'total_provision' => $this->total_provision,
            'total_predicted_cost' => $this->total_predicted_cost,
            'total_predicted_provision' => $this->total_predicted_provision,
            'event_model_id' => $this->event_model_id,
            'event_type_id' => $this->event_type_id,
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'location', $this->location]);

        //echo $query->createCommand()->getRawSql();
        return $dataProvider;
    }
}
