<?php
namespace common\models\form;

use backend\modules\permission\models\BasePermission;
use Yii;
use common\helpers\ArrayHelper;

class Calendar2Search extends \yii\base\Model
{

    public $type = [];
    public $status= [];
    public $users = [];
    public $status_task;



    public function rules()
    {
        $rules = [
            [['status','type', 'users'], 'each', 'rule'=>['integer']],

        ];
        return array_merge(parent::rules(), $rules);
    }

    public function getTasksOnCalendar()
    {
        $query = \common\models\Event::find();
        if ($this->type)
        {
            $query->andWhere(['type'=>$this->type]);
        }

        if ($this->status)
        {
            $query->andWhere(['status'=>$this->status]);
        }

        if ($this->users)
        {
                $ids = ArrayHelper::map(\common\models\EventUser::find()->where(['user_id'=>$this->users])->asArray()->all(), 'event_id', 'event_id');
                $query->andWhere(['id'=>$ids]);
        }
        $ids = ArrayHelper::map(\common\models\EventTask::find()->asArray()->all(), 'event_id', 'event_id');
        $query->andWhere(['id'=>$ids]);
        $event_ids = ArrayHelper::map($query->asArray()->all(), 'id', 'id');
        $query = \common\models\Task::find()->where(['event_id'=>$event_ids]);
        if ($this->status_task)
        {
            $query->andWhere(['status'=>$this->status_task]);
        }
        return $query->all();
    }


    public function getEventsOnCalendar($start = null, $end=null)
    {
        if ($start)
            $query = \common\models\Event::find()->where(['>', 'event_start', $start]);
        else
            $query = \common\models\Event::find()->where(['>', 'event_start', '2018-08-08']);
        if ($end)
            $query->andWhere(['<', 'event_end', $end]);
        if ($this->type)
        {
            $query->andWhere(['type'=>$this->type]);
        }

        if ($this->status)
        {
            $query->andWhere(['status'=>$this->status]);
        }

        if ($this->users)
        {
                $ids = ArrayHelper::map(\common\models\EventUser::find()->where(['user_id'=>$this->users])->asArray()->all(), 'event_id', 'event_id');
                $query->andWhere(['id'=>$ids]);
        }
        //$ids = ArrayHelper::map(\common\models\EventTask::find()->asArray()->all(), 'event_id', 'event_id');
        //$query->andWhere(['id'=>$ids]);
        return $query->all();
    }

    public function getEventsNotOnCalendar($start = null, $end=null)
    {
        if (!$start)
            $start = "2019-07-01";
        $query = \common\models\Event::find()->where(['event_start'=>null]);
        $ev_ids =ArrayHelper::map(\common\models\Event::find()->where(['>', 'event_start', $start])->andWhere(['<', 'event_end', $end])->asArray()->all(), 'id', 'id');
        $t_ids = ArrayHelper::map(\common\models\Task::find()->where(['event_id'=>$ev_ids])->asArray()->all(), 'id', 'id');
        $ids = ArrayHelper::map(\common\models\EventTask::find()->where(['task_id'=>$t_ids])->asArray()->all(), 'event_id', 'event_id');
        $query->andWhere(['id'=>$ids]);
        
        if ($this->type)
        {
            $query->andWhere(['type'=>$this->type]);
        }

        if ($this->status)
        {
            $query->andWhere(['status'=>$this->status]);
        }

        if ($this->users)
        {
                $ids = ArrayHelper::map(\common\models\EventUser::find()->where(['user_id'=>$this->users])->asArray()->all(), 'event_id', 'event_id');
                $query->andWhere(['id'=>$ids]);
        }
        
        return $query->all();
    }

}