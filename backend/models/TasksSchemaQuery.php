<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[\app\models\TasksSchema]].
 *
 * @see \app\models\TasksSchema
 */
class TasksSchemaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\TasksSchema[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\TasksSchema|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
