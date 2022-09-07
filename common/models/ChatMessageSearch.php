<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ChatMessage;

/**
 * common\models\ChatMessageSearch represents the model behind the search form about `common\models\ChatMessage`.
 */
 class ChatMessageSearch extends ChatMessage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_from', 'user_to', 'chat_id', 'read'], 'integer'],
            [['create_time', 'text'], 'safe'],
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
        $query = ChatMessage::find();

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
            'user_from' => $this->user_from,
            'user_to' => $this->user_to,
            'chat_id' => $this->chat_id,
            'create_time' => $this->create_time,
            'read' => $this->read,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
