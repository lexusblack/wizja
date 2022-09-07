<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\EventUserAllowance as BaseEventUserAllowance;

/**
 * This is the model class for table "event_user_allowance".
 */
class EventUserAllowance extends BaseEventUserAllowance
{
    const TYPE_LOCAL = 1;
    const TYPE_FOREIGN = 2;

    public $dateRange;

    public static function getTypeList()
    {
        $list = [
            self::TYPE_LOCAL => Yii::t('app', 'Krajowa'),
            self::TYPE_FOREIGN => Yii::t('app', 'Zagraniczna'),
        ];
        return $list;
    }

    public function getTypeLabel()
    {
        return ArrayHelper::getValue(static::getTypeList(), $this->type, UNDEFINDED_STRING);
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
