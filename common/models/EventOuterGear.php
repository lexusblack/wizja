<?php

namespace common\models;

use Yii;

use \common\models\base\EventOuterGear as BaseEventOuterGear;

/**
 * This is the model class for table "event_outer_gear".
 */
class EventOuterGear extends BaseEventOuterGear
{
	const TYPE_ALL_TIME = 1;
   	const TYPE_MANUAL = 2;

   	public function behaviors()
    {
        $behaviors = [
            'workingTime'=> [
                'class'=>\common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName'=>EventOuterGear::className(),
                'itemIdAttribute'=>'outer_gear_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert)
    {
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

    public function getStart()
    {
        $time = '';
        if ($this->start_time == null)
        {
            $time = $this->event->getTimeStart();
        }
        else
        {
            $time = $this->start_time;
        }
        return $time;
    }

    public function getEnd()
    {
        if ($this->end_time == null)
        {
            $time = $this->event->getTimeEnd();
        }
        else
        {
            $time = $this->end_time;
        }
        return $time;
    }



    public function afterDelete()
    {
        Notification::sendUserNotifications($this->event->manager, Notification::EVENT_GEAR_CHANGE, [$this->event, $this->outerGear, $this, Yii::$app->user->getIdentity()]);
        PacklistOuterGear::deleteAll(['event_outer_gear'=>$this->id]);
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

    public function getRentPrice()
    {
        $datetime1 = date_create(substr($this->start_time,0,11));
        $datetime2 = date_create(substr($this->end_time,0,11));
        $interval = date_diff($datetime1, $datetime2);
        $price = ($this->outerGear->price+0.5*$this->outerGear->price*$interval->days)*$this->quantity;
        return $price;
    }

    public function getDateFormatted($datetime)
    {
        $datetime1 = date_create($datetime);
        return date_format($datetime1, 'd.m.Y H:i');
    }

    public function updateExpense()
    {
        $expense = EventExpense::findOne(['event_id'=>$this->event_id, 'gear_id'=>$this->outer_gear_id]);
        if (($expense===null))
        {
                $expense = new EventExpense();
        } 

        $expense->event_id = $this->event_id;
        $expense->gear_id = $this->outer_gear_id;
        $expense->name = $this->outerGear->name." [x".$this->quantity."]";
        $expense->amount = $this->price;
        $expense->amount_customer = $this->outerGear->selling_price*$this->quantity;
        $expense->profit = $expense->amount_customer-$expense->amount;
        $expense->customer_id = $this->outerGear->company_id;
        return $expense->save();                 
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->updateExpense();
        if ($insert){
            Note::createNote(2, 'eventOuterGear', $this, $this->event_id);
            $pack = Packlist::find()->where(['event_id'=>$this->event_id])->orderBy(['main'=>SORT_DESC])->one();
            $p = new PacklistOuterGear();
            $p->packlist_id = $pack->id;
            $p->event_outer_gear = $this->id;
            $p->quantity = $this->quantity;
            $p->save();
        }else{
            if ((isset($changedAttributes['quantity']))&&($this->quantity!=$changedAttributes['quantity']))
            {
                $p = PacklistOuterGear::find()->where(['event_outer_gear'=>$this->id])->one();
                $p->quantity += $this->quantity-$changedAttributes['quantity'];
                $p->save();
            }
            
        }


    }


    public function updateCount()
    {
        $all = PacklistOuterGear::find()->where(['event_outer_gear'=>$this->id])->all();
        if (!$all)
        {
            $this->delete();
        }else{
            $sum = 0;
            foreach ($all as $e)
            {
                $sum +=$e->quantity;
            }
            $this->quantity = $sum;
            $this->save();
        }
    }
}
