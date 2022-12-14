<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\IncomesGearOur;

/**
 * IncomesGearOurSearch represents the model behind the search form about `common\models\base\IncomesGearOur`.
 */
class IncomesGearOurSearch extends IncomesGearOur
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'income_id', 'gear_id', 'quantity'], 'integer'],
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
        $query = IncomesGearOur::find();

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
            'income_id' => $this->income_id,
            'gear_id' => $this->gear_id,
            'quantity' => $this->quantity,
        ]);

        return $dataProvider;
    }
}
