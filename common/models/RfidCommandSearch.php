<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RfidCommand;

/**
 * common\models\RfidCommandSearch represents the model behind the search form about `common\models\RfidCommand`.
 */
 class RfidCommandSearch extends RfidCommand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['reader', 'command', 'content', 'create_time', 'done_time'], 'safe'],
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
        $query = RfidCommand::find();

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
            'status' => $this->status,
            'create_time' => $this->create_time,
            'done_time' => $this->done_time,
        ]);

        $query->andFilterWhere(['like', 'reader', $this->reader])
            ->andFilterWhere(['like', 'command', $this->command])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
