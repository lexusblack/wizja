<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\base\OutcomesGearOuter;

/**
 * OutcomesGearOuterSearch represents the model behind the search form about `common\models\base\OutcomesGearOuter`.
 */
class OutcomesGearOuterSearch extends OutcomesGearOuter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'outcome_id', 'outer_gear_id', 'gear_quantity'], 'integer'],
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
        $query = OutcomesGearOuter::find();

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
            'outcome_id' => $this->outcome_id,
            'outer_gear_id' => $this->outer_gear_id,
            'gear_quantity' => $this->gear_quantity,
        ]);

        return $dataProvider;
    }
}
