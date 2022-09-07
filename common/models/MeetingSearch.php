<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Meeting;

/**
 * MeetingSearch represents the model behind the search form about `common\models\Meeting`.
 */
class MeetingSearch extends Meeting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'contact_id', 'status', 'type', 'reminder', 'remind_sms', 'remind_email', 'remind_push', 'remind_all', 'remind_owner', 'remind_company', 'created_by'], 'integer'],
            [['name', 'start_time', 'end_time', 'description', 'create_time', 'update_time', 'location'], 'safe'],
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
        $query = Meeting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['start_time'=>SORT_DESC]]
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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'customer_id' => $this->customer_id,
            'contact_id' => $this->contact_id,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'reminder' => $this->reminder,
            'remind_sms' => $this->remind_sms,
            'remind_email' => $this->remind_email,
            'remind_push' => $this->remind_push,
            'remind_all' => $this->remind_all,
            'remind_owner' => $this->remind_owner,
            'remind_company' => $this->remind_company,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }
}
