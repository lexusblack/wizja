<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;

/**
 * common\models\LocationSearch represents the model behind the search form about `common\models\Location`.
 */
 class LocationSearch extends Location
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'owner_id', 'stars', 'province_id', 'beds', 'biggest_room', 'location_type_id'], 'integer'],
            [['name', 'address', 'city', 'zip', 'country', 'info', 'create_time', 'update_time', 'travel_time', 'manager_phone', 'electrician_phone', 'photo', 'video', 'description', 'website', 'email'], 'safe'],
            [['latitude', 'longitude', 'distance', 'rent_price', 'public'], 'number'],
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
        $query = Location::find();

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
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'type' => $this->type,
            'status' => $this->status,
            'distance' => $this->distance,
            'rent_price' => $this->rent_price,
            'owner_id' => $this->owner_id,
            'stars' => $this->stars,
            'province_id' => $this->province_id,
            'location_type_id' => $this->location_type_id,
            'public' => $this->public,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'travel_time', $this->travel_time])
            ->andFilterWhere(['like', 'manager_phone', $this->manager_phone])
            ->andFilterWhere(['like', 'electrician_phone', $this->electrician_phone])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'video', $this->video])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['>=', 'beds', $this->beds])
            ->andFilterWhere(['>=', 'biggest_room', $this->biggest_room])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
