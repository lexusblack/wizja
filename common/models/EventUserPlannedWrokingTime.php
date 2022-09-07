<?php

namespace common\models;

use Yii;
use \common\models\base\EventUserPlannedWrokingTime as BaseEventUserPlannedWrokingTime;

/**
 * This is the model class for table "event_user_planned_wroking_time".
 */
class EventUserPlannedWrokingTime extends BaseEventUserPlannedWrokingTime
{

    public function getDateRange() {
        return $this->start_time . " - " .$this->end_time;
    }

    public function beforeSave($insert) {
        /*if ($insert && Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_OFF) {
            $this->event->userWorkingTimeChanged();
        }*/
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $eu = EventUser::find()->where(['user_id'=>$this->user_id])->andWhere(['event_id'=>$this->event_id])->one();
        if ($eu){
            if (!$eu->new)
            $eu->edited = 1;
                $eu->save();
        }

        /*if ($insert  && Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_ON) {
            Notification::sendUserNotifications($this->user, Notification::EVENT_SCHEDULE_CHANGE, [$this->event, $this]);
        }*/
    }

    public function beforeDelete() {
        $eu = EventUser::find()->where(['user_id'=>$this->user_id])->andWhere(['event_id'=>$this->event_id])->one();
        if (!$eu->new)
            $eu->edited = 1;
        $eu->save();
        $roles = EventUserRole::find()->where(['working_hours_id'=>$this->id])->all();
        foreach ($roles as $role)
        {
            $role->delete();
        }
        return parent::beforeDelete(); 
    }

    public function afterDelete() {
        parent::afterDelete();
        $eu = EventUser::find()->where(['user_id'=>$this->user_id])->andWhere(['event_id'=>$this->event_id])->one();
        if (!$eu->new)
            $eu->edited = 1;
        $eu->save();
        /*
        if (Yii::$app->settings->get('eventNotifications', 'main') == Event::NOTIFICATIONS_ON) {
            Notification::sendUserNotifications($this->user, Notification::EVENT_SCHEDULE_CHANGE, [$this->event, $this]);
        }
        */
    }

}
