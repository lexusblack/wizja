<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\models\GearService;

/**
 * GearServiceSearch represents the model behind the search form about `common\models\GearService`.
 */
class GearServiceSearch extends GearService
{
    /**
     * @inheritdoc
     */

    public $year;
    public $month;
    public $gear_item_name;
    public $category_id;
    public $history;
    public function rules()
    {
        return [
            [['id', 'gear_item_id', 'status', 'type', 'year', 'month'], 'integer'],
            [['description', 'create_time', 'update_time', 'status_time', 'info', 'gear_item_name', 'category_id', 'history'], 'safe'],
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
    public function search($params, $pagination = true)
    {
        $query = GearService::find()->joinWith(['gearItem'], true, 'INNER JOIN')->where(['gear_item.active'=>1]);

        // add conditions that should always apply here
        if ($pagination)
        {
            $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        }else{
            $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>$pagination
        ]);
        }
        

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $item_ids = [];

        if ((isset($this->history))&&($this->history!=""))
        {
            $names = explode(" ", $this->history);
            if (count($names)<2)
                $users =  ArrayHelper::map(User::find()->where(['like', 'first_name', $this->history])->orwhere(['like', 'last_name', $this->history])->asArray()->all(), 'id', 'id');
            else
                $users =  ArrayHelper::map(User::find()->where(['or', ['like', 'first_name', $names[0]], ['like', 'last_name', $names[0]]])->andWhere(['or', ['like', 'first_name', $names[1]], ['like', 'last_name', $names[1]]])->asArray()->all(), 'id', 'id');
            $ids = ArrayHelper::map(GearServiceHistory::find()->where(['user_id'=>$users])->asArray()->all(), 'gear_service_id', 'gear_service_id');
            $query->andWhere(['IN', 'gear_service.id', $ids]);
        }

        if ((isset($this->gear_item_name))&&($this->gear_item_name!=""))
        {
            $ids = ArrayHelper::map(Gear::find()->where(['like', 'name', $this->gear_item_name])->asArray()->all(), 'id', 'id');
            $item_ids = ArrayHelper::map(GearItem::find()->where(['in', 'gear_id', $ids])->orWhere(['like', 'name', $this->gear_item_name])->asArray()->all(), 'id', 'id');
            if ($item_ids)
                $query->andWhere(['IN', 'gear_item_id', $item_ids]);
            else
                $query->andWhere(['gear_item_id'=>0]);
        }

        if ((isset($this->category_id))&&($this->category_id>0))
        {
            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($this->category_id);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$this->category_id], $ids);
            $ids = ArrayHelper::map(Gear::find()->where(['category_id'=>$categoryIds])->asArray()->all(), 'id', 'id');
            $item_ids = ArrayHelper::map(GearItem::find()->where(['in', 'gear_id', $ids])->asArray()->all(), 'id', 'id');
            if ($item_ids)
                $query->andWhere(['IN', 'gear_item_id', $item_ids]);
            else
                $query->andWhere(['gear_item_id'=>0]);

        }
        

        // grid filtering conditions
        $query->andFilterWhere([
            'gear_item_id' => $this->gear_item_id,

            'gear_service.status' => $this->status,
            'type' => $this->type,
        ]);

        if (empty($this->year) == false)
        {
            $query->andWhere(
                ['YEAR(gear_service.update_time)'=>$this->year]
            );
        }

        if (empty($this->month) == false)
        {
            $query->andWhere(
                ['MONTH(gear_service.update_time)'=>$this->month]
            );
        }

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'gear_service.create_time', $this->create_time])
            ->andFilterWhere(['like', 'gear_service.update_time', $this->update_time]);
                        

        $dataProvider->sort->defaultOrder = [
//            'status_time'=>SORT_DESC,
            'create_time'=>SORT_DESC,
        ];
        return $dataProvider;
    }
}
