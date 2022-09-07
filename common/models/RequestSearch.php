<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Request;


 class RequestSearch extends Request
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'type', 'priority'], 'integer'],
            [['name', 'create_time', 'company_id', 'username'], 'safe'],
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
        $query = Request::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'priority' => $this->priority,
            'type' => $this->type,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'create_time', $this->create_time])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    public function searchByCompany($params)
    {
        $query = Request::find()->where(['company_id'=>Yii::$app->params['companyID']]);
        if (!Yii::$app->user->can("RequestAll"))
        {
            $query->andWhere(['mail'=>Yii::$app->user->identity->email]);
        }
        //$query->andWhere(['mail'=>Yii::$app->user->identity->email]);
        //echo $query->createCommand()->getRawSql();
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

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'priority' => $this->priority,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'create_time', $this->create_time])
        ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
