<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FreeOffer;

/**
 * common\models\FreeOfferSearch represents the model behind the search form about `common\models\FreeOffer`.
 */
 class FreeOfferSearch extends FreeOffer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'deal_type', 'user_id'], 'integer'],
            [['name', 'start_time', 'end_time', 'company', 'work_info', 'transport_info', 'money_info', 'skills', 'devices', 'own_device', 'user_mail', 'company_name'], 'safe'],
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
        $query = FreeOffer::find();

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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'city_id' => $this->city_id,
            'deal_type' => $this->deal_type,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'work_info', $this->work_info])
            ->andFilterWhere(['like', 'transport_info', $this->transport_info])
            ->andFilterWhere(['like', 'money_info', $this->money_info])
            ->andFilterWhere(['like', 'skills', $this->skills])
            ->andFilterWhere(['like', 'devices', $this->devices])
            ->andFilterWhere(['like', 'own_device', $this->own_device])
            ->andFilterWhere(['like', 'user_mail', $this->user_mail])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        return $dataProvider;
    }
}
