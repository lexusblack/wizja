<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CrossRental;

/**
 * common\models\CrossRentalSearch represents the model behind the search form about `common\models\CrossRental`.
 */
 class CrossRentalSearch extends CrossRental
{
    /**
     * @inheritdoc
     */
    public $category_id;
    public $name;

    public function rules()
    {
        return [
            [['id', 'gear_model_id', 'owner_gear_id', 'quantity', 'category_id'], 'integer'],
            [['owner', 'owner_name', 'owner_city', 'owner_country', 'create_time', 'update_time', 'name'], 'safe'],
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
        $query = CrossRental::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!$this->name)
        {
            if ($this->category_id){
                $categoryIds = [];
                $ids = [];
                $tmpCat = CRCategory::findOne($this->category_id);

                if ($tmpCat !== null)
                {
                    $ids = $tmpCat->children()->column();
                }

                $categoryIds = array_merge([$this->category_id], $ids);
                $query->where(['category_id'=>$categoryIds]);
            }
        }else{
            $gm_ids = \common\helpers\ArrayHelper::map(GearModel::find()->where(['like', 'name', $this->name])->asArray()->all(), 'id', 'id');
            $query->andWhere(['gear_model_id'=>$gm_ids]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'price' => $this->price,
            'owner_gear_id' => $this->owner_gear_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'owner', $this->owner])
            ->andFilterWhere(['like', 'owner_name', $this->owner_name])
            ->andFilterWhere(['like', 'owner_city', $this->owner_city])
            ->andFilterWhere(['like', 'owner_country', $this->owner_country])
            ->andFilterWhere(['>=', 'quantity', $this->quantity]);

        return $dataProvider;
    }
}
