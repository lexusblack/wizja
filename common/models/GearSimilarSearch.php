<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearSimilar;

/**
 * common\models\GearSimilarSearch represents the model behind the search form about `common\models\GearSimilar`.
 */
 class GearSimilarSearch extends GearSimilar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gear_id', 'similar_id'], 'integer'],
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
        $query = GearSimilar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'gear_id' => $this->gear_id,
            'similar_id' => $this->similar_id,
        ]);

        return $dataProvider;
    }
}
