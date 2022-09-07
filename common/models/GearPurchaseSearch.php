<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearPurchase;

/**
 * common\models\GearPurchaseSearch represents the model behind the search form about `common\models\GearPurchase`.
 */
 class GearPurchaseSearch extends GearPurchase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gear_id', 'quantity', 'customer_id', 'expense_id', 'user_id'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['datetime'], 'safe'],
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
        $query = GearPurchase::find();

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
            'gear_id' => $this->gear_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'datetime' => $this->datetime,
            'customer_id' => $this->customer_id,
            'expense_id' => $this->expense_id,
            'user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}
