<?php

namespace common\models;

use common\helpers\ArrayHelper;
use common\widgets\PageSizeWidget;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * InvoiceSearch represents the model behind the search form about `common\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{

    public $dateRange;
    public $dateStart;
    public $dateEnd;
    public $late;
    public $useRange=0;
    public $types;
    public $q;
    public $qOptions;

    public $pm;


    public $statut2;
    public $statut3;
    public $statut4;
    public $statut5;
    public $statut6;
    public $statut7;
    public $statut8;
    public $statut9;
    public $statut10;
        public $statut1;

    public $ownerCode;
    public $manager;
    public $searchType;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'external_id', 'disposaldate_empty', 'period', 'number', 'day', 'month', 'year', 'semitemplatenumber', 'corrections', 'template', 'auto_send', 'schema_bill', 'schema_canceled', 'signed', 'notes', 'documents',  'paid', 'late', 'pm'], 'integer'],
            [['paymentmethod', 'paymentdate', 'paymentstate', 'disposaldate_format', 'disposaldate', 'date', 'fullnumber', 'type', 'correction_type', 'currency', 'currency_label', 'currency_date', 'description', 'header', 'footer', 'user_name', 'schema', 'register_description', 'hash', 'warehouse_type', 'tags', 'price_type', 'create_time','customer_id', 'series_id', 'event_id', 'owner_id',  'update_time', 'ownerCode'], 'safe'],
            [['total', 'total_composed', 'alreadypaid', 'alreadypaid_initial', 'remaining', 'currency_exchange', 'price_currency_exchange', 'good_price_group_currency_exchange', 'netto', 'tax'], 'number'],
            [['dateRange', 'dateStart', 'dateEnd', 'useRange', 'types', 'statut2', 'statut3', 'statut4', 'statut5', 'statut6', 'statut7', 'statut1', 'statut8', 'statut9', 'statut10', 'manager', 'searchType'], 'safe'],
            [['q', 'qOptions'], 'safe'],
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
        $query = Invoice::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['date'=>SORT_DESC]]
        ]);
                        if (!$this->searchType)
                $this->searchType = 1;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->useRange==1)
        {
            if ($this->searchType == 1)
            {
            $query->andFilterCompare('invoice.date', '>='.$this->dateStart);
            $query->andFilterCompare('invoice.date', '<='.$this->dateEnd);
            }else{
            $query->andFilterCompare('invoice.paymentdate', '>='.$this->dateStart);
            $query->andFilterCompare('invoice.paymentdate', '<='.$this->dateEnd);
            }

            $this->month= null;
            $this->year = null;

        }else{
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
                    $query->andFilterCompare('invoice.date', '>='.$dateStart);
                    $query->andFilterCompare('invoice.date', '<='.$dateEnd);
            }else{
                    $query->andFilterCompare('invoice.paymentdate', '>='.$dateStart);
                    $query->andFilterCompare('invoice.paymentdate', '<='.$dateEnd);
            }
            $this->month= null;
            $this->year = null;
        }
        if ($this->month==0)
                 $this->month= null;
       

        $query = $this->addSearch($query);

	    if ($this->ownerCode != null)
	    {
		    $owners = [];
            $owners[] = 'or';
            foreach ($this->ownerCode as $code)
            {
                $tmp = explode('_', $code);
                if (sizeof($tmp) == 2)
                {
                    $owners[] = ['and', ['invoice.owner_type'=>$tmp[0]], ['invoice.owner_id'=>$tmp[1]]];
                }
            }
            $query->andWhere($owners);
	    }
        if ($this->manager)
        {
            $ids = ArrayHelper::map(Event::find()->where(['manager_id'=>$this->manager])->asArray()->all(), 'id', 'id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }
        if ($this->statut1)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut1])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut2)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut2])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut3)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut3])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut4)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut4])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut5)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut5])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut6)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut6])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut7)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut7])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut8)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut8])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut9)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut9])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->statut10)
        {
            $ids = ArrayHelper::map(EventASResult::find()->where(['event_additional_statut_name_id'=>$this->statut10])->asArray()->all(), 'event_id', 'event_id');
            $query->andWhere(['IN', 'invoice.owner_id', $ids])->andWhere(['invoice.owner_type'=>1]);
        }

        if ($this->paid)
        {
            if ($this->paid==1)
                        $query->andFilterWhere(['invoice.paid'=>$this->paid]);
                    else
                        $query->andFilterWhere(['<>', 'invoice.paid', 1]);
        }

        if ($this->pm != null)
        {
            $e = \common\helpers\ArrayHelper::map(Event::find()->where(['manager_id'=>$this->pm])->asArray()->all(), 'id', 'id');
            $query->andWhere(['owner_id'=>$e, 'owner_type'=>1]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'invoice.id' => $this->id,
            'invoice.external_id' => $this->external_id,
            'invoice.paymentdate' => $this->paymentdate,
            'invoice.disposaldate_empty' => $this->disposaldate_empty,
            'invoice.disposaldate' => $this->disposaldate,
            'invoice.date' => $this->date,
            'invoice.period' => $this->period,
            'invoice.total' => $this->total,
            'invoice.total_composed' => $this->total_composed,
            'invoice.alreadypaid' => $this->alreadypaid,
            'invoice.alreadypaid_initial' => $this->alreadypaid_initial,
            'invoice.remaining' => $this->remaining,
            'invoice.number' => $this->number,
            'invoice.day' => $this->day,
            'invoice.month' => $this->month,
            'invoice.year' => $this->year,
            'invoice.semitemplatenumber' => $this->semitemplatenumber,
            'invoice.corrections' => $this->corrections,
            'invoice.currency_exchange' => $this->currency_exchange,
            'invoice.currency_date' => $this->currency_date,
            'invoice.price_currency_exchange' => $this->price_currency_exchange,
            'invoice.good_price_group_currency_exchange' => $this->good_price_group_currency_exchange,
            'invoice.template' => $this->template,
            'invoice.auto_send' => $this->auto_send,
            'invoice.schema_bill' => $this->schema_bill,
            'invoice.schema_canceled' => $this->schema_canceled,
            'invoice.netto' => $this->netto,
            'invoice.tax' => $this->tax,
            'invoice.signed' => $this->signed,
            'invoice.notes' => $this->notes,
            'invoice.documents' => $this->documents,
            'invoice.create_time' => $this->create_time,
            'invoice.update_time' => $this->update_time,
            'invoice.customer_id' => $this->customer_id,

            'invoice.series_id'=>$this->series_id,
            'invoice.event_id'=>$this->event_id,
            'invoice.type'=>$this->type,
            'invoice.type'=>$this->types
        ]);



        $query->andFilterWhere(['like', 'invoice.paymentmethod', $this->paymentmethod])
            ->andFilterWhere(['like', 'invoice.paymentstate', $this->paymentstate])
            ->andFilterWhere(['like', 'invoice.disposaldate_format', $this->disposaldate_format])
            ->andFilterWhere(['like', 'invoice.fullnumber', $this->fullnumber])
//            ->andFilterWhere(['like', 'invoice.type', $this->type])
            ->andFilterWhere(['like', 'invoice.correction_type', $this->correction_type])
            ->andFilterWhere(['like', 'invoice.currency', $this->currency])
            ->andFilterWhere(['like', 'invoice.currency_label', $this->currency_label])
            ->andFilterWhere(['like', 'invoice.description', $this->description])
            ->andFilterWhere(['like', 'invoice.header', $this->header])
            ->andFilterWhere(['like', 'invoice.footer', $this->footer])
            ->andFilterWhere(['like', 'invoice.user_name', $this->user_name])
            ->andFilterWhere(['like', 'invoice.schema', $this->schema])
            ->andFilterWhere(['like', 'invoice.register_description', $this->register_description])
            ->andFilterWhere(['like', 'invoice.hash', $this->hash])
            ->andFilterWhere(['like', 'invoice.warehouse_type', $this->warehouse_type])
            ->andFilterWhere(['like', 'invoice.tags', $this->tags])
            ->andFilterWhere(['like', 'invoice.price_type', $this->price_type]);

        if ($this->late)
        {
            $query->andWhere(['<>', 'invoice.paid', 1])->andWhere(['<', 'paymentdate', date('Y-m-d')]);
        }
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

            /*foreach ($this->qOptions as $option)
            {
                switch ($option)
                {
                    case 'name': //item name
                        $newQuery->joinWith('invoiceContents');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'invoice_content.name', $keyword]);
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
                            $newQuery->orWhere(['like', 'fullnumber', $keyword]);
                        }
                        break;
                    case 'date':
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'date', $keyword]);
                        }
                        break;


                }


            }*/

                        $newQuery->joinWith('invoiceContents');
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'invoice_content.name', $keyword]);
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
                           // $newQuery->orWhere(['like', 'event.name', $keyword]);
                        }
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'fullnumber', $keyword]);
                        }
                        foreach ($keywords as $keyword)
                        {
                            $newQuery->orWhere(['like', 'date', $keyword]);
                        }






            if(is_array($newQuery->joinWith))
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
