<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventLog;

/**
 * EventMessageSearch represents the model behind the search form about `common\models\EventMessage`.
 */
class EventLogSearch extends EventLog
{
    /**
     * @inheritdoc
     */

        public $dateRange;
    public $year;
    public $month;
    public $dateStart;
    public $dateEnd;
    public $useRange=0;
    public function rules()
    {
        return [
            [['event_id', 'user_id'], 'integer'],
            [['content', 'create_time', 'year', 'month', 'dateStart', 'dateEnd', 'dateRange', 'useRange'], 'safe'],
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
        $query = EventLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['create_time'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if (($this->event_id)||($this->create_time))
            $this->year = null;
  if ($this->year){
            if ($this->month>0)
            {
                $date = \DateTime::createFromFormat('Yn', $this->year.$this->month);
                $dateStart = $date->format('Y-m')."-01";
                $dateEnd = $date->format('Y-m-t');              
            }else{
                $date = \DateTime::createFromFormat('Y', $this->year);
                $dateStart = $date->format('Y-01-01');
                $dateEnd = $date->format('Y-12-31');                   
            }
            $query->andFilterCompare('create_time', '>='.$dateStart);
                $query->andFilterCompare('create_time', '<='.$dateEnd);
                       
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            
        ]);

        $query->andFilterWhere(['like', 'content', $this->content]);
        $query->andFilterWhere(['like', 'create_time', $this->create_time]);

        return $dataProvider;
    }
}
