<?php

namespace common\models;

use Yii;
use \common\models\base\EventSchedule as BaseEventSchedule;

/**
 * This is the model class for table "event_schedule".
 */
class EventSchedule extends BaseEventSchedule
{
    /**
     * @inheritdoc
     */
    public $dateRange;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
        [
            [['event_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['start_time', 'end_time', 'color'], 'safe'],
            [['name', 'prefix'], 'string', 'max' => 45]
        ]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $event = Event::findOne($this->event_id);
        $event->updateSchedule();
        if (!$insert)
        {
            if ((isset($changedAttributes['start_time']))&&($this->start_time!=$changedAttributes['start_time']))
            {
                //$this->updatePacklists($this->start_time, $changedAttributes['start_time'], 'start');
                $this->updateWorkers($this->start_time, $changedAttributes['start_time'], 'start');
            }
            if ((isset($changedAttributes['end_time']))&&($this->end_time!=$changedAttributes['end_time']))
            {
                //$this->updatePacklists($this->end_time, $changedAttributes['end_time'], 'end');
                $this->updateWorkers($this->end_time, $changedAttributes['end_time'], 'end');
            }
            $eventlog = new EventLog;
                $eventlog->event_id = $this->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Zmieniono harmonogram: ".$this->name." z ".$changedAttributes['start_time']." - ".$changedAttributes['end_time']." na ".$this->start_time." - ".$this->end_time);
                $eventlog->save();          
        }else{
            $eventlog = new EventLog;
                $eventlog->event_id = $this->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Dodano harmonogram: ".$this->name." ".$this->start_time." - ".$this->end_time);
                $eventlog->save();
        }

    }

    public function updatePacklists($new, $old, $type)
    {
        if ($type=='start')
        {
            $packlists = Packlist::find()->where(['start_time'=>$old])->all();
            foreach ($packlists as $p)
            {
                $p->start_time = $new;
                $p->save();
            }
        }else{
             $packlists = Packlist::find()->where(['end_time'=>$old])->all();
            foreach ($packlists as $p)
            {
                $p->end_time = $new;
                $p->save();
            }           
        }
    }

    public function updateWorkers($new, $old, $type)
    {
        if ($type=='start')
        {
            $workings = EventUserPlannedWrokingTime::find()->where(['event_schedule_id'=>$this->id])->andWhere(['start_time'=>$old])->all();
            foreach ($workings as $w)
            {
                $w->start_time = $new;
                $w->save();
            }
        }else{
            $workings = EventUserPlannedWrokingTime::find()->where(['event_schedule_id'=>$this->id])->andWhere(['end_time'=>$old])->all();
            foreach ($workings as $w)
            {
                $w->end_time = $new;
                $w->save();
            }           
        }

        if ($type=='start')
        {
            $workings = EventVehicleWorkingHours::find()->where(['event_schedule_id'=>$this->id])->andWhere(['start_time'=>$old])->all();
            foreach ($workings as $w)
            {
                $w->start_time = $new;
                $w->save();
            }
        }else{
            $workings = EventVehicleWorkingHours::find()->where(['event_schedule_id'=>$this->id])->andWhere(['end_time'=>$old])->all();
            foreach ($workings as $w)
            {
                $w->end_time = $new;
                $w->save();
            }           
        }
    }

    public function afterDelete()

    {
        $event = Event::findOne($this->event_id);
        EventUserPlannedWrokingTime::deleteAll(['event_schedule_id'=>$this->id]);
        EventVehicleWorkingHours::deleteAll(['event_schedule_id'=>$this->id]);
        $eventlog = new EventLog;
                $eventlog->event_id = $this->event_id;
                $eventlog->user_id = Yii::$app->user->identity->id;
                $eventlog->content = Yii::t('app', "Usunięto harmonogram: ".$this->name." ".$this->start_time." - ".$this->end_time);
                $eventlog->save();
        $event->updateSchedule();
            parent::afterDelete();
    }

        public function getPeriodTime()
    {
        $difference = ceil(abs(strtotime($this->end_time) - strtotime($this->start_time)) / 3600);
            return $difference;
    }

    public function prepareForCalendar()
    {
        
        $description = $this->event->name."<br/>".Yii::t('app', 'PM: ');
        if (isset($this->event->manager))
            $description .=$this->event->manager->displayLabel."<br/>".Yii::t('app', 'Przypisani:');
            $users = "[";
            foreach ($this->eventUserPlannedWrokingTimes as $eu)
            {
                if ($users != "[")
                    $users .=", ";
                $users .=$eu->user->getInitials();
                $description .= $eu->user->displayLabel.", ";
            }
            $description .= "<br/>".$this->event->description;
        $users .="]";
        $whole = false;
            if ((substr($this->end_time, 11, 8)==substr($this->start_time, 11, 8))&&(substr($this->start_time, 11, 8)=="00:00:00"))
            {
                $whole = true;
            }

        $att = count($this->event->attachments);
        $notes = count($this->event->customerNotes);
        return ['title'=> "[".$this->name."] ".$this->event->name, 'type'=>'event', 'id'=>$this->id, 'e_id'=>$this->event_id, 'start'=>substr($this->start_time, 0, 10)."T".substr($this->start_time, 11, 8), 'end'=>substr($this->end_time, 0, 10)."T".substr($this->end_time, 11, 8), 'className'=>'event typ-'.$this->event->type.' status-'.$this->event->status, 'notes'=>$notes, 'users'=>$users, 'files'=>$att, 'allDay'=>$whole, 'description'=>$description, 'color'=>$this->color];
    }
    
}
