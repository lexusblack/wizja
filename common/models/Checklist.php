<?php

namespace common\models;
use Yii;
use \common\models\base\Checklist as BaseChecklist;

/**
 * This is the model class for table "checklist".
 */
class Checklist extends BaseChecklist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string'],
            [['user_id', 'done', 'priority'], 'integer'],
            [['deadline', 'create_time', 'update_time'], 'safe']
        ]);
    }

    public static function getUndone()
    {
        $count = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id, 'done'=>0])->count();
        return $count;
    }
    public static function getNoListUndone()
    {
        $count = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id, 'done'=>0])->andWhere(['is', 'todolist_id', null])->count();
        return $count;
    }	
    
}
