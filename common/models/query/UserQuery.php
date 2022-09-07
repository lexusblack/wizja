<?php
namespace common\models\query;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    public function forRole()
    {
        $role = \Yii::$app->user->identity->role;
        $query = $this->andWhere(['<', 'role', $role]);
        return $query;
    }
}