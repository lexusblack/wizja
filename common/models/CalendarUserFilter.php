<?php

namespace common\models;

use Yii;
use \common\models\base\CalendarUserFilter as BaseCalendarUserFilter;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calendar_user_filter".
 */
class CalendarUserFilter extends BaseCalendarUserFilter
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
}
