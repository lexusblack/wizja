<?php
namespace common\behaviors;

use common\models\Offer;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\web\NotFoundHttpException;

class OfferWorkingTimeBehavior extends Behavior
{
    public $startAttribute = 'start_time';
    public $endAttribute = 'end_time';
    public $typeAttribute = 'type';

    public $itemIdAttribute;
    public $parentIdAttribute = 'offer_id';

    public $allTimeReturnString;

    public $connectionClassName;

    /**
     * @var string Pomocniczy do modelu
     */
    public $dateRange;

    public function __construct(array $config = []) { parent::__construct($config); $this->allTimeReturnString = Yii::t('app', 'Całą oferte'); }

    public function getWorkingTime($eventId, $alwaysRange=false, $formatted=false)
    {
        $className = $this->connectionClassName;
        $model = $className::findOne([$this->itemIdAttribute=>$this->owner->id, $this->parentIdAttribute=>$eventId]);
        if ($model == null)
        {
            throw new NotFoundHttpException(Yii::t('app','Czas pracy!'));
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
            $time = $this->owner->offer->getTimeStart();
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
            $time = $this->owner->offer->getTimeEnd();
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
            $this->owner->start_time = $this->owner->offer->getTimeStart();
        }
        if ($this->owner->end_time == null)
        {
            $this->owner->end_time = $this->owner->offer->getTimeEnd();
        }

        $this->owner->start_time = date('Y-m-d H:i:s', strtotime($this->owner->start_time));
        $this->owner->end_time = date('Y-m-d H:i:s', strtotime($this->owner->end_time));

        if ($this->owner->end_time==$this->owner->offer->getTimeEnd() && $this->owner->start_time==$this->owner->offer->getTimeStart())
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

        if ($owner instanceof Offer)
        {
            $query->andWhere(['<>', 'offer_id', $owner->id]);
        }


        $data = $query->asArray()->all();
//        VarDumper::dump($data, 10, true);

        if ($asHtml == true)
        {
            $formatter = \Yii::$app->formatter;
            foreach ($data as $d)
            {
                $id = $d['offer_id'];
                $route = ['offer/default/view', 'id'=>$id];
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

        if ($owner instanceof Offer)
        {
            $query->andWhere(['<>', 'offer_id', $owner->id]);
        }

        $count = $db->cache(function($db) use ($query) {
            return $query->count();
        }, 5);
        $available =  $count == 0 ? true : false;

        return $available;

    }

}
