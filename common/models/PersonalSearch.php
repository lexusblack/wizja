<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Personal;

/**
 * PersonalSearch represents the model behind the search form about `common\models\Personal`.
 */
class PersonalSearch extends Personal
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'repeat', 'reminder', 'user_id', 'status', 'typ', 'parent_id', 'remind_sms', 'remind_email', 'remind_push'], 'integer'],
            [['name', 'location', 'start_time', 'end_time', 'repeat_since', 'description', 'create_time', 'update_time'], 'safe'],
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
        $query = Personal::find();

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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'repeat' => $this->repeat,
            'repeat_since' => $this->repeat_since,
            'reminder' => $this->reminder,
            'user_id' => $this->user_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'status' => $this->status,
            'typ' => $this->typ,
            'parent_id' => $this->parent_id,
            'remind_sms' => $this->remind_sms,
            'remind_email' => $this->remind_email,
            'remind_push' => $this->remind_push,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
