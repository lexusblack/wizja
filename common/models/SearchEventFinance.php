<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventFinance;

/**
 * SearchEventFinance represents the model behind the search form about `common\models\EventFinance`.
 */
class SearchEventFinance extends EventFinance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'event_id', 'project_done', 'invoice_issued', 'invoice_sent', 'status', 'type'], 'integer'],
            [['provision'], 'number'],
            [['invoice_number', 'create_time', 'update_time'], 'safe'],
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
        $query = EventFinance::find();

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
            'event_id' => $this->event_id,
            'provision' => $this->provision,
            'project_done' => $this->project_done,
            'invoice_issued' => $this->invoice_issued,
            'invoice_sent' => $this->invoice_sent,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number]);

        return $dataProvider;
    }
}
