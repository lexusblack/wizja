<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventMessage;

/**
 * EventMessageSearch represents the model behind the search form about `common\models\EventMessage`.
 */
class EventMessageSearch extends EventMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'event_id', 'push', 'sms', 'email', 'type', 'status'], 'integer'],
            [['title', 'content', 'create_time', 'update_time', 'recipients_push', 'recipients_sms', 'recipients_email', 'sent_time'], 'safe'],
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
        $query = EventMessage::find();

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
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'push' => $this->push,
            'sms' => $this->sms,
            'email' => $this->email,
            'sent_time' => $this->sent_time,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'recipients_push', $this->recipients_push])
            ->andFilterWhere(['like', 'recipients_sms', $this->recipients_sms])
            ->andFilterWhere(['like', 'recipients_email', $this->recipients_email]);

        return $dataProvider;
    }
}
