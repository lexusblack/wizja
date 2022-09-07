<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vacation;

/**
 * VacationSearch represents the model behind the search form about `common\models\Vacation`.
 */
class VacationSearch extends Vacation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'day_number', 'status', 'type'], 'integer'],
            [['start_date', 'end_date', 'create_time', 'update_time'], 'safe'],
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
        $query = Vacation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['start_date'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->start_date)
        {
            $query->andWhere(['or',['>=', 'start_date',$this->start_date ],['>=', 'end_date',$this->start_date ]]);
        }
        if ($this->end_date)
        {
            $query->andWhere(['or',['<=', 'start_date',$this->end_date ],['<=', 'end_date',$this->end_date ]]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'day_number' => $this->day_number,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        return $dataProvider;
    }
}
