<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\Vacation as BaseVacation;

/**
 * This is the model class for table "vacation".
 */
class Vacation extends BaseVacation
{
    const STATUS_NEW = 0;
    const STATUS_ACCEPTED = 10;
    const STATUS_REJECTED = 99;

    public $dateRange;

    public function rules()
    {
        $rules = [
            ['dateRange', 'string'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function prepareDateAttributes()
    {
        $this->dateRange = $this->start_date.' - '.$this->end_date;
    }

    public function attributeLabels()
    {
        $labels = [
            'dateRange' => Yii::t('app', 'Od - do'),
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getDays()
    {
        $datetime1 = new \DateTime($this->start_date);
        $datetime2 = new \DateTime($this->end_date);
        $interval = $datetime1->diff($datetime2);
        return $interval->days+1;
    }

    public static function getStatusList($all=false)
    {
        $list = [
            self::STATUS_ACCEPTED => Yii::t('app', 'Zaakceptowany'),
            self::STATUS_REJECTED => Yii::t('app', 'Odrzucony'),
        ];

        if($all == true)
        {
            $list[self::STATUS_NEW] = Yii::t('app', 'Nowy');
        }

        return $list;
    }

    public function getStatusLabel()
    {
        $list = static::getStatusList(true);
        $index = $this->status;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {  
                Note::createNote(4, 'vacationAdded', $this, $this->user_id);
            }else{
                if ((isset($changedAttributes['status']))&&($changedAttributes['status']!=$this->status))
                {
                    if ($this->status==self::STATUS_ACCEPTED)
                        Note::createNote(4, 'vacationAccepted', $this, $this->user_id);
                    if ($this->status==self::STATUS_REJECTED)
                        Note::createNote(4, 'vacationRejected', $this, $this->user_id);
                }
            }
         
    }

    public function beforeDelete()
    {
        Note::createNote(4, 'vacationDeleted', $this, $this->user_id);
        return true;
    }
}
