<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\EventGearItem as BaseEventGearItem;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "event_gear_item".
 */
class EventGearItem extends BaseEventGearItem
{
    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;
    public $cnt;


    public function behaviors()
    {
        $behaviors = [
            'workingTime' => [
                'class' => \common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName' => EventGearItem::className(),
                'itemIdAttribute' => 'gear_item_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert)
            {
                $this->gear_id = $this->gearItem->gear_id;
            }
            $this->setWorkingTimes();
            return true;
        } else {
            return false;
        }
    }

    public static function updateTimesForEvent($model)
    {
        static::updateAll([
            'start_time' => $model->getTimeStart(),
            'end_time' => $model->getTimeEnd(),
        ], [
            'type' => self::TYPE_ALL_TIME,
            'event_id' => $model->id,
        ]);
    }

    public function getStart()
    {
        $time = '';
        if ($this->start_time == null) {
            $time = $this->event->getTimeStart();
        } else {
            $time = $this->start_time;
        }
        return $time;
    }

    public function getEnd()
    {
        if ($this->end_time == null) {
            $time = $this->event->getTimeEnd();
        } else {
            $time = $this->end_time;
        }
        return $time;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert == true && $this->event->manager) {
                Notification::sendUserNotifications($this->event->manager, Notification::EVENT_GEAR_CHANGE, [$this->gearItem, $this->event, $this, Yii::$app->user->getIdentity()]);
        }
        parent::afterSave($insert, $changedAttributes);

    }

    public function afterDelete()
    {
        if ($this->event->manager) {
            Notification::sendUserNotifications($this->event->manager, Notification::EVENT_GEAR_CHANGE, [$this->gearItem, $this->event, $this, Yii::$app->user->getIdentity()]);
        }
        parent::afterDelete();
    }

    protected function _getRecipients()
    {
//        $ids = Yii::$app->authManager->getUserIdsByRole('administrator');
//        $users = User::findAll($ids);
        $users = $this->getUsersForNotification();
        $recipients = ArrayHelper::map($users, 'email', 'displayLabel');
        return $recipients;
    }

    protected function getUsersForNotification()
    {
        $manager = $this->event->manager;
        if ($manager !== null)
        {
            return [$manager];
        }
        else
        {
            return null;
        }
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
