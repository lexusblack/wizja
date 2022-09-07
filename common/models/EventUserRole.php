<?php

namespace common\models;

use Yii;
use \common\models\base\EventUserRole as BaseEventUserRole;

/**
 * This is the model class for table "event_user_role".
 */
class EventUserRole extends BaseEventUserRole
{
    public static function removeAll($userId, $eventId)
    {
        $model = static::findEventUser($userId, $eventId);
        if ($model !== null)
        {
            static::deleteAll([
                'event_user_id'=>$model->id,
            ]);
        }
    }

    public static function findEventUser($userId, $eventId)
    {
        $model = EventUser::findOne([
            'user_id'=>$userId,
            'event_id'=>$eventId,
        ]);
        return $model;
    }
}
