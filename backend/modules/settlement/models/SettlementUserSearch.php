<?php

namespace backend\modules\settlement\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SettlementUser;
use common\models\SettlementUserSearch as BaseSearch;

/**
 * SettlementUserSearch represents the model behind the search form about `common\models\SettlementUser`.
 */
class SettlementUserSearch extends BaseSearch
{
    public $locationId;
    public $code;
    public $managerId;
    public $level;
    public $departmentIds;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['locationId', 'managerId', 'level'], 'integer'],
            [['code'], 'string'],
            [['departmentIds'], 'each', 'rule'=>['integer']],
        ];
        return array_merge(parent::rules(), $rules);
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
            'id' => $this->id,
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
