<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Gear;

/**
 * GearSearch represents the model behind the search form about `common\models\Gear`.
 */
class GearSearch extends Gear
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active', 'quantity', 'available', 'brightness', 'power_consumption', 'status', 'type', 'category_id', 'width', 'height', 'volume', 'depth', 'weight', 'weight_case', 'group_id', 'visible_in_warehouse', 'visible_in_offer'], 'integer'],
            [['name', 'info', 'photo', 'create_time', 'update_time'], 'safe'],
            [['price'], 'number'],
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
        $query = Gear::find();

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

        if ((isset($this->category_id))&&($this->category_id!=""))
        {
            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($this->category_id);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$this->category_id], $ids);
            $query->andFilterWhere(['category_id' => $categoryIds]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'quantity' => $this->quantity,
            'available' => $this->available,
            'brightness' => $this->brightness,
            'power_consumption' => $this->power_consumption,
            'status' => $this->status,
            'type' => $this->type,
            
            'width' => $this->width,
            'height' => $this->height,
            'volume' => $this->volume,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'weight_case' => $this->weight_case,
            'group_id' => $this->group_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'price' => $this->price,
            'active' => $this->active,
            'visible_in_warehouse' => $this->visible_in_warehouse,
            'visible_in_offer' => $this->visible_in_offer,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'photo', $this->photo]);

        return $dataProvider;
    }
}
