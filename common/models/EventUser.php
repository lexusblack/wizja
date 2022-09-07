<?php

namespace common\models;

use Yii;
use \common\models\base\EventUser as BaseEventUser;

/**
 * This is the model class for table "event_user".
 */
class EventUser extends BaseEventUser
{
    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;

    public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventUser::className(),
                'itemIdAttribute'=>'user_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            //$this->event->userWorkingTimeChanged();
            $this->new = 1;
        }
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


    public static function assign($attributes)
    {

        $className = static::className();
        $model = new $className($attributes);
        if ($model->user->isAvailable($model->event))
        {
            $model->new = 1;
            return $model->save();
        }
        else
        {
            return false;
        }
    }

    public static function remove($attributes)
    {
        $count = 0;
        $models = static::findAll($attributes);
        foreach ($models as $model)
        {
            $count  += (int) $model->delete();
        }
        return $count;
    }
/*
    public function afterSave($insert, $changedAttributes) {
        if ($insert && Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_ON) {
            Notification::sendUserNotifications($this->user, Notification::USER_ADDED_TO_EVENT, [$this->event, $this]);
        }
        parent::afterSave($insert, $changedAttributes);

    }
*/
    public function afterDelete() {
        if (!$this->new)
            Notification::sendUserNotifications($this->user, Notification::USER_REMOVED_FROM_EVENT, [$this->event, $this->user]);
        $sets = SettlementUser::find()->where(['user_id'=>$this->user_id])->andWhere(['event_id'=>$this->event_id])->all();
        foreach ($sets as $s)
        {
            $s->delete();
        }
        EventUserAddon::deleteAll(['user_id'=>$this->user_id, 'event_id'=>$this->event_id]);
        EventUserAllowance::deleteAll(['user_id'=>$this->user_id, 'event_id'=>$this->event_id]);
        EventUserWorkingTime::deleteAll(['user_id'=>$this->user_id, 'event_id'=>$this->event_id]);
        parent::afterDelete();
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

    public function getPlaceholderMap()
    {
        $creator = UNDEFINDED_STRING;
        if ($this->creator!== null)
        {
            $creator = $this->creator->displayLabel;
        }
        $map = [
            'crewCreator' => $creator,
        ];

        return $map;
    }

    public function getHash()
    {
        return md5($this->user_id." ".$this->event_id." kopytko");
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            Chat::addToChat($this);
            Note::createNote(2, 'eventCrewAdded', $this, $this->event_id);
            $eventlog = new EventLog;
                        $eventlog->event_id = $this->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Do wydarzenia przypisano pracownika ").$this->user->displayLabel;
                        $eventlog->save();

        }


    }

    public function beforeDelete()
    {
        Chat::removeFromChat($this);
        return true;
    }

}
