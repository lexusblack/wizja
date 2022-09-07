<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Offer;

/**
 * OfferSearch represents the model behind the search form about `common\models\Offer`.
 */
class OfferSearch extends Offer
{
    /**
     * @inheritdoc
     */

    public $dateRange;
    public $year;
    public $month;
    public $searchType;

    public $useRange=0;

    public function rules()
    {
        return [
            [['id', 'customer_id', 'location_id', 'manager_id'], 'integer'],
            [['name', 'term_from', 'term_to', 'page', 'offer_date', 'comment', 'event_start', 'event_end', 'packing_start', 'packing_end', 'montage_start', 'montage_end', 'readiness_start', 'readiness_end', 'practice_start', 'practice_end', 'disassembly_start', 'disassembly_end', 'create_time', 'update_time', 'status'], 'safe'],
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
        $query = Offer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['offer_date'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if (($this->id!="")||($this->name!=""))
        {
            $this->year=null;
        }
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
            if ($this->searchType == 1)
            {
                $query->andFilterCompare('event_start', '>='.$dateStart);
                $query->andFilterCompare('event_start', '<='.$dateEnd);
            }else{
                $query->andFilterCompare('offer_date', '>='.$dateStart);
                $query->andFilterCompare('offer_date', '<='.$dateEnd);
            }
                       
        }
        if ($this->status!=null)
        {
            $query->andFilterWhere(['status'=>$this->status]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'event_id' => $this->event_id,
            'rent_id' => $this->rent_id,
            'customer_id' => $this->customer_id,
            'location_id' => $this->location_id,
            'manager_id' => $this->manager_id,
            'event_start' => $this->event_start,
            'event_end' => $this->event_end,
            'packing_start' => $this->packing_start,
            'packing_end' => $this->packing_end,
            'montage_start' => $this->montage_start,
            'montage_end' => $this->montage_end,
            'readiness_start' => $this->readiness_start,
            'readiness_end' => $this->readiness_end,
            'practice_start' => $this->practice_start,
            'practice_end' => $this->practice_end,
            'disassembly_start' => $this->disassembly_start,
            'disassembly_end' => $this->disassembly_end,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'term_from', $this->term_from])
            ->andFilterWhere(['like', 'term_to', $this->term_to])
            ->andFilterWhere(['like', 'page', $this->page])
            ->andFilterWhere(['like', 'offer_date', $this->offer_date])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
