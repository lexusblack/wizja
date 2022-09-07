<?php

namespace common\models;

use Yii;
use \common\models\base\TaskNote as BaseTaskNote;

/**
 * This is the model class for table "task_note".
 */
class TaskNote extends BaseTaskNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['task_id', 'user_id'], 'integer'],
            [['text'], 'string'],
            [['create_time'], 'safe']
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            if ($this->task->event_id)
                Note::createNote(2, 'eventTaskComment', $this, $this->task->event_id);
            else
                Note::createNote(4, 'taskComment', $this, $this->id);
            Chat::sendTaskNote($this);
        }



    }
	
}
