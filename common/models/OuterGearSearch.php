<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OuterGear;

/**
 * OuterGearSearch represents the model behind the search form about `common\models\OuterGear`.
 */
class OuterGearSearch extends OuterGear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'status', 'type', 'category_id', 'sort_order'], 'integer'],
            [['name', 'brightness', 'info', 'photo', 'create_time', 'update_time', 'company_name'], 'safe'],
            [['power_consumption', 'width', 'height', 'volume', 'depth', 'weight', 'price', 'selling_price'], 'number'],
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
        $query = OuterGear::find();

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
            'quantity' => $this->quantity,
            'power_consumption' => $this->power_consumption,
            'status' => $this->status,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'width' => $this->width,
            'height' => $this->height,
            'volume' => $this->volume,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'price' => $this->price,
            'selling_price' => $this->selling_price,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'brightness', $this->brightness])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        return $dataProvider;
    }
}
