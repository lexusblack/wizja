<?php

namespace common\models;

use Yii;
use \common\models\base\Packlist as BasePacklist;

/**
 * This is the model class for table "packlist".
 */
class Packlist extends BasePacklist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ]);
    }

    public function getScheduleDiv()
    {
        $event = $this->event;
        $return = "<div style='height:30px; width:100%;' id='schedule-box'>";

        $width = 0;
        if ($event->eventSchedules)
            $width = 100/(count($event->eventSchedules));
        foreach ($event->eventSchedules as $schedule)
        {
            if ($schedule->start_time)
            {
                if ($schedule->prefix)
                    $prefix = $schedule->prefix;
                else
                    $prefix = substr($schedule->name, 0, 1);
                if ($schedule->color)
                    $color_style = "background-color:".$schedule->color.";";
                else
                    $color_style = "background-color:#aaaaaa;";

                if (($this->start_time<=$schedule->end_time)&&($this->end_time>=$schedule->start_time))
                    $checked = " checked";
                else
                    $checked = "";
                $return .="<div style='width:".$width."%; height:100%; color:white;".$color_style."' class='manage-crew-div'>".$prefix."<input type='checkbox'  name='schedule_".$schedule->id."' class='schedule-checkbox-packlist' data-start='".substr($schedule->start_time,0, 16)."' data-end='".substr($schedule->end_time,0, 16)."' data-schedule-id=".$schedule->id." ".$checked."/></div>";
            }

        }
        $return .= "</div>";
        return $return;
    }

    public function getGearsByCategories()
    {
        $gears = PacklistGear::find()->where(['packlist_id'=>$this->id])->all();
        $return_array = [];
        foreach ($gears as $gear)
        {
            $cat = $gear->eventGear->gear->category->getMainCategory();
            if (!isset($return_array[$cat->id]))
            {
                $return_array[$cat->id] = [];
                $return_array[$cat->id]['cat'] = $cat;
                $return_array[$cat->id]['items'] = [];
            }
            $return_array[$cat->id]['items'][] = ['id'=>$gear->id, 'name'=>$gear->eventGear->gear->name, 'item'=>$gear, 'type'=>'gear'];
        }
        $gears = PacklistOuterGear::find()->where(['packlist_id'=>$this->id])->all();
        foreach ($gears as $gear)
        {
            $cat = $gear->eventOuterGear->outerGear->outerGearModel->category->getMainCategory();
            if (!isset($return_array[$cat->id]))
            {
                $return_array[$cat->id] = [];
                $return_array[$cat->id]['cat'] = $cat;
                $return_array[$cat->id]['items'] = [];
            }
            $return_array[$cat->id]['items'][] = ['id'=>$gear->id, 'name'=>$gear->eventOuterGear->outerGear->outerGearModel->name, 'item'=>$gear, 'type'=>'outer_gear'];
        }
        $gears = PacklistExtra::find()->where(['packlist_id'=>$this->id])->all();
        foreach ($gears as $gear)
        {
            $cat = $gear->eventExtraItem->gearCategory->getMainCategory();
            if (!isset($return_array[$cat->id]))
            {
                $return_array[$cat->id] = [];
                $return_array[$cat->id]['cat'] = $cat;
                $return_array[$cat->id]['items'] = [];
            }
            $return_array[$cat->id]['items'][] = ['id'=>$gear->id, 'name'=>$gear->eventExtraItem->name, 'item'=>$gear, 'type'=>'extra'];
        }
        foreach ($return_array as $id=>$val)
        {
            $sortArray = [];
            foreach ($return_array[$id]['items'] as $i)
            {
                $sortArray[] = $i['name'];
            }
            array_multisort($sortArray,SORT_ASC,$return_array[$id]['items']);
        }
        return $return_array;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert)
        {
            if (((isset($changedAttributes['start_time']))&&($this->start_time!=$changedAttributes['start_time']))||((isset($changedAttributes['end_time']))&&($this->start_time!=$changedAttributes['end_time'])))
            {
                $gears = PacklistGear::find()->where(['packlist_id'=>$this->id])->all();
                foreach ($gears as $gear)
                {
                    if (($this->start_time>=$gear->start_time)&&($this->end_time<=$gear->end_time))
                    {
                        //nowy czas jest mniejszy więc zmieniamy
                        $gear->start_time = $this->start_time;
                        $gear->end_time = $this->end_time;
                        $gear->save();
                    }else{
                        $service = $gear->gear->getInService();
                        if (($this->start_time>$gear->end_time)||($this->end_time<$gear->start_time))
                        {
                            //czasy się nie pokrywają, więc sprawdzamy nowy okres i próbujemy zarezerować
                            $available = $gear->gear->getAvailabe($this->start_time, $this->end_time);
                            $missing = $gear->quantity - $available;

                        }else{
                            $missing = 0;
                            if ($this->start_time<$gear->start_time)
                            {
                                $available = $gear->gear->getAvailabe($this->start_time, $gear->start_time);
                                $missing = $gear->quantity - $available;
                            }
                            if ($this->end_time>$gear->start_time)
                            {
                                $available = $gear->gear->getAvailabe($gear->end_time, $this->end_time);

                                $missing2 = $gear->quantity - $available;
                                if ($missing2>$missing)
                                    $missing=$missing2;
                            }
                        }
                        $missing = $missing+$service;
                        if ($missing>0)
                        {
                            //robimy konflikt
                            $gear->start_time = $this->start_time;
                            $gear->end_time = $this->end_time;
                            $quantity = $gear->quantity;
                            if ($missing>=$quantity)
                            {
                                $gear->quantity = 0;
                            }else{
                                $gear->quantity = $gear->quantity-$missing;
                            }
                            
                            $gear->save();
                            $conflict = EventConflict::find()->where(['packlist_gear_id'=>$gear->id])->andWhere(['resolved'=>0])->one();
                            if ($conflict)
                            {
                                $conflict->added = $gear->quantity;
                                $conflict->quantity = $conflict->quantity+$missing;
                                $conflict->save();
                            }else{
                                EventConflict::deleteAll(['packlist_gear_id'=>$gear->id]);
                                $conflict = new EventConflict();
                                $conflict->event_id = $gear->packlist->event_id;
                                $conflict->packlist_gear_id = $gear->id;
                                $conflict->gear_id = $gear->gear_id;
                                $conflict->added = $gear->quantity;
                                $conflict->quantity = $missing;
                                $conflict->save();
                            }
                        }else{
                            //rezerwujemy
                            $gear->start_time = $this->start_time;
                            $gear->end_time = $this->end_time;
                            $gear->save();
                        }
                    }
                }
            }  
            if (((isset($changedAttributes['blocked']))&&($this->blocked!=$changedAttributes['blocked'])))
            {
                $log = new EventLog();
                $log->event_id = $this->event_id;
                $log->user_id = Yii::$app->user->id;
                if ($this->blocked)
                    $log->content = Yii::t('app', 'Paklista ').$this->name.Yii::t('app', ' została zablokowana.');
                else
                    $log->content = Yii::t('app', 'Paklista ').$this->name.Yii::t('app', ' została odblokowana.');
                $log->save();
            }      
        }

    }
	
}
