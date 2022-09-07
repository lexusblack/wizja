<?php

namespace common\models;

use Yii;
use \common\models\base\EventGear as BaseEventGear;

/**
 * This is the model class for table "event_gear".
 */
class EventGear extends BaseEventGear
{
    const TYPE_ALL_TIME = 1;
    const TYPE_MANUAL = 2;
    public $cnt;


    public function behaviors()
    {
        $behaviors = [
            'workingTime' => [
                'class' => \common\behaviors\WorkingTimeBehavior::className(),
                'connectionClassName' => EventGear::className(),
                'itemIdAttribute' => 'gear_id',

            ],
        ];
        return array_merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
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
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'gear_id', 'quantity', 'type'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ]);
    }

    public function clearConflicts()
    {
        EventConflict::deleteAll(['event_id' => $this->event_id, 'gear_id'=>$this->gear_id]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $count = Note::find()->where(['event_id'=>$this->event_id])->andWhere(['user_id'=>Yii::$app->user->id])->andWhere(['>', 'datetime', date('Y-m-d H')."00:00"])->andWhere(['like', 'text',Yii::t('app', 'Zmieniono rezerwację sprzętu w wydarzeniu ')])->count();
        if (!$count)
            Note::createNote(2, 'eventGearChanged', $this->event, $this->event_id);
        if ($this->gear->type==3)
        {
                    $expense = EventExpense::findOne(['event_id'=>$this->event_id, 'gear_id'=>$this->gear_id]);
                                if (($expense===null))
                                {
                                    $expense = new EventExpense();
                                } 
                                    $expense->event_id = $this->event_id;
                                    $expense->gear_id = $this->gear_id;
                                    $expense->name = $this->gear->name." [x".$this->quantity."] mat. ekspl.";
                                    $expense->amount = $this->gear->getExplPrice()*$this->quantity;
                                    $expense->profit = $expense->amount_customer-$expense->amount;
                                    $expense->customer_id = $this->gear->getExplCompany();
                                    $gear = $this->gear;
                                    $expense->sections = [$gear->category->getMainCategory()->name];
                                    $expense->save(); 
        }

        //zmiana liczby sztuk - sprawdzamy czy nie trzeba usunąć z packlisty jakiejś
        if (!$insert)
        {
            if (isset($changedAttributes['quantity']))
            {
                $log = new EventLog();
                $log->event_id = $this->event_id;
                $log->user_id = Yii::$app->user->id;
                $log->content = Yii::t('app', 'Zmieniono rezerwację sprzętu ').$this->gear->name.Yii::t('app', ' z ').$changedAttributes['quantity'].Yii::t('app', ' na ').$this->quantity.Yii::t('app', ' szt.');
                $log->save();
                if ($changedAttributes['quantity']>$this->quantity)
                {
                    //usunięty jakiś sprzęt
                    $total = 0;
                    foreach ($this->packlistGears as $g)
                    {
                        $total+=$g->quantity;
                    }
                    if ($total>$this->quantity)
                    {
                        $to_delete = $total-$this->quantity;
                        foreach ($this->packlistGears as $g)
                        {
                            if ($to_delete>0)
                            {
                                if ($to_delete>=$g->quantity)
                                {
                                    $to_delete -=$g->quantity;
                                    $g->delete();
                                }else{
                                    $g->quantity -=$to_delete;
                                    $g->save();
                                    $to_delete = 0;
                                }
                            }
                        }
                    }
                }
            }
        }else{
            $log = new EventLog();
            $log->event_id = $this->event_id;
            $log->user_id = Yii::$app->user->id;
            $log->content = Yii::t('app', 'Do eventu przypisano sprzęt ').$this->gear->name." - ".$this->quantity.Yii::t('app', ' szt.');
            $log->save();

        }

    }

    public function beforeDelete()
    {
        if ($this->gear->type==3)
        {
            EventExpense::deleteAll(['event_id'=>$this->event_id, 'gear_id'=>$this->gear_id]);
            $this->gear->quantity +=$this->quantity;
            $this->gear->save();
        }
        $items = PacklistGear::find()->where(['event_gear_id'=>$this->id])->all();
        foreach ($items as $item)
        {
            $item->delete();
        }
        
        return true;
    }

    public function getTotal()
    {
                $items = PacklistGear::find()->where(['event_gear_id'=>$this->id])->all();
        $total = 0;
        foreach ($items as $item)
        {
            $total +=$item->quantity;
        }
        return $total;
    }

    public function updateCount()
    {
        $items = PacklistGear::find()->where(['event_gear_id'=>$this->id])->all();
        $total = 0;
        foreach ($items as $item)
        {
            $total +=$item->quantity;
        }
        if ($total)
        {
                $this->quantity = $total;
                $this->save();
        }else{
            if (!$items)
                $this->delete();
        }
        
    }
	
}
