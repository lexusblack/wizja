<?php

namespace common\models;

use Yii;
use \common\models\base\EventOuterGearItem as BaseEventOuterGearItem;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "event_outer_gear_item".
 */
class EventOuterGearItem extends BaseEventOuterGearItem
{
	const TYPE_ALL_TIME = 1;
   	const TYPE_MANUAL = 2;

   	public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventOuterGearItem::className(),
                'itemIdAttribute'=>'outer_gear_item_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            $this->setWorkingTimes();
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function updateTimesForEvent($model)
    {
        static::updateAll([
            'start_time'=>$model->getTimeStart(),
            'end_time'=>$model->getTimeEnd(),
        ], [
           'type'=>self::TYPE_ALL_TIME,
            'event_id'=>$model->id,
        ]);
    }

    public function getStart()
    {
        $time = '';
        if ($this->start_time == null)
        {
            $time = $this->event->getTimeStart();
        }
        else
        {
            $time = $this->start_time;
        }
        return $time;
    }

    public function getEnd()
    {
        if ($this->end_time == null)
        {
            $time = $this->event->getTimeEnd();
        }
        else
        {
            $time = $this->end_time;
        }
        return $time;
    }
}
