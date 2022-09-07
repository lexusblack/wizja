<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * CustomerSearch represents the model behind the search form about `common\models\Customer`.
 */
class CustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'customer', 'supplier', 'customer_type_id'], 'integer'],
            [['company', 'name', 'address', 'city', 'zip', 'phone', 'email', 'info', 'create_time', 'update_time', 'logo', 'nip', 'bank_account', 'next_date', 'last_date', 'groups'], 'safe'],
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
        $query = Customer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if (isset($this->groups))
        {
            if ($this->groups!="")
                $query->leftJoin('customer_group', 'customer_group.customer_id = customer.id')->andWhere(['customer_group.customer_type_id'=>$this->groups]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'type' => $this->type,
            'status' => $this->status,
            'customer' => $this->customer,
            'supplier' => $this->supplier,
            'customer_type_id' => $this->customer_type_id,
        ]);



        $query->andFilterWhere(['like', 'company', $this->company])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'info', $this->info])
            ->andFilterWhere(['like', 'logo', $this->logo])
            ->andFilterWhere(['like', 'nip', $this->nip])
            ->andFilterWhere(['like', 'bank_account', $this->bank_account]);

        return $dataProvider;
    }
}
