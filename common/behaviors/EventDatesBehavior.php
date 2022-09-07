<?php
namespace common\behaviors;

use common\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\validators\CompareValidator;
use Yii;

class EventDatesBehavior extends Behavior
{
    public static $dateAttributesBase = [
        'event',
        'packing',
        'montage',
        'practice',
        'readiness',
        'disassembly'
    ];

    public function events()
    {
        return [
//            ActiveRecord::EVENT_BEFORE_VALIDATE => 'validateDates',
        ];
    }

    public function validateDates()
    {
        return true;
        $owner = $this->owner;
        $attrs = self::$dateAttributesBase;

        $tmpS = null;
        $tmpE = null;

        $toValidate = [];
        $toSet = [];

        foreach ($attrs as $a)
        {
            $s = $a.'_start';
            $e = $a.'_end';
            if (empty($owner->$s) == false)
            {
                //???: $toSet[] = $a;

                $dateS = \DateTime::createFromFormat('d/m/Y H:i', $owner->$s);
                $dateE = \DateTime::createFromFormat('d/m/Y H:i', $owner->$e);

                if ($dateS==false || $dateE==false)
                {
                    continue;
                }
                $toSet[] = $a;
                $owner->$s = $dateS->getTimestamp();
                $owner->$e = $dateE->getTimestamp();
//                $toValidate[] = $s;
//                $toValidate[] = $e;
            }
        }

        $validator = new CompareValidator([
            'operator'=>'<=',
            'type'=>'number'
        ]);
        $validator->when = (function ($model, $attribute) use ($validator) {
            $compareAttribute = $validator->compareAttribute;
            if (empty($model->$compareAttribute) == true )
            {
                return false;
            }
            return true;
        });

        foreach ($attrs as $a)
        {
            $validator->compareAttribute = $a.'_end';
            $validator->attributes = [$a.'_start'];
            $validator->validateAttributes($owner);
        }


        $validator->compareAttribute = 'disassembly_start';
        $validator->attributes = ['packing_end', 'montage_end', 'readiness_end', 'practice_end'];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'practice_start';
        $validator->attributes = ['packing_end', 'montage_end', 'readiness_end',];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'readiness_start';
        $validator->attributes = ['packing_end', 'montage_end'];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'montage_start';
        $validator->attributes = ['packing_end'];
        $validator->validateAttributes($owner);

//        $validator = new CompareValidator([
//            'operator'=>'>=',
//            'type'=>'number'
//        ]);
        $validator->operator = '>=';

        $validator->compareAttribute = 'packing_end';
        $validator->attributes = ['montage_start', 'readiness_start', 'practice_start', 'disassembly_start'];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'montage_end';
        $validator->attributes = ['readiness_start', 'practice_start', 'disassembly_start'];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'readiness_end';
        $validator->attributes = ['practice_start', 'disassembly_start'];
        $validator->validateAttributes($owner);

        $validator->compareAttribute = 'practice_end';
        $validator->attributes = ['disassembly_start'];
        $validator->validateAttributes($owner);

        foreach ($toSet as $a)
        {
            $s = $a.'_start';
            $e = $a.'_end';
            $owner->$s = date('Y-m-d H:i:s', $owner->$s);
            $owner->$e = date('Y-m-d H:i:s', $owner->$e);
        }
    }

    public function prepareDateAttributes()
    {
        $owner = $this->owner;
        $this->removeDateAttributes();
        $attributes = self::$dateAttributesBase;

        foreach ($attributes as $attribute)
        {
            //if(isset($owner->{$attribute.'_start'}) && isset($owner->{$attribute.'_end'}) && ($owner->{$attribute.'_start'} != $owner->{$attribute.'_end'}))
            if(isset($owner->{$attribute.'_start'}) && isset($owner->{$attribute.'_end'}))
            {
                $start = date('d/m/Y H:i', strtotime($owner->{$attribute.'_start'}));
                $end = date('d/m/Y H:i', strtotime($owner->{$attribute.'_end'}));
                $owner->{$attribute.'DateRange'} = $start.' - '.$end;
            } else {
                $owner->{$attribute.'DateRange'} = null;
            }

        }

    }

    public function removeDateAttributes()
    {
        $owner = $this->owner;
        $attributes = self::$dateAttributesBase;

        foreach ($attributes as $attribute)
        {
            /*if($owner->{$attribute.'_start'} == $owner->{$attribute.'_end'}){
                $owner->{$attribute.'_start'} = null;
                $owner->{$attribute.'_end'} = null;
            }*/
        }

    }

    public function setDateAttributes()
    {
        return true;
        $owner = $this->owner;
        $this->removeDateAttributes();
        $attributes = self::$dateAttributesBase;

        foreach ($attributes as $attribute)
        {
//            if($this->{$attribute.'DateRange'} !== '' && $this->{$attribute.'DateRange'} !== null)
            if(empty($owner->{$attribute.'DateRange'}) == false)
            {
                $date_arr = explode(" - ", $owner->{$attribute.'DateRange'});
                $owner->{$attribute.'_start'} = $date_arr[0];
                $owner->{$attribute.'_end'} = $date_arr[1];
            }
            else
            {
                $owner->{$attribute.'_start'} = null;
                $owner->{$attribute.'_end'} = null;
            }
        }

        return true;

    }

    /**
     * @param bool|integer $type false - all, 0 - start, 1 - end
     */
    public function getEventTimes($type=false)
    {
        $owner = $this->owner;
        $start = [
            $owner->packing_start,
            $owner->montage_start,
            $owner->readiness_start,
            $owner->practice_start,
            $owner->event_start,
            $owner->disassembly_start,
        ];
        $end = [
            $owner->packing_end,
            $owner->montage_end,
            $owner->readiness_end,
            $owner->practice_end,
            $owner->event_end,
            $owner->disassembly_end,
        ];

        $start = array_filter($start);
        $end = array_filter($end);

        $list = [];
        switch ($type)
        {
            default:
                $list = array_merge($start, $end);
                break;
            case 0:
                $list = $start;
                break;
            case 1:
                $list = $end;
                break;
        }
        $list = ArrayHelper::sortDates($list);
        return $list;
    }

    public function getTimeStart()
    {
        return $this->owner->event_start;
    }

    public function getStartTimeForCalendar()
    {
        return $this->getTimeStart();

    }

    public function getTimeEnd()
    {
        return $this->owner->event_end;
    }

    public function getTimeRange($separator = ' - ', $format='short')
    {
        $owner = $this->owner;
        $formatter = Yii::$app->formatter;

        $start = $owner->getTimeStart();
        $end = $owner->getTimeEnd();

        return $formatter->asDatetime($start, $format).$separator.$formatter->asDatetime($end, $format);
    }
}