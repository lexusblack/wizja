<?php

namespace common\models;

use Yii;
use \common\models\base\ProjectUser as BaseProjectUser;

/**
 * This is the model class for table "project_user".
 */
class ProjectUser extends BaseProjectUser
{
    /**
     * @inheritdoc
     */

    public $roleIds = [];
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['project_id', 'user_id', 'manager'], 'integer'],
            [['user_id'], 'required']
        ]);
    }

    public function getRolesLabel()
    {
        $roles = "";
        foreach ($this->roles as $role)
        {
            if ($roles!="")
                { $roles .=", ";}
            $roles .=$role->name;
        }
        return $roles;
    }

    public function getTaskStatus()
    {
        $task_ids = $this->user->getAllTaskIds(); 
        $tasks = Task::find()->where(['IN', 'id', $task_ids])->andWhere(['project_id'=>$this->project_id])->all();
        $all = count($tasks);
        $done = 0;
        foreach ($tasks as $task)
        {
            if ($task->status ==10)
                $done++;
            else
            {
                $done += TaskDone::find()->where(['task_id'=> $task->id])->andWhere(['user_id'=>$this->user_id])->count();
            }
        }
        if ($all)
            $status = intval($done/$all*100);
        else 
            $status = 0;
        if ($all)
            $content = '<small>'.Yii::t('app', 'Ukończono:').' '.$status.'% ('.$done.'/'.$all.')</small><div class="progress progress-mini">
                                                    <div style="width: '.$status.'%;" class="progress-bar"></div>
                                                </div>';
        else
            $content = Yii::t('app', 'Brak zadań');

        return ['task'=>$all, 'done'=>$done, 'status'=>$status, 'label'=>$content];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Note::createNote(1, 'projectUserAdded', $this, $this->project_id);
    }

    public function beforeDelete()
    {
        Note::createNote(1, 'projectUserDeleted', $this, $this->project_id);
        return true;
    }
	
}
