<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventOuterGearModel;
use common\helpers\ArrayHelper;

/**
 * common\models\EventOterGearSearch represents the model behind the search form about `common\models\EventOuterGear`.
 */
 class EventOuterGearModelSearch extends EventOuterGearModel
{
    /**
     * @inheritdoc
     */
    public $company;
    public $gear_category_id;

    public function rules()
    {
        return [
            [['event_id', 'outer_gear_model_id', 'quantity', 'resolved', 'gear_category_id'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
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
        $query = EventOuterGearModel::find();
         $query->joinWith(['outerGearModel']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['update_time'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->gear_category_id)
        {
            $categoryIds = [];
            $ids = [];
            $tmpCat = GearCategory::findOne($this->gear_category_id);

            if ($tmpCat !== null)
            {
                $ids = $tmpCat->children()->column();
            }

            $categoryIds = array_merge([$this->gear_category_id], $ids);
            $gear_ids = ArrayHelper::map(OuterGearModel::find()->where(['category_id'=>$categoryIds])->asArray()->all(), 'id', 'id');
            $query->andWhere(['IN', 'outer_gear_model_id', $gear_ids]);
        }
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'outer_gear_model_id' => $this->outer_gear_model_id,
            'quantity' => $this->quantity,
            'resolved'=>$this->resolved
            ])
        ->andFilterWhere(['like', 'start_time', $this->start_time])
        ->andFilterWhere(['like', 'end_time', $this->end_time])
        ;

        return $dataProvider;
    }
}
