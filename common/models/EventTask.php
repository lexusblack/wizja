<?php

namespace common\models;

use Yii;
use \common\models\base\EventTask as BaseEventTask;

/**
 * This is the model class for table "event_task".
 */
class EventTask extends BaseEventTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'task_id'], 'required'],
            [['event_id', 'task_id'], 'integer']
        ]);
    }
	
    public function afterSave($insert, $changedAttributes)
    {
        
        $task = Task::findOne($this->task_id);
        $expense = new EventExpense();
        $expense->event_id = $task->event_id;
        $expense->task_id = $task->id;
        $expense->amount = 0;
        $expense->name = "[".Yii::t('app', 'Koszty')."] ".$task->title;
        $expense->info = "[".Yii::t('app', 'Koszty')."]";
        $expense->sections = [0=>"Scenografia"];

        $expense->save();

        $expense = new EventExpense();
        $expense->event_id = $task->event_id;
        $expense->task_id = $task->id;
        $expense->amount = 0;
        $expense->name = "[".Yii::t('app', 'Obsługa')."] ".$task->title;
        $expense->info = "[".Yii::t('app', 'Obsługa')."]";
        $expense->sections = [0=>"Scenografia"];
        $expense->save();
        parent::afterSave($insert, $changedAttributes);
    }
}
