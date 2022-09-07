<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Rent;
use yii\db\Expression;
use  backend\modules\permission\models\BasePermission;

/**
 * RentSearch represents the model behind the search form about `common\models\Rent`.
 */
class RentSearch extends Rent
{
    /**
     * @inheritdoc
     */

    public $year;
    public $month;
    public function rules()
    {
        return [
            [['customer_id', 'contact_id', 'status', 'type', 'reminder', 'invoice_status', 'payment_status', 'created_by', 'manager_id', 'year', 'month'], 'integer'],
            [['name', 'start_time', 'end_time', 'return_time', 'info', 'description', 'create_time', 'update_time', 'private_note', 'invoice_number'], 'safe'],
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
        $query = Rent::find();
        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventRents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Rent::find()->where(['or', ['created_by' => Yii::$app->user->id],['manager_id'=>Yii::$app->user->id]]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['start_time'=>SORT_DESC]]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'return_time' => $this->return_time,
            'customer_id' => $this->customer_id,
            'contact_id' => $this->contact_id,
            'status' => $this->status,
            'type' => $this->type,
            'reminder' => $this->reminder,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'invoice_status' => $this->invoice_status,
            'payment_status' => $this->payment_status,
            'created_by' => $this->created_by,
            'manager_id' => $this->manager_id,
        ]);

        $query->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'private_note', $this->private_note])
            ->andFilterWhere(['like', 'invoice_number', $this->invoice_number]);

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

        if (empty($this->name) == false)
        {
            $query->andWhere([
                'or',
                ['like', 'code', $this->name],
                ['like', 'name', $this->name]
            ]);
        }
        return $dataProvider;
    }
}
