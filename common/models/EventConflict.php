<?php

namespace common\models;

use Yii;
use \common\models\base\EventConflict as BaseEventConflict;
use yii\bootstrap\Html;

/**
 * This is the model class for table "event_conflict".
 */
class EventConflict extends BaseEventConflict
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id', 'gear_id', 'quantity', 'resolved', 'added'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ]);
    }

    public function checkConflict()
    {
        $eg = PacklistGear::find()->where(['id'=>$this->packlist_gear_id])->one();
        $start = $eg->start_time;
        $end = $eg->end_time; 
        $quantity = $this->gear->getAvailableDateChanged($start, $end, $this->id, 'event');
        $total = $this->quantity+$this->added;
        if ($quantity>=$total)
            {return 1;}else{
                if ($quantity>$this->added)
                {
                    return 2;
                }else{
                    return 0;
                }
                return false;
            }
    }

    public function resolveConflict()
    {
        $eg = PacklistGear::findOne($this->packlist_gear_id);
        $start = $eg->start_time;
        $end = $eg->end_time; 
        $quantity = $this->gear->getAvailableDateChanged($start, $end, $eg->id, 'event');
        $total = $this->quantity+$this->added;
        if ($quantity>=$total)
            {
                $eg->quantity = $total;
                $eg->save();
                $this->resolved = 1;
                $this->save();
                return true;
            }else{
                return false;
            }

    }

    public function resolveConflictPartial()
    {
        $eg = PacklistGear::findOne($this->packlist_gear_id);
        $start = $eg->start_time;
        $end = $eg->end_time; 
        $quantity = $this->gear->getAvailableDateChanged($start, $end, $eg->id, 'event');
        $total = $this->quantity+$this->added;
        if ($quantity>=$total)
            {
                $eg->quantity = $total;
                $eg->save();
                $this->resolved = 1;
                $this->save();
                Note::createNote(2, 'eventConflictPartialResolved', $this, $this->event_id);
                return true;
            }else{
                if ($quantity>$this->added)
                    {
                        
                        $eg->quantity = $quantity;
                        $eg->save();
                        $this->added = $quantity;
                        $this->quantity = $total - $quantity;
                        $this->save();
                        Note::createNote(2, 'eventConflictPartialResolved', $this, $this->event_id);
                        return true;
                    }                
            }
            return false;
    }

    public function getEventsConflicted()
    {
        $start = $this->event->getTimeStart();
        $end = $this->event->getTimeEnd();
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-10 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+10 days" ) );
        $eg = PacklistGear::findOne($this->packlist_gear_id);

        if ($eg)
        {
            $start = $eg->start_time;
            $end = $eg->end_time;          
        }
        $gears = PacklistGear::find()->where(['gear_id'=>$this->gear_id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<>','id',$this->packlist_gear_id])->andWhere(['<', 'start_time', $checkend])->all();
        $gearArray = "[";
        $n = $this->quantity+$this->added;
            $tmp = "{title: '".$this->event->name." (".$this->added."/".$n.")', id:".$this->event_id.", resourceId:'b', start:'".substr($start, 0, 10)."T".substr($start, 11, 8)."', end:'".substr($end, 0, 10)."T".substr($end, 11, 8)."', backgroundColor:'#ff9999'},";
            $gearArray .= $tmp;       
        foreach ($gears as $gear)
        {
            $conflict = EventConflict::find()->where(['event_id'=>$gear->event_id, 'gear_id'=>$gear->gear_id, 'resolved'=>0])->one();
            $quantity = $gear->quantity;
            if ($conflict)
                $quantity= $gear->quantity+$conflict->quantity;
            $tmp = "{title: '".$gear->event->name." (".$gear->quantity."/".$quantity.")', id:".$gear->event_id.", resourceId:'b', start:'".substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8)."', end:'".substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8)."'},";
            $gearArray .= $tmp;
        }
        $gears = RentGear::find()->where(['gear_id'=>$this->gear_id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        foreach ($gears as $gear)
        {
            $tmp = "{title: '".$gear->rent->name." (".$gear->quantity."/".$gear->quantity.")', id:".$gear->rent_id.", resourceId:'c', start:'".substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8)."', end:'".substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8)."', backgroundColor:'#1c84c6'},";
            $gearArray .= $tmp;
        }
        $gearArray .=$this->gear->getAvability($checkstart, $checkend);
        $gearArray .="]";
        return $gearArray;
    }

    public function getEventsConflictedArray()
    {
        $start = $this->event->getTimeStart();
        $end = $this->event->getTimeEnd();
        $checkstart = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $start ) ) . "-10 days" ) );
        $checkend = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $end ) ) . "+10 days" ) );
        $eg = PacklistGear::findOne($this->packlist_gear_id);
        $array2 =[];
        $array2[]=['id'=>'a', 'title'=>Yii::t('app', 'Dostępność')];
        $array2[]=['id'=>'e'.$this->event_id, 'title'=>$this->event->name];
        $ids = [];
        if ($eg)
        {
            $start = $eg->start_time;
            $end = $eg->end_time;          
        }
        $gears = PacklistGear::find()->where(['gear_id'=>$this->gear_id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<>','id', $this->packlist_gear_id])->andWhere(['<', 'start_time', $checkend])->all();
        $gearArray = [];
        $n = $this->quantity+$this->added;
        $gear = $eg;
        $tmp = ['title'=>$gear->packlist->name." (".$this->added."/".$n, 'id'=>$gear->id, 'resourceId'=>'e'.$this->event_id, 'start'=>substr($start, 0, 10)."T".substr($start, 11, 8), 'end'=>substr($end, 0, 10)."T".substr($end, 11, 8), 'backgroundColor'=>'#ed5565'] ;
        $gearArray[] = $tmp;  

        foreach ($gears as $gear)
        {
            $conflict = EventConflict::find()->where(['packlist_gear_id'=>$gear->id, 'resolved'=>0])->one();
            $start = strtotime($gear->start_time);
            $end = strtotime($gear->end_time);
            $ids[] = $gear->packlist->event_id;
            $full_time = $end - $start;
            $quantity = $gear->quantity;
            $background = "#1ab394";
            if ($conflict){
                $quantity= $gear->quantity+$conflict->quantity;
                $background = "#ed5565";
            }
            $tmp = ['title'=>$gear->packlist->name." (".$gear->quantity."/".$quantity.")", 'id'=>$gear->id, 'resourceId'=>'e'.$gear->packlist->event_id, 'start'=>substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8), 'end'=>substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8), 'backgroundColor'=>$background] ;
            $gearArray[] = $tmp;
        }
        $gears = RentGear::find()->where(['gear_id'=>$this->gear_id])->andWhere(['>', 'end_time', $checkstart])->andWhere(['<', 'start_time', $checkend])->all();
        foreach ($gears as $gear)
        {
            $tmp = ['title'=>$gear->rent->name." (".$gear->quantity."/".$gear->quantity.")", 'id'=>$gear->rent_id, 'resourceId'=>'r'.$gear->rent_id, 'start'=>substr($gear->start_time, 0, 10)."T".substr($gear->start_time, 11, 8), 'end'=>substr($gear->end_time, 0, 10)."T".substr($gear->end_time, 11, 8), 'backgroundColor'=>'#1c84c6'] ;
            $gearArray[] = $tmp;
            $array2[] = ["id"=>'r'.$geear->rent_id, "title"=>$gear->rent->name];

        }
        $events = Event::find()->where(['id'=>$ids])->asArray()->all();
        foreach ($events as $e)
            {
                $array2[] = ['id'=>'e'.$e['id'], 'title'=>$e['name']];
            }
        $gearArray = array_merge($gearArray, $this->gear->getAvabilityArray($checkstart, $checkend));     
        
        return ['events'=>$gearArray, 'resources'=>$array2];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            Note::createNote(2, 'eventConflictCreated', $this, $this->event_id);
            $eventlog = new EventLog;
                        $eventlog->event_id = $this->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Utworzono konflikt ").$this->gear->name." na".$this->quantity." szt.";
                        $eventlog->save();
        }
        else{
            if ($this->resolved==1){
                Note::createNote(2, 'eventConflictResolved', $this, $this->event_id);
                $eventlog = new EventLog;
                        $eventlog->event_id = $this->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Rozwiązano konflikt ").$this->gear->name;
                        $eventlog->save();
            }

        }

    }
	
}
