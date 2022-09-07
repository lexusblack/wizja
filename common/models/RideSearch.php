<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Ride;

/**
 * common\models\RideSearch represents the model behind the search form about `common\models\Ride`.
 */
 class RideSearch extends Ride
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'vehicle_id', 'user_id', 'event_id', 'km_start', 'km_end'], 'integer'],
            [['start', 'end', 'start_place', 'end_place', 'description'], 'safe'],
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
        $query = Ride::find();

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
            'vehicle_id' => $this->vehicle_id,
            'user_id' => $this->user_id,
            'event_id' => $this->event_id,
            'start' => $this->start,
            'end' => $this->end,
            'km_start' => $this->km_start,
            'km_end' => $this->km_end,
        ]);

        $query->andFilterWhere(['like', 'start_place', $this->start_place])
            ->andFilterWhere(['like', 'end_place', $this->end_place])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
