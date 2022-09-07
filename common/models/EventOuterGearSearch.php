<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventOuterGear;
use common\helpers\ArrayHelper;
use yii\db\Expression;

/**
 * common\models\EventOterGearSearch represents the model behind the search form about `common\models\EventOuterGear`.
 */
 class EventOuterGearSearch extends EventOuterGear
{
    /**
     * @inheritdoc
     */
    public $company;
    public $gear_category_id;
    public $outer_gear_name;
    public $year;
    public $month;

    public function rules()
    {
        return [
            [['event_id', 'outer_gear_id', 'quantity', 'discount', 'type', 'planned', 'order_id', 'user_id', 'gear_category_id'], 'integer'],
            [['start_time', 'end_time', 'reception_time', 'return_time', 'company', 'description', 'outer_gear_name', 'year', 'month'], 'safe'],
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
        $query = EventOuterGear::find();
         $query->joinWith(['outerGear']);
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
        if ($this->outer_gear_name)
        {
            $ogm_ids = ArrayHelper::map(OuterGearModel::find()->where(['like', 'name', $this->outer_gear_name])->asArray()->all(), 'id', 'id');
            $og_ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id' => $ogm_ids])->asArray()->all(), 'id', 'id');

            $query->andWhere(['IN', 'outer_gear_id', $og_ids]);
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
            $gear_ids = ArrayHelper::map(OuterGear::find()->where(['category_id'=>$categoryIds])->asArray()->all(), 'id', 'id');
            $query->andWhere(['IN', 'outer_gear_id', $gear_ids]);
        }

        if (empty($this->year) == false)
        {
            $query->andWhere([
                'or',
                new Expression('YEAR(start_time)=:year'),
                new Expression('YEAR(end_time)=:year'),
            ],[
                ':year'=>$this->year,
            ]);
        }

        if (empty($this->month) == false)
        {
            $query->andWhere([
                'or',
                new Expression('MONTH(start_time)=:month'),
                new Expression('MONTH(end_time)=:month'),
            ],[
                ':month'=>$this->month,
            ]);
        }
        $query->andFilterWhere([
            'event_id' => $this->event_id,
            'outer_gear_id' => $this->outer_gear_id,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'type' => $this->type,
            'planned' => $this->planned,
            'order_id' => $this->order_id,
            'price' => $this->price,
            'user_id' => $this->user_id
            ])
        ->andFilterWhere(['outer_gear.company_id'=>$this->company])
        ->andFilterWhere(['like', 'start_time', $this->start_time])
        ->andFilterWhere(['like', 'end_time', $this->end_time])
        ->andFilterWhere(['like', 'description', $this->description])
        ->andFilterWhere(['like', 'reception_time', $this->reception_time])
        ->andFilterWhere(['like', 'return_time', $this->return_time])
        ;
        if (!$this->order_id)
            $query->andWhere(['is', 'order_id', null]);

        return $dataProvider;
    }
}
