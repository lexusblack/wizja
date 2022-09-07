<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearModel;

/**
 * common\models\GearModelSearch represents the model behind the search form about `common\models\GearModel`.
 */
 class GearModelSearch extends GearModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'category_id', 'company_id'], 'integer'],
            [['name', 'info', 'photo', 'create_time', 'update_time'], 'safe'],
            [['brightness', 'power_consumption', 'width', 'height', 'volume', 'depth', 'weight', 'weight_case'], 'number'],
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
        $query = GearModel::find();

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
            'brightness' => $this->brightness,
            'power_consumption' => $this->power_consumption,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'company_id' => $this->company_id,
            'width' => $this->width,
            'height' => $this->height,
            'volume' => $this->volume,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'weight_case' => $this->weight_case,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'photo', $this->photo]);

        return $dataProvider;
    }
}
