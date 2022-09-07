<?php

namespace common\models;

use Yii;
use \common\models\base\EventUserAddon as BaseEventUserAddon;

/**
 * Koszty dodatkowe użytkownika (przejazd autem itd.)
 * This is the model class for table "event_user_addon".
 */
class EventUserAddon extends BaseEventUserAddon
{
    public $dateRange;

//    public function behaviors()
//    {
//        $behaviors = [
//
//            'eventDatesBehavior' => [
//                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
//            ],
//
//        ];
//        return array_merge(parent::behaviors(), $behaviors);
//    }

    public function beforeValidate()
    {
        if ($this->user_id === null)
        {
            $this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        $labels = [
            'dateRange'=>Yii::t('app', 'Daty'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getTimeRange($separator = ' - ', $format='short')
    {
        $formatter = Yii::$app->formatter;

        $start = $this->start_time;
        $end = $this->end_time;

        return $formatter->asDatetime($start, $format).$separator.$formatter->asDatetime($end, $format);
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //robimy store
        //SettlementUser::store($this->user, $this->event, $year, $month)
        if ($this->event->type!=1)
                $this->event->updateParentExpense();

                $start_year = substr($this->start_time, 0, 4);
        $end_year = substr($this->end_time, 0, 4);
        $start_month = substr($this->start_time,5,2);
        $end_month = substr($this->end_time,5,2);
        if ($end_month[0]=="0")
            $end_month = $end_month[1];
        if ($start_month[0]=="0")
            $start_month = $start_month[1];
        SettlementUser::store($this->user, $this->event, $start_year, $start_month );
        if ($start_month!=$end_month)
        {
            SettlementUser::store($this->user, $this->event, $end_year, $end_month );
        }
    }

            public function afterDelete()
    {
        parent::afterDelete();
        if ($this->event !== null)
        {
            if ($this->event->type!=1)
            {
                $this->event->updateParentExpense();
            }
                    $start_year = substr($this->start_time, 0, 4);
                    $end_year = substr($this->end_time, 0, 4);
                    $start_month = substr($this->start_time,5,2);
                    $end_month = substr($this->end_time,5,2);
                    if ($end_month[0]=="0")
                        $end_month = $end_month[1];
                    if ($start_month[0]=="0")
                        $start_month = $start_month[1];
                    SettlementUser::store($this->user, $this->event, $start_year, $start_month );
                    if ($start_month!=$end_month)
                    {
                        SettlementUser::store($this->user, $this->event, $end_year, $end_month );
                    }
        }
    }
}
