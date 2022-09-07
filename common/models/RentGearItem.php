<?php

namespace common\models;

use Yii;
use \common\models\base\RentGearItem as BaseRentGearItem;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "rent_gear_item".
 */
class RentGearItem extends BaseRentGearItem
{
    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;
    public $cnt;
    public function getDateRange() {
        return $this->start_time . " - " . $this->end_time;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if ($this->start_time == null)
            {
                $this->start_time = $this->rent->getTimeStart();
            }
            if ($this->end_time == null)
            {
                $this->end_time = $this->rent->getTimeEnd();
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function updateTimesForRent($model)
    {
        static::updateAll([
            'start_time'=>$model->getTimeStart(),
            'end_time'=>$model->getTimeEnd(),
        ], [
            'type'=>self::TYPE_ALL_TIME,
            'rent_id'=>$model->id,
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert == true) {
            Notification::sendUserNotifications($this->rent->createdBy, Notification::EVENT_GEAR_CHANGE, [$this->rent, $this->gearItem, $this, Yii::$app->user->getIdentity()]);
        }
        parent::afterSave($insert, $changedAttributes);

    }

    public function afterDelete()
    {
        Notification::sendUserNotifications($this->rent->createdBy, Notification::EVENT_GEAR_CHANGE, [$this->rent, $this->gearItem, $this, Yii::$app->user->getIdentity()]);
        parent::afterDelete();
    }

    public function getPlaceholderMap()
    {
        $formatter = Yii::$app->formatter;
        $map = [
            'gear.timeStart'=>$formatter->asDatetime($this->start_time, 'short'),
            'gear.timeEnd'=>$formatter->asDatetime($this->end_time, 'short'),
        ];

        return $map;
    }
}
