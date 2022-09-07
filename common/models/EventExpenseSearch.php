<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EventExpense;
use yii\helpers\ArrayHelper;

/**
 * EventExpenseSearch represents the model behind the search form about `common\models\EventExpense`.
 */
class EventExpenseSearch extends EventExpense
{
    /**
     * @inheritdoc
     */
    public $year;
    public $month;
    public $manager_id;
    public function rules()
    {
        return [
            [['id', 'event_id', 'department_id', 'customer_id', 'status', 'type', 'expense_id', 'manager_id'], 'integer'],
            [['name', 'invoice_nr', 'create_time', 'update_time'], 'safe'],
            [['amount', 'amount_customer', 'profit'], 'number'],
            [['section'], 'string'],
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
        $query = EventExpense::find()->where([
                'group_id'=>null,
            ]);
        if ($this->year){
            if ($this->month>0)
            {
                $date = \DateTime::createFromFormat('Yn', $this->year.$this->month);
                $dateStart = $date->format('Y-m')."-01";
                $dateEnd = $date->format('Y-m-t');              
            }else{
                $date = \DateTime::createFromFormat('Y', $this->year);
                $dateStart = $date->format('Y-01-01');
                $dateEnd = $date->format('Y-12-31');                   
            }
            $query->andFilterCompare('create_time', '>='.$dateStart);
            $query->andFilterCompare('create_time', '<='.$dateEnd);           
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['create_time'=>SORT_DESC]]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->manager_id)
        {
            $ids = ArrayHelper::map(Event::find()->where(['manager_id'=>$this->manager_id])->all(), 'id', 'id');
            $query->andWhere(['IN', 'event_id', $ids]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'event_id' => $this->event_id,
            'department_id' => $this->department_id,
            'amount' => $this->amount,
            'amount_customer' => $this->amount_customer,
            'profit' => $this->profit,
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'section', $this->section])
            ->andFilterWhere(['like', 'invoice_nr', $this->invoice_nr]);

        if ($this->expense_id)
        {
            if ($this->expense_id==1)
                $query->andWhere(['is', 'expense_id', null]);
            if ($this->expense_id==2)
                $query->andWhere(['invoice_nr'=>""]);
             if ($this->expense_id==3)
             {
                $query->andWhere(['invoice_nr'=>""]); 
                $query->andWhere(['is', 'expense_id', null]);
             }
                          
        }
        return $dataProvider;
    }
}
