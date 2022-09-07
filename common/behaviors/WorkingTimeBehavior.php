<?php
namespace common\behaviors;

use common\models\Event;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;

class WorkingTimeBehavior extends Behavior
{
//    const TYPE_ALL_TIME = 1;
//    const TYPE_MANUAL = 2;

    public $startAttribute = 'start_time';
    public $endAttribute = 'end_time';
    public $typeAttribute = 'type';

    public $itemIdAttribute;
    public $parentIdAttribute = 'event_id';

    public $allTimeReturnString = 'Cały event';

    public $connectionClassName;

    /**
     * @var string Pomocniczy do modelu
     */
    public $dateRange;

//    public $attributes = [];
//    public $relations = [];
//    public $modelClasses = [];

//    public function events()
//    {
//        return [
//            ActiveRecord::EVENT_AFTER_INSERT => 'linkObjects',
//            ActiveRecord::EVENT_AFTER_UPDATE => 'linkObjects',
//        ];
//    }

    public function getWorkingTime($eventId, $alwaysRange=false, $formatted=false)
    {
        $className = $this->connectionClassName;
        $model = $className::findOne([$this->itemIdAttribute=>$this->owner->id, $this->parentIdAttribute=>$eventId]);
        if ($model == null)
        {
            echo $this->itemIdAttribute;
            echo $this->owner->id;
            exit;
            throw new NotFoundHttpException('Czas pracy!');
        }

        if ($model->type == $className::TYPE_ALL_TIME && $alwaysRange==false)
        {
            $value = $this->allTimeReturnString;
        }
        else
        {
            $start = $model->getStart();
            $end = $model->getEnd();
            if ($formatted == true)
            {
                $start = Yii::$app->formatter->asDatetime($start, 'short');
                $end = Yii::$app->formatter->asDatetime($end, 'short');
            }

            $value = $start.' - '.$end;
        }

        return $value;
    }

    public function getStart()
    {
        $time = '';
        if ($this->owner->start_time == null)
        {
            $time = $this->owner->event->getTimeStart();
        }
        else
        {
            $time = $this->owner->start_time;
        }
        return $time;
    }

    public function getEnd()
    {
        if ($this->owner->end_time == null)
        {
            $time = $this->owner->event->getTimeEnd();
        }
        else
        {
            $time = $this->owner->end_time;
        }
        return $time;
    }

    public function setWorkingTimes()
    {
        $className = $this->connectionClassName;

        if ($this->owner->start_time == null)
        {
            $this->owner->start_time = $this->owner->event->getTimeStart();
        }
        if ($this->owner->end_time == null)
        {
            $this->owner->end_time = $this->owner->event->getTimeEnd();
        }

        $this->owner->start_time = date('Y-m-d H:i:s', strtotime($this->owner->start_time));
        $this->owner->end_time = date('Y-m-d H:i:s', strtotime($this->owner->end_time));

        if ($this->owner->end_time==$this->owner->event->getTimeEnd() && $this->owner->start_time==$this->owner->event->getTimeStart())
        {
            $this->owner->type = $className::TYPE_ALL_TIME;
        }
        else
        {
            $this->owner->type = $className::TYPE_MANUAL;
        }
    }

    public function getUnavailableRanges($owner, $asHtml=false)
    {
        $db = Yii::$app->getDb();
        $ranges = [];

        $start = $owner->getTimeStart();
        $end = $owner->getTimeEnd();

        $className = $this->connectionClassName;
        $query = $className::find()
            ->where([
                'and',
                ['<=', 'start_time', $start],
                ['>=', 'end_time', $start],
            ])
            ->orWhere([
                'and',
                ['<=', 'start_time', $end],
                ['>=', 'end_time', $end],
            ])
            ->orWhere([
                'and',
                ['>=', 'start_time', $start],
                ['<=', 'end_time', $end],
            ]);
        $query->andWhere([
            $this->itemIdAttribute=>$this->owner->id,
        ]);

        if ($owner instanceof Event)
        {
            $query->andWhere(['<>', 'event_id', $owner->id]);
        }


        $data = $query->asArray()->all();
//        VarDumper::dump($data, 10, true);

        if ($asHtml == true)
        {
            $formatter = \Yii::$app->formatter;
            foreach ($data as $d)
            {
                $id = $d['event_id'];
                $route = ['event/view', 'id'=>$id];
                $r = $formatter->asDate($d['start_time']).' - '.$formatter->asDate($d['end_time']);
                $ranges[] = Html::a($r, $route, ['class'=>'btn btn-danger btn-sm']);
            }
        }
        else
        {
            $ranges = $data;
        }

        return $ranges;

    }

    public function isAvailable($owner)
    {
        $db = Yii::$app->getDb();
        $available = false;

        $start = $owner->getTimeStart();
        $end = $owner->getTimeEnd();

        $className = $this->connectionClassName;
        $query = $className::find()
            ->where([
                'and',
                ['<=', 'start_time', $start],
                ['>=', 'end_time', $start],
            ])
            ->orWhere([
                'and',
                ['<=', 'start_time', $end],
                ['>=', 'end_time', $end],
            ])
            ->orWhere([
                'and',
                ['>=', 'start_time', $start],
                ['<=', 'end_time', $end],
            ])
            ->andWhere([
                $this->itemIdAttribute=>$this->owner->id,
            ]);

        if ($owner instanceof Event)
        {
            $query->andWhere(['<>', 'event_id', $owner->id]);
        }

        $count = $db->cache(function($db) use ($query) {
            return $query->count();
        }, 5);
        $available =  $count == 0 ? true : false;

        return $available;

    }

}
