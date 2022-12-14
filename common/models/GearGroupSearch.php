<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearGroup;

/**
 * GearGroupSearch represents the model behind the search form about `common\models\GearGroup`.
 */
class GearGroupSearch extends GearGroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'width', 'height', 'depth', 'volume', 'weight'], 'integer'],
            [['name', 'create_time', 'update_time', 'warehouse', 'location'], 'safe'],
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
        $query = GearGroup::find();

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
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'type' => $this->type,
            'status' => $this->status,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'volume' => $this->volume,
            'weight' => $this->weight,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'warehouse', $this->warehouse])
            ->andFilterWhere(['like', 'location', $this->location]);

        return $dataProvider;
    }
}
