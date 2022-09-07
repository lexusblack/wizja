<?php

namespace common\models;

use \common\models\base\EventOuterGearModel as BaseEventOuterGearModel;

/**
 * This is the model class for table "event_outer_gear_model".
 */
class EventOuterGearModel extends BaseEventOuterGearModel
{
    /**
     * @inheritdoc
     */

    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventOuterGearModel::className(),
                'itemIdAttribute'=>'id',

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

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'outer_gear_model_id', 'quantity'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ]);
    }

    public function getDateFormatted($datetime)
    {
        $datetime1 = date_create($datetime);
        return date_format($datetime1, 'd.m.Y H:i');
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
