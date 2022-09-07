<?php

namespace common\models;

use Yii;
use \common\models\base\EventVehicle as BaseEventVehicle;

/**
 * This is the model class for table "event_vehicle".
 */
class EventVehicle extends BaseEventVehicle
{
    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventGearItem::className(),
                'itemIdAttribute'=>'vehicle_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public static function assign($attributes)
    {
        $className = static::className();
        $model = new $className($attributes);
        return $model->save();
    }

    public static function remove($attributes)
    {
        EventVehicleWorkingHours::deleteAll($attributes);
        return static::deleteAll($attributes);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
           //$this->setWorkingTimes();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
            Note::createNote(2, 'eventCarAdded', $this, $this->event_id);
        if ($this->vehicle->type==5)
        {
            //samochód do wypożyczenia - dodajemy koszt
            if ($insert)
            {
                $e = new EventExpense();
                $e->event_id = $this->event_id;
                $e->name = $this->vehicle->name;
                $e->sections = [Yii::t('app', 'Transport')];
                $e->amount = 0;
                $e->save();
            }
        }


    }
}
