<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SettlementUser;

/**
 * SettlementUserSearch represents the model behind the search form about `common\models\SettlementUser`.
 */
class SettlementUserSearch extends SettlementUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'event_id', 'year', 'month', 'status'], 'integer'],
            [['department_data', 'role_data', 'addon_data', 'allowance_data', 'working_hours_data'], 'safe'],
            [['sum'], 'number'],
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
        $query = SettlementUser::find();

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
            'user_id' => $this->user_id,
            'event_id' => $this->event_id,
            'year' => $this->year,
            'month' => $this->month,
            'sum' => $this->sum,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'department_data', $this->department_data])
            ->andFilterWhere(['like', 'role_data', $this->role_data])
            ->andFilterWhere(['like', 'addon_data', $this->addon_data])
            ->andFilterWhere(['like', 'allowance_data', $this->allowance_data])
            ->andFilterWhere(['like', 'working_hours_data', $this->working_hours_data]);

        return $dataProvider;
    }
}
