<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use common\models\Purchase;

/**
 * common\models\PurchaseSearch represents the model behind the search form about `common\models\Purchase`.
 */
 class PurchaseSearch extends Purchase
{
    /**
     * @inheritdoc
     */

    public $year;
    public $month;
    public $event;

    public function rules()
    {
        return [
            [['id', 'user_id', 'purchase_type_id'], 'integer'],
            [['section', 'code', 'description', 'year', 'month', 'event'], 'safe'],
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
        $query = Purchase::find()->where(['group_id'=>null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['datetime'=>SORT_DESC]],
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (empty($this->year) == false)
        {
            $query->andWhere([
               'YEAR(datetime)'=>$this->year
            ]);
        }

        if (empty($this->month) == false)
        {
            $query->andWhere([
               'MONTH(datetime)'=>$this->month
            ]);
        }

        if (empty($this->event) == false)
        {
            $ids = ArrayHelper::map(Event::find()->where(['LIKE', 'name', $this->event])->orWhere(['LIKE', 'code', $this->event])->asArray()->all(), 'id', 'id');
            $p_ids = ArrayHelper::map(PurchaseEvent::find()->where(['event_id'=>$ids])->asArray()->all(), 'purchase_id', 'purchase_id');
            $query->andWhere(['id'=>$p_ids]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'price' => $this->price,
            'purchase_type_id' => $this->purchase_type_id,
        ]);

        $query->andFilterWhere(['like', 'section', $this->section])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
