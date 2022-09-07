<?php

namespace common\models;

use Yii;
use \common\models\base\Todolist as BaseTodolist;

/**
 * This is the model class for table "todolist".
 */
class Todolist extends BaseTodolist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public function getUndone()
    {
        return $this->hasMany(\common\models\Checklist::className(), ['todolist_id' => 'id'])->where(['done'=>0])->count();
    }
	
}
