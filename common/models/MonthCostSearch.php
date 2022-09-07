<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MonthCost;

/**
 * common\models\MonthCostSearch represents the model behind the search form about `common\models\MonthCost`.
 */
 class MonthCostSearch extends MonthCost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'department_id', 'creator_id'], 'integer'],
            [['name', 'section', 'create_time', 'update_time'], 'safe'],
            [['amount'], 'number'],
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
        $query = MonthCost::find()->where([
                'group_id'=>null,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'department_id' => $this->department_id,
            'creator_id' => $this->creator_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'section', $this->section]);

        return $dataProvider;
    }
}
