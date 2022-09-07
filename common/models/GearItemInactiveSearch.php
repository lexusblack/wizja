<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GearItem;

/**
 * GearItemSearch represents the model behind the search form about `common\models\GearItem`.
 */
class GearItemInactiveSearch extends GearItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'weight', 'width', 'height', 'depth', 'weight_case', 'height_case', 'depth_case', 'width_case', 'volume', 'lamp_hours', 'gear_id', 'status', 'type'], 'integer'],
            [['name', 'photo', 'warehouse', 'location', 'code', 'serial', 'info', 'test_date', 'tester', 'test_status', 'service', 'create_time', 'update_time', 'gear.name', 'gear.category.name'], 'safe'],
            [['purchase_price', 'refund_amount'], 'number'],
        ];
    }

    public function attributes() {
	    return array_merge(parent::attributes(), ['gear.name', 'gear.category.name']);
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
        $query = GearItem::find();

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
	    $query->andWhere(['gear_item.active'=>0])->andWhere(['<>', 'gear_item.name', '_ILOSC_SZTUK_']);

        $query->joinWith('gear')->leftJoin('gear_category', 'gear_category.id = gear.category_id');


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'weight' => $this->weight,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'weight_case' => $this->weight_case,
            'height_case' => $this->height_case,
            'depth_case' => $this->depth_case,
            'width_case' => $this->width_case,
            'volume' => $this->volume,
            'lamp_hours' => $this->lamp_hours,
            'purchase_price' => $this->purchase_price,
            'refund_amount' => $this->refund_amount,
            'test_date' => $this->test_date,
            'gear_id' => $this->gear_id,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'gear_item.name', $this->name])
            ->andFilterWhere(['like', 'gear_item.photo', $this->photo])
            ->andFilterWhere(['like', 'gear_item.warehouse', $this->warehouse])
            ->andFilterWhere(['like', 'gear_item.location', $this->location])
            ->andFilterWhere(['like', 'gear_item.code', $this->code])
            ->andFilterWhere(['like', 'gear_item.serial', $this->serial])
            ->andFilterWhere(['like', 'gear_item.info', $this->info])
            ->andFilterWhere(['like', 'gear_item.tester', $this->tester])
            ->andFilterWhere(['like', 'gear_item.test_status', $this->test_status])
            ->andFilterWhere(['like', 'gear_item.service', $this->service])
	        ->andFilterWhere(['like', 'gear.name', $this->getAttribute('gear.name')])
	        ->andFilterWhere(['like', 'gear_category.name', $this->getAttribute('gear.category.name')]);

        return $dataProvider;
    }
}
