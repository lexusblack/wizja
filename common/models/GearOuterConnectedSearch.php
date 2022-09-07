<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearOuterConnected;

/**
 * common\models\GearOuterConnectedSearch represents the model behind the search form about `common\models\GearOuterConnected`.
 */
 class GearOuterConnectedSearch extends GearOuterConnected
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gear_id', 'connected_id', 'quantity', 'checked', 'gear_quantity', 'in_offer'], 'integer'],
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
        $query = GearOuterConnected::find();

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
            'connected_id' => $this->connected_id,
            'quantity' => $this->quantity,
            'checked' => $this->checked,
            'gear_quantity' => $this->gear_quantity,
            'in_offer' => $this->in_offer,
        ]);

        return $dataProvider;
    }
}
