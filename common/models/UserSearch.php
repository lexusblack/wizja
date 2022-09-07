<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    public $skillId;
    public $departmentId;
    public $superuser;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'role', 'status', 'type', 'rate_type', 'superuser'], 'integer'],
            [['skillId', 'departmentId'], 'safe'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'create_time', 'update_time', 'first_name', 'last_name', 'last_visit', 'photo', 'birth_date', 'pesel', 'id_card', 'phone'], 'safe'],
            [['rate_amount'], 'number'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['last_name'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->superuser)
        {
            $groups_super_user = \common\helpers\ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
            $ids = \common\helpers\ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id');
            if ($this->superuser==1)
            {
                $query->andWhere(['id'=>$ids]);
            }
            if ($this->superuser==2)
            {
                $query->andWhere(['NOT IN','id',$ids]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role' => $this->role,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'last_visit' => $this->last_visit,
            'birth_date' => $this->birth_date,
            'type' => $this->type,
            'rate_type' => $this->rate_type,
            'rate_amount' => $this->rate_amount,
        ]);

        $query->joinWith('userDepartments');
        $query->andFilterWhere([
            'user_department.department_id'=>$this->departmentId,
        ]);

        $query->joinWith('userSkills');
        $query->andFilterWhere([
            'user_skill.skill_id'=>$this->skillId,
        ]);


        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'pesel', $this->pesel])
            ->andFilterWhere(['like', 'id_card', $this->id_card])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
