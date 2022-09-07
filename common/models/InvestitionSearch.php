<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Investition;
use yii\db\Expression;

/**
 * common\models\InvestitionSearch represents the model behind the search form about `common\models\Investition`.
 */
 class InvestitionSearch extends Investition
{
    /**
     * @inheritdoc
     */
public $year;
public $month;

    public function rules()
    {
        return [
            [['id', 'quantity', 'year', 'month', 'expense_id', 'creator_id'], 'integer'],
            [['name', 'section', 'create_time'], 'safe'],
            [['price', 'total_price', 'vat'], 'number'],
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
        $query = Investition::find()->where([
                'group_id'=>null,
            ]);

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

        $query->andFilterWhere([
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'vat' => $this->vat,
            'expense_id' => $this->expense_id,
            'creator_id' => $this->creator_id,
            'create_time' => $this->create_time,
        ]);

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'section', $this->section]);

        return $dataProvider;
    }
}
