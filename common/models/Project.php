<?php

namespace common\models;

use Yii;
use \common\models\base\Project as BaseProject;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project".
 */
class Project extends BaseProject
{
    public $event_ids;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['tasks_schema_id', 'customer_id', 'contact_id', 'creator_id'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time', 'event_ids'], 'safe'],
            [['description'], 'string'],
            [['departmentIds'], 'each', 'rule'=>['integer']],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ]);
    }


    public function getUserList()
    {
        $ids = ArrayHelper::map(ProjectUser::find()->where(['project_id'=>$this->id])->asArray()->all(), 'user_id', 'user_id');
        $users = User::find()->where(['active'=>1])->andWhere(['NOT IN', 'id', $ids])->orderBy(['last_name'=>SORT_ASC])->all();
        $list =[];
        foreach ($users as $u)
        {
            $list[$u->id] = $u->last_name." ".$u->first_name;
        }
        return $list;

    }
    public function getAssignedEvents($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getEvents();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getAssignedOffers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $ids = ArrayHelper::map(Event::find()->where(['project_id'=>$this->id])->asArray()->all(), 'id', 'id');
        $query = Offer::find()->where(['IN', 'event_id', $ids])->orWhere(['project_id'=>$this->id]);
        $query->orderBy = ['event_end'=>SORT_DESC, 'event_start'=>SORT_DESC];
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
	

    public function getCompletion()
    {
        $task = Task::find()->where(['project_id'=>$this->id])->count();
        $task_done =  Task::find()->where(['project_id'=>$this->id])->andWhere(['status'=>10])->count();
        if ($task==0)
            return ['task'=>$task, 'done'=>$task_done, 'status'=>0];
        else
            return ['task'=>$task, 'done'=>$task_done, 'status'=>intval($task_done/$task*100)];
    }

    public function copyTasks()
    {
        if ($this->tasks_schema_id)
        {
            $tasksSchema = TasksSchema::findOne($this->tasks_schema_id);
            foreach ($tasksSchema->tasksSchemaCats as $category)
            {
                $cat = new TaskCategory;
                $cat->name = $category->name;
                $cat->order = $category->order;
                $cat->project_id = $this->id;
                $cat->color = $category->color;
                $cat->save();
                foreach ($category->taskSchemas as $schema)
                {
                    $task = new Task;
                    $task->title = $schema->name;
                    $task->content = $schema->description;
                    $task->order = $schema->order;
                    $task->task_category_id = $cat->id;
                    $task->project_id = $this->id;
                    $task->only_one = $schema->only_one;
                    if (($schema->time_type!=1)&&($this->start_time))
                    {
                        $secs = 3600*$schema->hours+60*$schema->minutes+24*3600*$schema->days;
                        if ($schema->time_type<4)
                        {
                            $start = $this->start_time;
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);

                        }else{
                            $start = $this->end_time;
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);
                        }
                        if (($schema->time_type==2)||($schema->time_type==4))
                        {
                            $time = $time-$secs;
                        }else{
                            $time = $time+$secs;
                        }
                        $task->datetime = date("Y-m-d H:i:s", $time);
                    }
                    $task->save();
                    foreach ($schema->users as $user)
                    {
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    /*
                    if (($schema->manager)&&($this->manager_id)){
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }*/
                    foreach ($schema->roles as $role)
                    {
                        $tr = new TaskRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role_id = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->notificationUsers as $user)
                    {
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    /*
                    if (($schema->manager_notification)&&($this->manager_id)){
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }*/
                    foreach ($schema->notificationRoles as $role)
                    {
                        $tr = new TaskNotificationRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->taskSchemaNotifications as $no)
                    {
                        $not = new TaskNotification;
                        $not->task_id = $task->id;
                        $not->time_type = $no->time_type;
                        $not->time = $no->time;
                        $not->email = $no->email;
                        $not->sms = $no->sms;
                        $not->push = $no->push;
                        $not->text = $no->text;
                        $not->sent = 0;
                        $not->save();
                    }

                }
            }
        }
    }


    public function behaviors()
    {

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'departmentIds',
            ],
            'relations' => [
                'departments',
            ],
            'modelClasses'=>[
                'common\models\Department',
            ],
            ];
        $behaviors['timestamp'] = [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ];

        return $behaviors;
    }

    public function statusLabel()
    {
                    $now = date('Y-m-d');
                    if ($this->start_time>$now)
                    {
                        return '<span class="label label-success">'.Yii::t('app', 'Nowy').'</span>';
                    }
                    if ($this->end_time<$now)
                    {
                        return '<span class="label label-default">'.Yii::t('app', 'Zamknięty').'</span>';
                    }
                    return '<span class="label label-primary">'.Yii::t('app', 'Aktywny').'</span>';
    }

    public function getOtherTasks()
    {
        $tasks = Task::find()->where(['project_id'=>$this->id])->andWhere(['is', 'task_category_id', null])->orderBy(['order'=>SORT_ASC])->all();
        return $tasks;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            Note::createNote(1, 'projectCreate', $this, $this->id);
        }else{
            if (((isset($changedAttributes['start_time']))&&($changedAttributes['start_time']!=$this->start_time))||((isset($changedAttributes['end_time']))&&($changedAttributes['end_time']!=$this->end_time)))
                    Note::createNote(1, 'projectScheduleChanged', $this, $this->id);
        }

    }

    public function beforeDelete()
    {
        Note::createNote(1, 'projectDelete', $this, $this->id);
        return true;
    }
}
