<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Note;
use common\helpers\ArrayHelper;

/**
 * OfferSearch represents the model behind the search form about `common\models\Offer`.
 */
class NoteSearch extends Note
{
    /**
     * @inheritdoc
     */

    public $dateRange;
    public $year;
    public $month;
    public $dateStart;
    public $dateEnd;
    public $useRange=0;

    public function rules()
    {
        return [
            [['user_id', 'event_id', 'rent_id', 'project_id', 'customer_id', 'note_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['datetime', 'year', 'month', 'dateStart', 'dateEnd', 'dateRange', 'useRange'], 'safe']
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
        $query = Note::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if (($this->event_id)||($this->rent_id)||($this->datetime))
            $this->year = null;
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
            $query->andFilterCompare('datetime', '>='.$dateStart);
                $query->andFilterCompare('datetime', '<='.$dateEnd);
                       
        }
        if ($this->customer_id!="")
        {
            $event_ids = ArrayHelper::map(Event::find()->where(['customer_id'=>$this->customer_id])->asArray()->all(), 'id', 'id');
            $rent_ids = ArrayHelper::map(Rent::find()->where(['customer_id'=>$this->customer_id])->asArray()->all(), 'id', 'id');
            $query->andWhere(['or', ['rent_id'=>$rent_ids], ['event_id'=>$event_ids],  ['customer_id' => $this->customer_id]]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'event_id' => $this->event_id,
            'rent_id' => $this->rent_id,
            
            'user_id' => $this->user_id,
            'type'=>$this->type
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);
         $query->andFilterWhere(['like', 'datetime', $this->datetime]);


        return $dataProvider;
    }
}
