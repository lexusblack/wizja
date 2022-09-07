<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * ExpenseSearch represents the model behind the search form about `common\models\Expense`.
 */
class ExpenseSearch extends Expense
{
    public $dateRange;
    public $dateStart;
    public $dateEnd;
    public $late;
    public $is_event;
    public $useRange = 0;
        public $pm;
    public $q;
    public $qOptions;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'discount', 'notes', 'documents', 'paid',  'year', 'month', 'day',  'is_event', 'pm'], 'integer'],
            [['name', 'code', 'unit', 'type', 'classification', 'description', 'tags', 'create_time', 'update_time', 'number', 'date'], 'safe'],
            [['netto', 'brutto', 'lumpcode', 'count', 'total', 'tax', 'alreadypaid'], 'number'],
            [['expense_type','customer_id','dateRange', 'dateStart', 'dateEnd', 'useRange'], 'safe'],
            [['q', 'qOptions'], 'safe'],
            [['eventIds'], 'each', 'rule'=>['integer']],
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
        $query = Expense::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['date'=>SORT_DESC]]
        ]);

        $this->load($params);
        if(is_array($this->eventIds)==false && empty($this->eventIds)==false)
        {
            $this->eventIds = [$this->eventIds];
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->useRange == 1)
        {
//            $tmp = explode(' - ', $this->dateRange);
//            $this->dateStart = $tmp[0];
//            $this->dateEnd = $tmp[1];
            $query->andFilterCompare('expense.date', '>='.$this->dateStart);
            $query->andFilterCompare('expense.date', '<='.$this->dateEnd);
            $this->month= null;
            $this->year = null;

        }

        if ($this->month==0)
            $this->month=null;

        $query = $this->addSearch($query);

        // grid filtering conditions
        $query->andFilterWhere([
            'expense.id' => $this->id,
            'expense.netto' => $this->netto,
            'expense.brutto' => $this->brutto,
            'expense.alreadypaid' => $this->alreadypaid,
            'expense.lumpcode' => $this->lumpcode,
            'expense.discount' => $this->discount,
            'expense.notes' => $this->notes,
            'expense.documents' => $this->documents,
            'expense.create_time' => $this->create_time,
            'expense.update_time' => $this->update_time,
            'expense.count' => $this->count,
            'expense.paid'=>$this->paid,
            'expense.customer_id'=>$this->customer_id,
            'expense.day' => $this->day,
            'expense.month' => $this->month,
            'expense.year' => $this->year,
            'expense.expense_type' => $this->expense_type,
        ]);
        if ((empty($this->eventIds)==false)||($this->pm!=null))
        {
            $query->innerJoinWith('events');
        }
        if (empty($this->eventIds)==false)
        {
            
            $query->andWhere([
                'event.id'=>$this->eventIds,
            ]);
        }
        if ($this->pm!=null)
        {
            
            $query->andWhere([
                'event.manager_id'=>$this->pm,
            ]);
        }

        if ($this->is_event==1)
        {
            //przypisany do eventu
            $ids = ArrayHelper::map(EventExpense::find()->asArray()->all(), 'expense_id', 'expense_id');
            $query->andWhere(['expense.id'=>$ids]);
        }

        if ($this->is_event==2)
        {
            //nieprzypisany do eventu
            $ids = ArrayHelper::map(EventExpense::find()->where(['IS NOT', 'expense_id', null])->asArray()->all(), 'expense_id', 'expense_id');

            $query->andWhere(['NOT IN', 'expense.id', $ids]);
        }

        $query->andFilterWhere(['like', 'expense.name', $this->name])
            ->andFilterWhere(['like', 'expense.ode', $this->code])
            ->andFilterWhere(['like', 'expense.unit', $this->unit])
            ->andFilterWhere(['like', 'expense.classification', $this->classification])
            ->andFilterWhere(['like', 'expense.description', $this->description])
            ->andFilterWhere(['like', 'expense.tags', $this->tags])
            ->andFilterWhere(['like', 'expense.number', $this->number])
            ->andFilterWhere(['like', 'expense.date', $this->date])
            ->andFilterWhere(['like', 'expense.tax', $this->tax])
            ->andFilterWhere(['like', 'expense.total', $this->total]);
        if ($this->late)
        {
            $query->andWhere(['paid'=>0])->andWhere(['<', 'paymentdate', date('Y-m-d')]);
        }

        //echo $query->createCommand()->getRawSql();

        return $dataProvider;
    }

    /**
     * @param $query ActiveQuery;
     * @return mixed
     */
    protected function addSearch($query)
    {
        if (empty($this->q) == false)
        {
            $newQuery = new ActiveQuery(static::className());
            $keywords = preg_split('/[\s,;]+/', $this->q);
            /*
            foreach ($this->qOptions as $option)
            {
                switch ($option)
                {
                    case 'name': //item name
                        $newQuery->joinWith('expenseContents');
                        $newQuery->joinWith('expenseContentRates');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'expense_content.name', $keyword]);
                            $newQuery->orWhere(['like', 'expense_content_rate.name', $keyword]);
                        }

                        break;
                    case 'customer': //item name
                        $newQuery->joinWith('customer');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'customer.name', $keyword]);
                        }
                        break;
                    case 'nip': //item name
                        $newQuery->joinWith('customer');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'customer.nip', $keyword]);
                        }
                        break;
                    case 'event': //item name
                        $newQuery->joinWith('event');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'event.name', $keyword]);
                        }
                        break;
                    case 'number':
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'number', $keyword]);
                        }
                        break;
                    case 'date':
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'date', $keyword]);
                        }
                        break;


                }


            }

            */
                        $newQuery->joinWith('expenseContents');
                        $newQuery->joinWith('expenseContentRates');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'expense_content.name', $keyword]);
                            $newQuery->orWhere(['like', 'expense_content_rate.name', $keyword]);
                        }
                        $newQuery->joinWith('customer');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'customer.name', $keyword]);
                        }
                        $newQuery->joinWith('customer');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'customer.nip', $keyword]);
                        }
                        //$newQuery->joinWith('event');
                        foreach ($keywords as $keyword)
                        {
                            //$newQuery->orWhere(['like', 'event.name', $keyword]);
                        }
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'number', $keyword]);
                        }
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'date', $keyword]);
                        }
            if(is_array($newQuery->joinWith) == true)
            {
                $with = ArrayHelper::merge($query->joinWith, $newQuery->joinWith);
                $query->joinWith = $with;
            }
            $query->andWhere($newQuery->where);

        }


        return $query;
    }

    public function attributeLabels()
    {
        $labels = [
            'q'=>Yii::t('app', 'Szukana fraza'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }
}
