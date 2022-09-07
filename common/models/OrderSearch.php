<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * common\models\OrderSearch represents the model behind the search form about `common\models\Order`.
 */
 class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'contact_id', 'confirm', 'user_id'], 'integer'],
            [['hash', 'create_at', 'update_at'], 'safe'],
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
        $query = Order::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['create_at'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'company_id' => $this->company_id,
            'contact_id' => $this->contact_id,
            'confirm' => $this->confirm,
            'update_at' => $this->update_at,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'hash', $this->hash]);
        $query->andFilterWhere(['like', 'create_at', $this->create_at]);

        return $dataProvider;
    }
}
