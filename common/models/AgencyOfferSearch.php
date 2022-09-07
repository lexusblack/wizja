<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AgencyOffer;

/**
 * common\models\AgencyOfferSearch represents the model behind the search form about `common\models\AgencyOffer`.
 */
 class AgencyOfferSearch extends AgencyOffer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'contact_id', 'manager_id', 'location_id', 'event_id'], 'integer'],
            [['name', 'event_start', 'event_end', 'offer_date', 'payment_date', 'create_time', 'update_time'], 'safe'],
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
        $query = AgencyOffer::find();

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
            'customer_id' => $this->customer_id,
            'contact_id' => $this->contact_id,
            'manager_id' => $this->manager_id,
            'location_id' => $this->location_id,
            'event_start' => $this->event_start,
            'event_end' => $this->event_end,
            'offer_date' => $this->offer_date,
            'payment_date' => $this->payment_date,
            'event_id' => $this->event_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
