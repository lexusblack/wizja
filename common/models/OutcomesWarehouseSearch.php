<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OutcomesWarehouse;
use yii\helpers\ArrayHelper;

/**
 * OutcomesWarehouseSearch represents the model behind the search form about `common\models\base\OutcomesWarehouse`.
 */
class OutcomesWarehouseSearch extends OutcomesWarehouse
{
    /**
     * @inheritdoc
     */

    public $year;
    public $month;
    public $event;

    public function rules()
    {
        return [
            [['id', 'user', 'year', 'month'], 'integer'],
            [['start_datetime', 'comments', 'event'], 'safe'],
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
        $query = OutcomesWarehouse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['start_datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->event!="")
        {
            $ids = ArrayHelper::map(Event::find()->where(['like', 'name', $this->event])->orWhere(['like', 'code', $this->event])->asArray()->all(), 'id', 'id');
            $ids2 = ArrayHelper::map(Rent::find()->where(['like', 'name', $this->event])->orWhere(['like', 'code', $this->event])->asArray()->all(), 'id', 'id');
            $i_ids = ArrayHelper::map(OutcomesForEvent::find()->where(['event_id'=>$ids])->asArray()->all(), 'outcome_id', 'outcome_id');
            $i_ids2 = ArrayHelper::map(OutcomesForRent::find()->where(['rent_id'=>$ids])->asArray()->all(), 'outcome_id', 'outcome_id');
            $i_ids = array_merge($i_ids, $i_ids2);
            $query->andWhere(['id'=>$i_ids]);
        }

        if (empty($this->year) == false)
        {
            $query->andWhere(['YEAR(start_datetime)'=>$this->year]);
        }

        if (empty($this->month) == false)
        {
            $query->andWhere(['MONTH(start_datetime)'=>$this->month]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user' => $this->user,
            'start_datetime' => $this->start_datetime,
        ]);

        $query->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }
}
