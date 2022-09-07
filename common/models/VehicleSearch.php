<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vehicle;

/**
 * VehicleSearch represents the model behind the search form about `common\models\Vehicle`.
 */
class VehicleSearch extends Vehicle
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'reminder', 'type', 'status'], 'integer'],
            [['name', 'photo', 'registration_number', 'vin_number', 'inspection_date', 'oc_date', 'create_time', 'update_time', 'info', 'description'], 'safe'],
            [['capacity', 'volume', 'fuel_consumption', 'price_km', 'price_city', 'price_rent'], 'number'],
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
        $query = Vehicle::find();

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
            'capacity' => $this->capacity,
            'volume' => $this->volume,
            'fuel_consumption' => $this->fuel_consumption,
            'inspection_date' => $this->inspection_date,
            'oc_date' => $this->oc_date,
            'price_km' => $this->price_km,
            'price_city' => $this->price_city,
            'reminder' => $this->reminder,
            'price_rent' => $this->price_rent,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'registration_number', $this->registration_number])
            ->andFilterWhere(['like', 'vin_number', $this->vin_number])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
