<?php

namespace common\models;

use backend\modules\permission\models\BasePermission;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\form\CalendarSearch;
use \DateTime;
use function igorw\retry;
use kartik\helpers\Html;
use Yii;
use \common\models\base\Event as BaseEvent;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\validators\NumberValidator;
use common\models\interfaces\EventInterface;
use Zend\Validator\Date;

/**
 * This is the model class for table "event".
 */
class Event extends BaseEvent implements EventInterface
{
    public $eventDateRange;
    public $packingDateRange;
    public $montageDateRange;
    public $readinessDateRange;
    public $practiceDateRange;
    public $disassemblyDateRange;
    public $schedule_type;
    public $packlist_schema;
    public $departmentIds;
    public $userIds;
    public $projectStatus;

    const PROVISION_TYPE_PROFIT = 1;
    const PROVISION_TYPE_OFFER = 2;

    const INVOICE_VALUE_NONE = 0;
    const INVOICE_VALUE_EQUAL = 10;
    const INVOICE_VALUE_LESS = 5;
    const INVOICE_VALUE_GREATER = 15;

    const PROJECT_PAID_NONE = 0;
    const PROJECT_PAID_PARTIAL = 5;
    const PROJECT_PAID_ALL = 10;

    const NOTIFICATIONS_ON = 1;
    const NOTIFICATIONS_OFF = 0;

    public static $dateAttributesBase = [
        'event',
        'packing',
        'montage',
        'practice',
        'readiness',
        'disassembly'
    ];

    public function getPurhaseListItemNumber()
    {
        return PurchaseListItem::find()->where(['event_id'=>$this->id, 'status'=>0])->count()."/".PurchaseListItem::find()->where(['event_id'=>$this->id])->count();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'departmentIds',
            ],
            'relations' => [
                'departments',
            ],
            'modelClasses'=>[
                'common\models\Department',
            ],
        ];

        $behaviors['eventDatesBehavior'] = [
            'class'=>\common\behaviors\EventDatesBehavior::className(),
        ];

        $behaviors['codeBehavior'] = [
            'class'=>\common\behaviors\CodeBehavior::className(),
            'prefix' => 'E',
        ];

        return $behaviors;
    }

        public function getScheduleDiv()
    {
        $event = $this;
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

                    $checked = " checked";

                $return .="<div style='width:".$width."%; height:100%; color:white;".$color_style."' class='manage-crew-div'>".$prefix."<input type='checkbox'  name='schedule_".$schedule->id."' class='schedule-checkbox-packlist' data-start='".substr($schedule->start_time,0, 16)."' data-end='".substr($schedule->end_time,0, 16)."' data-schedule-id=".$schedule->id." ".$checked."/></div>";
            }

        }
        $return .= "</div>";
        return $return;
    }

        public function saveSchedule($type, $schedules, $event, $offer=null)
    {
        //return true;
        if ((($event)||($offer))&&(!$type))
        {
            if ($offer)
            {
            foreach ($offer->offerSchedules as $m)
            {
                $model = new EventSchedule();
                $model->attributes = $m->attributes;
                $model->event_id = $this->id;
                if ($schedules[$m->id]['dateRange'])
                {
                    $dates = explode(" - ", $schedules[$m->id]['dateRange']);
                    $model->start_time = $dates[0];
                    $model->end_time = $dates[1];
                }
                $model->save();
            }
            }
            if ($event)
            {
            foreach ($event->eventSchedules as $m)
            {
                $model = new EventSchedule();
                $model->attributes = $m->attributes;
                $model->event_id = $this->id;
                if ($schedules[$m->id]['dateRange'])
                {
                    $dates = explode(" - ", $schedules[$m->id]['dateRange']);
                    $model->start_time = $dates[0];
                    $model->end_time = $dates[1];
                }
                $model->save();
            }
            }

        }else{
            $models = \common\models\Schedule::find()->where(['schedule_type_id'=>$type])->orderBy(['position'=>SORT_ASC])->all();
            foreach ($models as $m)
            {
                $model = new EventSchedule();
                $model->attributes = $m->attributes;
                $model->event_id = $this->id;
                if ($schedules[$m->id]['dateRange'])
                {
                    $dates = explode(" - ", $schedules[$m->id]['dateRange']);
                    $model->start_time = $dates[0];
                    $model->end_time = $dates[1];
                }
                $model->save();
            }
        }

    }


    public function updateSchedule()
    {
        $models = \common\models\EventSchedule::find()->where(['event_id'=>$this->id])->andWhere(['<>', 'start_time', ''])->orderBy(['start_time'=>SORT_ASC])->all();
        $first = true;
        $start = "";
        $end = "";
        foreach ($models as $m)
        {
            if ($first)
            {
                $start = $m->start_time;
                $end = $m->end_time;
                $first = false;
            }else{
                if ($m->start_time < $start)
                {
                    $start = $m->start_time;
                }
                if ($m->end_time > $end)
                {
                    $end = $m->end_time;
                }
            }
        }
        $this->event_start = $start;
        $this->event_end = $end;
        if ($start==$end)
            $this->event_end = substr($start, 0, 11)."23:00:00";
        /*
        $models = \common\models\EventSchedule::find()->where(['event_id'=>$this->id])->andWhere(['<>', 'start_time', ''])->andWhere(['book_gears'=>1])->orderBy(['start_time'=>SORT_ASC])->all();
        $first = true;
        $start = "";
        $end = "";
        foreach ($models as $m)
        {
            if ($first)
            {
                $start = $m->start_time;
                $end = $m->end_time;
                $first = false;
            }else{
                if ($m->start_time < $start)
                {
                    $start = $m->start_time;
                }
                if ($m->end_time > $end)
                {
                    $end = $m->end_time;
                }
            }
        }
        $this->montage_start = $start;
        $this->montage_end = $end;
        */
        $gears = \common\models\EventGear::find()->where(['event_id'=>$this->id])->one();
        if ($gears)
        {
            $this->packing_type = 1;
        }
        
        $this->save();
    }


    public function prepareForCalendar()
    {
        $description = $this->name."<br/>".Yii::t('app', 'Autor: ').$this->creator->displayLabel."<br/>".Yii::t('app', 'Przypisani:');
            $users = "[";
            foreach ($this->eventUsers as $eu)
            {
                if ($users != "[")
                    $users .=", ";
                $users .=$eu->user->getInitials();
                $description .= $eu->user->displayLabel.", ";
            }
            if ($this->getTaskFor())
            {

                 $description .= "<br/>".Yii::t('app', 'Na potrzeby: ').$this->getTaskFor()->event->name;
            }
            $description .= "<br/>".$this->description;
        $users .="]";
        $whole = false;
            if ((substr($this->event_end, 11, 8)==substr($this->event_start, 11, 8))&&(substr($this->event_start, 11, 8)=="00:00:00"))
            {
                $whole = true;
            }

        $att = count($this->attachments);
        $notes = count($this->customerNotes);
        return ['title'=> $this->name, 'type'=>'event', 'id'=>$this->id, 'start'=>substr($this->event_start, 0, 10)."T".substr($this->event_start, 11, 8), 'end'=>substr($this->event_end, 0, 10)."T".substr($this->event_end, 11, 8), 'className'=>'event typ-'.$this->type.' status-'.$this->status, 'notes'=>$notes, 'users'=>$users, 'files'=>$att, 'allDay'=>$whole, 'description'=>$description];
    }

    public function getEventByProject($events)
    {
        $projects = [];
        if ($events)
        {
            $ids = [];
            foreach ($events as $event)
            {
                    if ($event->getTaskFor())
                        $ids[] = $event->getTaskFor()->event_id;
            }
            $projects = Event::find()->where(['id'=>$ids])->orderBy(['event_start'=>SORT_DESC])->all();
        }
        return $projects;
    }

    public function getTaskFor()
    {
        $ids = ArrayHelper::map(EventTask::find()->where(['event_id'=>$this->id])->asArray()->all(), 'task_id', 'task_id');
        $task = Task::find()->where(['id'=>$ids])->one();
        if ($task)
            return $task;
        else
            return null;
    }

    public function getTaskStatus()
    {
        $task = Task::find()->where(['event_id'=>$this->id])->count();
        $task_done =  Task::find()->where(['event_id'=>$this->id])->andWhere(['status'=>10])->count();
        if ($task==0)
            return ['task'=>$task, 'done'=>$task_done, 'status'=>0];
        else
            return ['task'=>$task, 'done'=>$task_done, 'status'=>intval($task_done/$task*100)];
    }


    public function rules()
    {
        $rules = [
            [['eventDateRange', 'packingDateRange', 'montageDateRange', 'readinessDateRange', 'practiceDateRange', 'disassemblyDateRange'], 'string'],
            [['departmentIds', 'userIds'], 'each', 'rule'=>['integer']],
            [['event_start'], 'validateDates', 'skipOnEmpty'=>false],
            [['projectStatus'], 'string'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function beforeDelete()
    {
        Note::createNote(2, 'eventDelete', $this, $this->id);
        return true;
    }

    public function beforeValidate()
    {
        $intValidator = new NumberValidator();
        $intValidator->integerOnly = true;

        if (empty($this->customer_id) == false && $intValidator->validate($this->customer_id) == false)
        {
            $customer = new Customer();
            $customer->name = $this->customer_id;
            $customer->save();
            $this->customer_id = $customer->id;
        }

        if (empty($this->contact_id) == false && $intValidator->validate($this->contact_id) == false && empty($this->customer_id) == false)
        {
            /*$contact = new Contact();
            $contact->name = $this->contact_id;
            $contact->customer_id = $this->customer_id;
            $contact->save();
            $this->contact_id = $contact->id;*/
            
        }

        if (empty($this->location_id) == false && $intValidator->validate($this->location_id) == false)
        {
            $location = new Location();
            $location->name = $this->location_id;
            $location->save();
            $this->location_id = $location->id;
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($insert== false)
        {
            $oldModel = static::findOne($this->id);

        }

        if ( ($insert==true && $this->offer_sent==1) ||
            ($insert==false && $oldModel->offer_sent!=$this->offer_sent && $this->offer_sent==1) )
        {
            $this->offerHasBeenSent();
        }

        if ( ($insert==true && $this->ready_to_invoice==1) ||
            ($insert==false && $oldModel->ready_to_invoice!=$this->ready_to_invoice && $this->ready_to_invoice==1) )
        {
            $this->ready_to_invoice_date = new Expression('NOW()');
            $this->ready_to_invoice_user_id = Yii::$app->user->id;
        }

        if ( ($insert==true && $this->expense_entered==1) ||
            ($insert==false && $oldModel->expense_entered!=$this->expense_entered && $this->expense_entered==1) )
        {
            $this->expense_entered_date = new Expression('NOW()');
            $this->expense_entered_user_id = Yii::$app->user->id;
        }
        if ($insert)
        {
            $this->create_time = date('Y-m-d H:i:s');
            $statut = EventStatut::find()->where(['type'=>1, 'active'=>1])->orderBy(['position'=>SORT_ASC])->one();
            if ($statut)
                $this->status = $statut->id;
            else
                $this->status = null; 
        }

        $this->removeDateAttributes();

        $this->updateIvoiceIssued();
        $this->updateExpenseStatus();
        $this->updateStatutes();

        if ($insert)
        {
            if ((Yii::$app->params['companyID']=="djak")&&($this->details!=''))
            {
                $this->details =  '<table class="table table-bordered"><tbody><tr> <td colspan="2">TECHNICZNY/ELEKTRYK</td> </tr> <tr> <td style="width:200px;">techniczny elektryk</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td>mail.</td> <td> </td> </tr> <tr> <td>źródło zasilania</td> <td> </td> </tr> <tr> <td>rodzaj przyłącza</td> <td> </td> </tr> <tr> <td>odległość od sceny</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">SCENA</td> </tr> <tr> <td>scena</td> <td> </td> </tr> <tr> <td>osoba kontaktowa</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td>mail</td> <td> </td> </tr> <tr> <td>opis sceny</td> <td> </td> </tr> <tr> <td>kontak podniesienie dachu</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">HOTEL</td> </tr> <tr> <td>hotel</td> <td> </td> </tr> <tr> <td>adres</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td></td> <td></td> </tr> <tr> <td colspan="2">WYŻYWIENIE</td> </tr> <tr> <td>wyżywienie</td> <td> </td> </tr> <tr> <td>osoba kontaktowa</td> <td> </td> </tr> <tr> <td>tel.</td> <td> </td> </tr> <tr> <td colspan="2">MULTIMEDIA</td> </tr> <tr> <td>Firma</td> <td> </td> </tr> <tr> <td>Telefon</td> <td> </td> </tr> <tr> <td>Kamery</td> <td> </td> </tr> <tr> <td>Realizacja</td> <td> </td> </tr> <tr> <td colspan="2">TRANSPORT</td> </tr> <tr> <td>Samochód</td> <td> </td> </tr> <tr> <td>Kierowca</td> <td> </td> </tr> <tr> <td>Telefon</td> <td> </td> </tr> <tr> <td colspan="2">INNE</td> </tr> <tr> <td>Ubiór</td> <td> </td> </tr></tbody></table>';
            }
        }

        return parent::beforeSave($insert);
    }

    public function copyProvisions()
    {
        EventProvision::deleteAll(['event_id'=>$this->id]);
        if ($this->manager_id)
        {
                $provisions = UserProvision::find()->where(['user_id'=>$this->manager_id])->all();
                foreach ($provisions as $p)
                {
                    $ep = new EventProvision();
                    $ep->event_id = $this->id;
                    $ep->section = $p->section;
                    $ep->value = $p->value;
                    $ep->type = $p->type;
                    $ep->save();
                }
                if (!$provisions)
                {
                       $sections = GearCategory::getMainList();
                        $sections[] = Yii::t('app', 'Obsługa');
                        $sections[] = Yii::t('app', 'Transport');
                        $sections[] = Yii::t('app', 'Inne');
                        foreach ($sections as $s)
                        {
                            $up = new EventProvision();
                            $up->event_id = $this->id;
                            $up->section = $s;
                            $up->value = 0;
                            $up->type = 1;
                            $up->save();
                        }     
                }
        }else{
        $sections = GearCategory::getMainList();
        $sections[] = Yii::t('app', 'Obsługa');
        $sections[] = Yii::t('app', 'Transport');
        $sections[] = Yii::t('app', 'Inne');
        foreach ($sections as $s)
        {
            $up = new EventProvision();
            $up->event_id = $this->id;
            $up->section = $s;
            $up->value = 0;
            $up->type = 1;
            $up->save();
        }
        }
    }

    public function getProvisions()
    {
        return EventProvision::find()->where(['event_id'=>$this->id])->all();
    }

    public function getTotalProvision()
    {
        $values = $this->getEventValueAll();
        $profits = $this->getEventProfits();
        $provisions = $this->getProvisions();
        $sum_prov = 0;
        foreach ($provisions as $p)
        {
                
                                if ($p->type==1){
                                        if (isset($profits[$p->section]))
                                            $val= $p->value/100*$profits[$p->section];
                                        else
                                            $val = 0;
                                    }else{
                                        if (isset($values[$p->section]))
                                            $val= $p->value/100*$values[$p->section];
                                        else
                                            $val = 0;
                                    }   
                                $sum_prov +=$val;

                
                           
        }
        return $sum_prov;
    }

    public function getProvisionPM($profits)
    {
        $provisions = $this->getProvisions();
        $values = $this->getEventValueAll();
        $sum_prov = 0;
        $sections = [];
        foreach ($profits as $k=>$v)
        {
            $sections[$k] = 0;
        }
        foreach ($provisions as $p)
        {
                
                                if ($p->type==1){
                                        if (isset($profits[$p->section]))
                                            $val= round($p->value/100*$profits[$p->section],2);
                                        else
                                            $val = 0;
                                    }else{
                                        if (isset($values[$p->section]))
                                            $val= round($p->value/100*$values[$p->section],2);
                                        else
                                            $val = 0;
                                    }  
                                $sections[$p->section] +=$val;
                                $sum_prov +=$val;

                
                           
        }
        $sections[Yii::t('app', 'Suma')] = $sum_prov;
        return ['value'=>$sum_prov, 'sections'=>$sections];
    }

    public function getProvisionBySections()
    {
        $values = $this->getEventValueAll();
        $profits = $this->getEventProfits();
        $provisions = $this->getProvisions();
        $sum_prov = 0;
        $return = [];
        foreach ($provisions as $p)
        {
                
                                if ($p->type==1){
                                        if (isset($profits[$p->section]))
                                            $val= $p->value/100*$profits[$p->section];
                                        else
                                            $val = 0;
                                    }else{
                                        if (isset($values[$p->section]))
                                            $val= $p->value/100*$values[$p->section];
                                        else
                                            $val = 0;
                                    } 
                                $return[$p->section] = $val;  
                                $sum_prov +=$val;

                
                           
        }
        $return[Yii::t('app', 'Suma')] = $sum_prov;
        return $return;        
    }    

    public function getSectionProvision($user)
    {
        $values = $this->getEventValueAll();
        $profits = $this->getEventProfits();
        $provisions = $this->getProvisions();
        $sum_prov = 0;
        foreach ($provisions as $p)
        {
                $up = UserProvision::find()->where(['user_id'=>$user->id])->andWhere(['section'=>$p->section])->one();
                if ($up)
                {
                        if ($up->event_type==2)
                        {
                            if ($up->type==1){
                                if (isset($profits[$p->section]))
                                    $val= $up->value/100*$profits[$p->section];
                                else
                                    $val = 0;
                            }else{
                                if (isset($values[$up->section]))
                                    $val= $up->value/100*$values[$p->section];
                                else
                                    $val = 0;
                            }   
                        $sum_prov +=$val;  
                    }
                }
          
        }
        return $sum_prov;
    }

    public function createMainPacklist()
    {
        $packlist = new Packlist();
        $packlist->event_id = $this->id;
        $packlist->start_time = $this->event_start;
        $packlist->end_time = $this->event_end;
        $packlist->name = Yii::t('app', 'Główna grupa sprzętu');
        $packlist->color = "#222222";
        $packlist->main = 1;
        $packlist->save();
    }

    public function createPacklists($id=null)
    {
        $this->updateSchedule();
        if ($id)
        {
            $schema = PacklistSchema::findOne($id);
            $first = true;
            foreach ($schema->packlistSchemaItems as $item)
            {
                $packlist = new Packlist();
                $packlist->event_id = $this->id;
                $packlist->start_time = $this->event_start;
                $packlist->end_time = $this->event_end;
                $packlist->name = $item->name;
                $packlist->color = $item->color;
                if ($first)
                {
                    $first=false;
                    $packlist->main = 1;
                }
                $packlist->save();
            }
        }else{
            $this->createMainPacklist();
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
        {
                $customer = Customer::findOne($this->customer_id);     
                $customer->createLog('event_create', $this->id);
                Note::createNote(2, 'eventCreate', $this, $this->id);
                $this->copyProvisions();
                $this->copyProvisionGroups();
                $this->saveExtraFields();
                $this->sendStatusReminder();
                $this->saveAdditionalStatuts();
                
        }else{
            if ((isset($changedAttributes['description']))&&($this->description!=$changedAttributes['description']))
            {
                Note::createNote(2, 'eventDescriptionChanged', $this, $this->id);
            }
            if ((isset($changedAttributes['manager_id']))&&($this->manager_id!=$changedAttributes['manager_id']))
            {
                $this->copyProvisions();
            }
             
            if ((isset($changedAttributes['project_id']))&&($this->project_id!=$changedAttributes['project_id']))
            {
                if ($this->project_id)
                    Note::createNote(1, 'eventToProject', $this, $this->project_id);
                else
                    Note::createNote(1, 'eventFromProject', $this, $changedAttributes['project_id']);
            }  
            if ((isset($changedAttributes['status']))&&($this->status!=$changedAttributes['status']))
            {
                //wysyłamy powiadomienie
                Note::createNote(2, 'eventStatusChanged', $this, $this->id);
                $this->sendStatusReminder();
                $this->changeBookings();
            }         
        }
        EventGearItem::updateTimesForEvent($this);

    }

    public function saveAdditionalStatuts()
    {
        foreach (\common\models\EventAdditionalStatut::find()->where(['active'=>1])->all() as $s)
            {
                $st = \common\models\EventAdditionalStatutName::find()->where(['active'=>1, 'event_additional_statut_id'=>$s->id])->orderBy(['position'=>SORT_ASC])->one();
                if ($st)
                {
                    $m = new \common\models\EventASResult();
                    $m->event_id = $this->id;
                    $m->event_additional_statut_id = $s->id;
                    $m->event_additional_statut_name_id = $st->id;
                    $m->save();


                }
            }
    }

    public function copyProvisionGroups()
    {
        //usuwamy dotychczasowe
        EventProvisionGroup::deleteAll(['event_id'=>$this->id]);
        $provisions = ProvisionGroup::find()->all();
        foreach ($provisions as $p)
        {
            $opg = new EventProvisionGroup();
            $opg->attributes = $p->attributes;
            $opg->event_id = $this->id;
            $opg->provision_group_id = $p->id;
            $opg->save();
            foreach ($p->provisionGroupProvisions as $pgp)
            {
                $opgp = new EventProvisionGroupProvision();
                $opgp->attributes = $pgp->attributes;
                $opgp->event_provision_group_id = $opg->id;
                $opgp->save();
            }
        }
    }

    public function saveExtraFields()
    {
        $fields = EventFieldSetting::find()->all();
        foreach ($fields as $field)
        {
            if (($field->default_value!="")&&($field->type!=1))
            {
                $f = new EventField();
                $f->event_field_setting_id = $field->id;
                $f->event_id = $this->id;
                $f->value_text = $field->default_value;
                $f->save();
            }else{
                if (($field->default_value_int)&&($field->type==1))
                {
                    $f = new EventField();
                    $f->event_field_setting_id = $field->id;
                    $f->event_id = $this->id;
                    $f->value_int = $field->default_value_int;
                    $f->save();
                }
            }
        }
    }

    public function changeBookings()
    {
        if ($this->eventStatut->delete_gear)
        {
            //usuwamy wszystkie rezerwacje
            EventGear::deleteAll(['event_id'=>$this->id]);
            EventGearItem::deleteAll(['event_id'=>$this->id]);
            EventOuterGear::deleteAll(['event_id'=>$this->id]);
            EventOuterGearModel::deleteAll(['event_id'=>$this->id]);
            EventConflict::deleteAll(['event_id'=>$this->id]);
            EventExtraItem::deleteAll(['event_id'=>$this->id]);
        }

        if ($this->eventStatut->delete_crew)
        {
            EventUserPlannedBreaks::deleteAll(['event_id' => $this->id]);
            EventUserPlannedWrokingTime::deleteAll(['event_id' => $this->id]);
            $breaks = EventBreaks::findAll(['event_id'=>$this->id]);
            foreach ($breaks as $break) {
                EventBreaksUser::deleteAll(['event_break_id' => $break->id]);
            }
            EventBreaks::deleteAll(['event_id' => $this->id]);
            $eus = EventUser::findAll(['event_id'=>$this->id]);
            foreach ($eus as $eu)
            {
                 EventUserRole::deleteAll(['event_user_id' => $eu->id]);
            }
           
            EventUser::deleteAll(['event_id' => $this->id]);
        }

        if ($this->eventStatut->delete_task)
        {
            
            Task::deleteAll(['event_id'=>$this->id]);
        }
    }

    public function sendStatusReminder()
    {
        $text = Yii::t('app', 'Zmieniono status wydarzenia ').$this->name.Yii::t('app', ' na ').$this->eventStatut->name;
        $user_ids = explode(";",$this->eventStatut->reminder_users);
        $userIds2 = [];
        $role_ids = explode(";",$this->eventStatut->reminder_roles);
        if ($role_ids)
        {
            $euserIds = ArrayHelper::map(EventUserRole::find()->where(['IN', 'user_event_role_id', $role_ids])->asArray()->all(), 'event_user_id', 'event_user_id');
            if ($euserIds)
                $userIds2 = ArrayHelper::map(EventUser::find()->where(['IN', 'id', $euserIds])->andWhere(['event_id'=>$this->id])->asArray()->all(), 'user_id', 'user_id');
        }
        
        $userIds = array_merge($user_ids, $userIds2);
        if ($this->eventStatut->reminder_pm)
        {
            $pm = [$this->manager_id];
            $userIds = array_merge($pm, $userIds);
        }
        $users = User::find()->where(['IN', 'id', $userIds])->all();
        foreach ($users as $user)
        {
            if ($this->eventStatut->reminder)
            {
                    Notification::sendUserPushNotification($user, Yii::t('app', 'Powiadomienie z serwisu eventowego'), $text, Notification::PUSH_TYPE_EVENTS, $this->id);
            }
            if ($this->eventStatut->reminder_sms)
            {
                Notification::sendUserSmsNotification($user, $text, false);
            }
            if ($this->eventStatut->reminder_mail)
            {
                Notification::sendUserMailNotification($user, Yii::t('app', 'Wiadomość automatyczna'), $text." ".Html::a(Yii::t('app', 'Zobacz'), "http://".Yii::$app->getRequest()->serverName.'/admin/event/view?id='.$this->id ));
            }            
        }

    }

    public function attributeLabels()
    {
        $labels = [
            'eventDateRange' => Yii::t('app', 'Impreza'),
            'departmentIds' => Yii::t('app', 'Działy'),
            'packingDateRange' => (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Załadunek') : Yii::t('app', 'Pakowanie'),
            'montageDateRange' => Yii::t('app', 'Montaż'),
            'readinessDateRange' => Yii::t('app', 'Gotowość'),
            'practiceDateRange' => Yii::t('app', 'Próby'),
            'disassemblyDateRange' => Yii::t('app', 'Demontaż'),
            'packing_type' => Yii::t('app', 'Z godzinami'),
            'montage_type' => Yii::t('app', 'Z godzinami'),
            'readiness_type' => Yii::t('app', 'Z godzinami'),
            'practice_type' => Yii::t('app', 'Z godzinami'),
            'disassembly_type' => Yii::t('app', 'Z godzinami'),
            'tasks_schema_id' => Yii::t('app', 'Schemat zadań')
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    public function getDepartmentList($asString=false, $separator = ', ')
    {
        $list = ArrayHelper::map($this->departments, 'id', 'name');

        if ($asString == true)
        {
            $value = implode($separator, $list);
        }
        else
        {
            $value = $list;
        }

        return $value;
    }

    public static function getProjectStatusList()
    {
        $event = new static();
        $attrs = [
            'offer_sent',
            'offer_accepted',
            'ready_to_invoice',
            'expense_entered',
            'project_done',
            'invoice_issued',
            'invoice_sent',
            'transfer_booked',
        ];
        $list = [];
        foreach ($attrs as $a)
        {
            $list[$a] = $event->getAttributeLabel($a);
        }
        return $list;
    }

    public function getCustomUserWorkingHours() {
        $customHours = [];
        foreach ($this->eventUserPlannedWrokingTimes as $time) {
            if ($this->event_start == $time->start_time && $this->event_end == $time->end_time) { continue; }
            if ($this->montage_start == $time->start_time && $this->montage_end == $time->end_time) { continue; }
            if ($this->disassembly_start == $time->start_time && $this->disassembly_end == $time->end_time) { continue; }
            if (in_array([$time->start_time, $time->end_time], $customHours)) { continue; }
            $customHours[] = [$time->start_time, $time->end_time];
        }
        return $customHours;
    }

    public static function getLevelList()
    {
        $list = range(1,5);
        $list = array_combine($list, $list);
        return $list;
    }

    public function getAssignedGear($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getGearItems();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedAgencyOffers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getAgencyOffers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedGearModel($params = [], $sort="cat") {
        $gear_category = [];

        $ids = [];
        if ($sort=="cat")
        {
            $gears = EventGear::find()->where(['event_id'=>$this->id])->joinWith(['gear'])->orderBy(['gear.category_id'=>SORT_ASC, 'gear.name'=>SORT_ASC])->all();
            foreach ($gears as $gear) {

                    $category = $gear->gear->category;
                    $categories = $category->parents()->all();
                    if (count($categories) > 1) {
                        $category = $categories[1];
                    }
                    $gear_category[$category->id][] = $gear;
            }

            $gears = [];
            foreach ($gear_category as $category => $items) {
                foreach ($items as $item) {
                    $gears[] = $item;
                }
            }
        }
        if (($sort=="name")||($sort=="comment"))
        {
            $gears = EventGear::find()->where(['event_id'=>$this->id])->joinWith(['gear'])->orderBy(['gear.name'=>SORT_ASC])->all();
        }
        if ($sort=="warehouse")
        {
            $gears = EventGear::find()->where(['event_id'=>$this->id])->joinWith(['gear'])->orderBy(['gear.location'=>SORT_ASC, 'gear.name'=>SORT_ASC])->all();
        }
        $provider = new ArrayDataProvider([
            'allModels' => $gears,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $provider;
        }
        
    public function getAssignedGearModelPacklist($packlist, $sort) {
        $gear_category = [];

        //$ids = ArrayHelper::map(PacklistItem::find()->where(['packlist_id'=>$packlist])->asArray()->all(), 'gear_id', 'gear_id');
        if ($sort=="cat")
        {
            $gears = PacklistGear::find()->where(['packlist_id'=>$packlist])->joinWith(['gear'])->orderBy(['gear.category_id'=>SORT_ASC, 'gear.name'=>SORT_ASC])->all();
            foreach ($gears as $gear) {

                    $category = $gear->gear->category;
                    $categories = $category->parents()->all();
                    if (count($categories) > 1) {
                        $category = $categories[1];
                    }
                    $gear_category[$category->id][] = $gear;
            }

            $gears = [];
            foreach ($gear_category as $category => $items) {
                foreach ($items as $item) {
                    $gears[] = $item;
                }
            }
        }
        if ($sort=="name")
        {
            $gears = PacklistGear::find()->where(['packlist_id'=>$packlist])->joinWith(['gear'])->orderBy(['gear.name'=>SORT_ASC])->all();

        }
        if ($sort=="warehouse")
        {
            $gears = PacklistGear::find()->where(['packlist_id'=>$packlist])->joinWith(['gear'])->orderBy([ 'gear.location'=>SORT_ASC, 'gear.name'=>SORT_ASC])->all();

        }
        if ($sort=="comment")
        {
            $gears = PacklistGear::find()->where(['packlist_id'=>$packlist])->joinWith(['gear'])->orderBy(['comment'=>SORT_DESC, 'gear.name'=>SORT_ASC])->all();

        }
        $provider = new ArrayDataProvider([
            'allModels' => $gears,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $provider;
    }

    public function getAssignedOuterGear($offer_id,$outer_gear_id) {
        return EventOuterGear::findOne(['event_id' => $offer_id, 'outer_gear_id' => $outer_gear_id ]);
    }

    public function getEOuterGears(){

        $query = $this->getEventOuterGears();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    
    }

    public function getAssignedOuterGearModel($offer_id,$outer_gear_id) {
        return EventOuterGearModel::findOne(['event_id' => $offer_id, 'outer_gear_model_id' => $outer_gear_id ]);
    }

    public function getAssignedOuterGearNumber($event_id, $outer_gear_id) {
        $model = EventOuterGear::findOne(['event_id' => $event_id, 'outer_gear_id' => $outer_gear_id]);
        if ($model == null) {
            return 0;
        }
        if ($model->quantity == null) {
            return 1;
        }
        return $model->quantity;
    }

    public function getAssignedOuterGearModelNumber($event_id, $outer_gear_id) {
        $model = EventOuterGearModel::findOne(['event_id' => $event_id, 'outer_gear_model_id' => $outer_gear_id]);
        if ($model == null) {
            return 0;
        }
        if ($model->quantity == null) {
            return 1;
        }
        return $model->quantity;
    }

    public function getAssignedOuterGears($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getOuterGears();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOuterGears2($packlist)
    {
        $gears = PacklistOuterGear::find()->where(['packlist_id'=>$packlist])->all();

        $provider = new ArrayDataProvider([
            'allModels' => $gears,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $provider;
    }

    public function getAssignedOuterGearModels($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $ids = ArrayHelper::map(EventOuterGearModel::find()->where(['resolved'=>0, 'event_id'=>$this->id])->asArray()->all(), 'outer_gear_model_id', 'outer_gear_model_id');
        $query = $this->getOuterGearModels()->where(['IN', 'id', $ids]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOuterGearModelsNumber()
    {
        $data = EventOuterGearModel::find()->where(['resolved'=>0, 'event_id'=>$this->id])->count();
        if ($data)
            return ' <span class="badge badge-warning pull-right">'.$data.'</span>';
        else
            return "";
    }

    public function getConflicts($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = EventConflict::find()->where(['event_id'=>$this->id, 'resolved'=>0]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedAttachements($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getAttachments();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedLogs()
    {
        $query = $this->getLogs();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination' => false
        ]);

        return $dataProvider;       
    }

    public function getEventInvoiceDataProvider()
    {

        $query = $this->getEventInvoices();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getInvoicesDataProvider()
    {

        $query = $this->getInvoices();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getExpensesDataProvider()
    {

        $query = $this->getExpenses();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }



    public function getAssignedUsers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getUsers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedVehicles($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getVehicles();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedOffers($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getOffers();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getSumUserWorkingTimes($period)
    {
        $query = $this->getEventUserWorkingTimes();
        if ($period)
            $query->andWhere(['type'=>$period]);
        $user = Yii::$app->user;

        if (!$user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL]))
        {
            $query->andWhere(['user_id'=>$user->id]);
        }
        $models = $query->all();
        $return = [];
        foreach ($models as $m)
        {
            if (!isset($return[$m->user_id]))
            {
                $return[$m->user_id]['name'] = $m->user->displayLabel;
                $return[$m->user_id]['hours'] = 0;
            }
            $return[$m->user_id]['hours'] += $m->duration;
        }
        return $return;
    }

    public function getUserWorkingTimes($period = null)
    {
        $query = $this->getEventUserWorkingTimes();
        if ($period)
            $query->andWhere(['type'=>$period]);
        $query->orderBy(['start_time' => SORT_ASC]);

        $user = Yii::$app->user;

        if (!$user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL]))
        {
            $query->andWhere(['user_id'=>$user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    /**
     * @param integer $userId
     * @return float|int
     */
    public function getUserWorkingTimeSalary($userId, $year=null, $month=null)
    {
        $salary = 0;
        $user = User::findOne($userId);
        $rate = $user->rate_amount;
        $type = $user->rate_type;

        if ($type > 0 && $type != User::RATE_MONTH) {
            //$periods = $user->getEventUserWorkingTimes()->where(['event_id' => $this->id])->all();
            $periods = EventUserWorkingTime::getMonth($userId, $this->id, $month, $year);
            $periods = $this->sumOverlapingPeriods($periods);
            $duration = $this->getUserWorkingTimeInPeriods($periods);

            $hours = $duration / 3600;

            // stawka za godzine
            if ($type == 1) {
                $salary = $hours * $rate;
            }
            else {
                $time4hPeriods = floor($hours / 4);
                $salary4hPeriods = floor($rate / $type * 4);
                $salary = $time4hPeriods * $salary4hPeriods;
                $salary = round($hours*$rate/$type, 2);
            }
        }

        return $salary;
    }

    public function getUserWorkingTimeHours($userId, $year, $month)
    {
        $hours = 0;
        $user = User::findOne($userId);
        $periods = EventUserWorkingTime::getMonth($userId, $this->id, $month, $year);
        $periods = $this->sumOverlapingPeriods($periods);
        $duration = $this->getUserWorkingTimeInPeriods($periods);

        $hours = +floor($duration / 3600);

        return $hours;
    }

    private function getUserWorkingTimeInPeriods($periods) {
        $time = 0;
        foreach ($periods as $period) {
            $start = new DateTime($period->start_time);
            $end = new DateTime($period->end_time);
            $diff = $start->diff($end);
            $time += $this->dateIntervalToSeconds($diff);
        }
        return $time;
    }

    private function dateIntervalToSeconds(\DateInterval $interval) {
        return ($interval->y * 365 * 24 * 60 * 60) +
            ($interval->m * 30 * 24 * 60 * 60) +
            ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }

    private function sumOverlapingPeriods($periods) {
        /**
         * @var \common\models\EventUserWorkingTime $period1
         * @var \common\models\EventUserWorkingTime $period2
         */
        foreach ($periods as $key1 => $period1) {
            foreach ($periods as $key2 => $period2) {
                if ($key1 == $key2) {
                    continue;
                }
                if (self::datesAreOverlaping(new DateTime($period1->start_time), new DateTime($period1->end_time), new DateTime($period2->start_time), new DateTime($period2->end_time))) {
                    $newPeriod = $this->mergePeriods($period1, $period2);
                    $periods[$key1] = $newPeriod;
                    for ($i = $key2; $i < count($periods)-1; $i++) {
                        $periods[$i] = $periods[$i+1];
                    }
                    unset($periods[count($periods)-1]);
                    return $this->sumOverlapingPeriods($periods);
                }
            }
        }
        return $periods;
    }

    private function mergePeriods(EventUserWorkingTime $period1, EventUserWorkingTime $period2) {
        $period1->start_time = min(new DateTime($period1->start_time), new DateTime($period2->start_time))->format("Y-m-d H:i:s");
        $period1->end_time = max(new DateTime($period1->end_time), new DateTime($period2->end_time))->format("Y-m-d H:i:s");
        return $period1;
    }

    public static function datesAreOverlaping(DateTime $start_one, DateTime $end_one, DateTime$start_two, DateTime $end_two) {
        if (($start_one < $end_two && $end_one > $start_two) || ($start_two < $end_one && $end_two > $start_one) ) {
            return true;
        }
        return false;
    }

    public function getRolesAddons($userId, $asArray = false, $year, $month) {
        $value = 0;
        $addonArr = [];
            if ($year)
            {
                $firstDayUTS = mktime (0, 0, 0, $month, 1, $year);
                $firstDay = date("Y-m-d H:i:s", $firstDayUTS);
                $lastDay = date("Y-m-t", strtotime($firstDay));
                $lastDay .=" 23:59:59";
                if (($this->getTimeEnd()>=$firstDay)&&($this->getTimeEnd()<=$lastDay))
                {

                }else{
                    if ($asArray) {
                        return $addonArr;
                    }
                    return $value;
                }
            }
        $query = new Query();
        $query->select('*')
            ->from(['t1'=>'event_user_working_time'])
            ->innerJoin(['t2'=>'user_addon_rate'], 't1.user_id = t2.user_id')
            ->innerJoin(['t3'=>'addon_rate'], 't3.id = t2.rate_id')
            ->innerJoin(['t4'=>'event_working_time_role'], 't4.working_time_id=t1.id')
            ->where([
                't1.user_id'=>$userId,
                't1.event_id'=>$this->id,
                't3.level'=>$this->level,
            ])
            ->andWhere('t2.role_id = t4.role_id')->orderBy(['t1.start_time'=>SORT_ASC]);
        $result = $query->all();

        // okresy przepracowane pełniąc rolę, której id będzie indexem
        $rolePeriods = [];
        // suma czasu przepracowanego pełniąc rolę
        $rolePeriodsSum = [];
        // stawka za rolę
        $roleAddon = [];
        // typ stawki: za 8h/12h/dzień itp
        $rolePeriodType = [];

        // dzielimy przepracowane okresy na role
        foreach ($result as $r) {
            $roleAddon[$r['role_id']] = $r['amount'];
            $rolePeriodType[$r['role_id']] = $r['period'];
            $rolePeriods[$r['role_id']][] = ['start' => $r['start_time'], 'end' => $r['end_time']];

            $start = new DateTime($r['start_time']);
            $end = new DateTime($r['end_time']);
            $time = $this->dateIntervalToSeconds($start->diff($end));

            if (isset($rolePeriodsSum[$r['role_id']])) {
                $rolePeriodsSum[$r['role_id']] += $time;
            }
            else {
                $rolePeriodsSum[$r['role_id']] = $time;
            }
        }

	    $addonPeriodEvent = [];
        // dla każdej roli w zaleznosci od typu bonusu naliczamy dodatki
        foreach ($rolePeriods as $role_id => $periods) {
            // jeżeli jeden dodatek za cały event, to dodaj
            if ($rolePeriodType[$role_id] == AddonRate::PERIOD_EVENT && !isset($addonPeriodEvent[$role_id])) {
	            $addonPeriodEvent[$role_id] = true;
                $value += $roleAddon[$role_id];
                $addonArr[] = [
                    'role_id' => $role_id,
                    'name' => UserEventRole::findOne($role_id)->name,
                    'amount' => $roleAddon[$role_id],
                ];
            }
            elseif ($rolePeriodType[$role_id] == AddonRate::PERIOD_DAY) {
                $days = 0;
                $paid = [];
                //minimum 6h drugiego dnia
                foreach ($rolePeriods[$role_id] as $period) {
                    $start = new DateTime($period['start']);
                    $end = new DateTime($period['end']);
                    if (!isset($paid[$start->format('Y-m-d')])) {

	                    $start2 = clone $start;
                        $start2->modify( '-1 day' );
                        if (!isset($paid[$start2->format('Y-m-d')]))
                        {
                            $days++;
                        }else{
                            $tmp = new DateTime($start->format('Y-m-d') ." 01:01");
                            if ($start->format('Y-m-d H:i:s')>$tmp->format('Y-m-d H:i:s'))
                            {
                                $days++;
                            }
                        }
                    }
                    $paid[$start->format('Y-m-d')] = true;
                    if ($start->format("Y-m-d") != $end->format('Y-m-d')) {
                        $tmp = new DateTime($end->format('Y-m-d') ." 00:00");
                        if ($this->dateIntervalToSeconds($tmp->diff($tmp)) >= 6 * 60 * 60) {
                            if (!$paid[$end->format("Y-m-d")]) {
                                $days++;
                            }
                        }
                    }
                }
                $value += $days * $roleAddon[$role_id];
                $addonArr[] = [
                    'role_id' => $role_id,
                    'name' => UserEventRole::findOne($role_id)->name,
                    'amount' => $days * $roleAddon[$role_id]
                ];
            }
            else {
                $workedHours = $rolePeriodsSum[$role_id] / 3600;
                if ($workedHours >= $rolePeriodType[$role_id]) {
                    $time4hPeriods = floor($workedHours / 4);
                    $salary4hPeriods = floor($roleAddon[$role_id] / $rolePeriodType[$role_id] * 4);
                    $value += $time4hPeriods * $salary4hPeriods;
                    $addonArr[] = [
                        'role_id' => $role_id,
                        'name' => UserEventRole::findOne($role_id)->name,
                        'amount' => $time4hPeriods * $salary4hPeriods
                    ];
                }
            }
        }
        if ($asArray) {
            return $addonArr;
        }
        return $value;
    }

    public function getWorkingTimeSummaryForEventTab($user_id, $formatted) {
    	$user = Yii::$app->user;
    	if ( $user->can('eventsEventEditEyeWorkingHours'.BasePermission::SUFFIX[BasePermission::ALL]) ) {
			return $this->getWorkingTimeSummary(null, $formatted);
	    }
	    return $this->getWorkingTimeSummary($user_id, $formatted);
    }

    public function getWorkingTimeSummary($userId, $formatted=false, $year=null, $month=null)
    {
        //$cache = Yii::$app->cache;
       //$cacheKey = md5(__METHOD__.$userId.'_'.((int)$formatted).'_'.$this->id);
       // $summary = $cache->get($cacheKey);
       //$summary = false;
        //if ($summary === false) {
            $user = User::findOne($userId);
            if ($year)
            {
                $firstDayUTS = mktime (0, 0, 0, $month, 1, $year);
                $firstDay = date("Y-m-d H:i:s", $firstDayUTS);
                $lastDay = date("Y-m-t", strtotime($firstDay));
                $lastDay .=" 23:59:59";
            }
            $addons = $this->getUserAddons($userId);
            $addonsValue = 0;
            foreach ($addons as $addon) {
                if ($year)
                {
                    if (($addon->start_time>=$firstDay)&&($addon->start_time<=$lastDay))
                    {
                        $addonsValue += (float)$addon->amount;
                    }
                }else{
                    $addonsValue += (float)$addon->amount;
                }
                
            }

            $allowances = $this->getUserAllowances($userId);
            $allowancesValue = 0;
            foreach ($allowances as $allowance) {
                if ($year)
                {
                    if (($allowance->start_time>=$firstDay)&&($allowance->start_time<=$lastDay))
                    {
                        $allowancesValue += (float)$allowance->amount;
                    }
                }else{
                    $allowancesValue += (float)$allowance->amount;
                }
                
            }

            if ($userId != null) {
            	$summary['provision'] = 0;
                $summary['provision_non'] = 0;
                $summary['rate'] = (float)$user->rate_amount;
            	$summary['salary'] = $this->getUserWorkingTimeSalary($userId, $year, $month);
                if ($year)
                {
                    if (($this->getTimeEnd()>=$firstDay)&&($this->getTimeEnd()<=$lastDay))
                    {
                        $summary['roleAddons'] = $this->getRolesAddons($userId, false, $year, $month);
                    }else{
                        $summary['roleAddons'] = 0;
                    }
                    
                }else{
                    $summary['roleAddons'] = $this->getRolesAddons($userId, false, $year, $month);
                }
            	
                $summary['hours'] = $this->getUserWorkingTimeHours($userId, $year, $month);
                if ((isset($this->eventStatut))&&($this->eventStatut->count_provision)){
                
                if ((!$year)||(($this->getTimeEnd()>=$firstDay)&&($this->getTimeEnd()<=$lastDay)))
                {
                    if ($userId==$this->manager_id)
                    {
                        //$summary['salary'] +=$this->getTotalProvision();
                        //$summary['provision'] = $this->getTotalProvision();
                    }else{
                        if ($user->hasSectionProvisions())
                        {
                            //$summary['salary'] +=$this->getSectionProvision($user);
                            //$summary['provision'] = $this->getSectionProvision($user);
                        }

                    }
                    $provs = $this->getUserGProvision($userId);
                    foreach ($provs as $p)
                    {
                        //$summary['salary'] +=$p['value'];
                        $summary['provision'] +=$p['value'];
                    }
                    }
                    
                }else{
                    if ((!$year)||(($this->getTimeEnd()>=$firstDay)&&($this->getTimeEnd()<=$lastDay)))
                {

                    $provs = $this->getUserGProvision($userId);
                    foreach ($provs as $p)
                    {
                        //$summary['salary'] +=$p['value'];
                        $summary['provision_non'] +=$p['value'];
                    }
                    }
                }

            }
            else {
            	$users = [];
            	foreach ($this->eventUserWorkingTimes as $time) {
            		if (!in_array($time->user_id, $users)) {
            			$users[] = $time->user_id;
		            }
	            }
	            foreach ($this->eventUserAllowances as $allowances) {
		            if (!in_array($allowances->user_id, $users)) {
			            $users[] = $allowances->user_id;
		            }
	            }
	            foreach ($this->eventUserAddons as $addon) {
		            if (!in_array($addon->user_id, $users)) {
			            $users[] = $addon->user_id;
		            }
	            }

	            $summary['salary'] = 0;
                $summary['provision'] = 0;
                $summary['provision_non'] = 0;
	            $summary['roleAddons'] = 0;
                $summary['brutto'] = 0;

	            foreach ($users as $user_id) {
                    $salary = $this->getUserWorkingTimeSalary($user_id, false, $year, $month);
                    $roleAddons = $this->getRolesAddons($userId, false, $year, $month);;
                    $user = User::findOne($user_id);
		            $summary['salary'] += $salary;
		            $summary['roleAddons'] += $roleAddons;
                    $addons2 = $this->getUserAddons($user_id);
                    $s = 0;
                    foreach ($addons2 as $addon) {
                        $s += (float)$addon->amount;
                    }
                    foreach ($allowances as $allowance) {
                        $s += (float)$allowance->amount;
                    }
                    $allowances2 = $this->getUserAllowances($user_id);
                    $summary['brutto'] += ($salary+$roleAddons+$s)*100/(100-$user->tax_rate);
	            }
            }

            // addons == koszty
            $summary['addons'] = $addonsValue;
	        $summary['allowances'] = $allowancesValue;
            $summary['sum'] = $summary['addons'] + $summary['salary'] + $summary['allowances'] + $summary['roleAddons']+$summary['provision'];
            if ($userId)
            {
                $user = User::findOne($userId);
                $summary['brutto'] = $summary['sum']*100/(100-$user->tax_rate);
            }
            if ($formatted == true) {
                $formatter = Yii::$app->formatter;
                foreach ($summary as $k=>$v) {
                    $summary[$k] = $formatter->asCurrency($v);
                }
            }

            //$cache->set($cacheKey, $summary, 10);
        //}

        return $summary;
    }

    public function getDepartmentsWorkingTimeSummary()
    {
        $summary = [];
        $users = $this->getUsers()->indexBy('id')->all();

        foreach ($users as $user)
        {
            /* @var $user User */
            $times = $user->getEventUserWorkingTimes()
                ->where([
                    'event_id'=>$this->id,
                ])
                ->innerJoinWith('department')
                ->all();
            foreach ($times as $time)
            {
                /* @var $time EventUserWorkingTime */


                $value = $time->getSalary();
                $index = $time->department->name;

                $val = ArrayHelper::getValue($summary, $index, 0);
                $summary[$index] = $val + $value;
            }

        }
        $summary[Yii::t('app', 'Suma')] = array_sum($summary);

        $summary = static::sortSummary($summary);
        return $summary;
    }

    public function getWorkingTimeSummaryAll()
    {
        $workingTimeData = [];
        foreach ($this->users as $key => $user)
        {

            $workingTimeData[$key] = $this->getWorkingTimeSummary($user->id);
            $workingTimeData[$key]['user'] = $user->getDisplayLabel();
            $workingTimeData[$key]['userId'] = $user->id;
//            $workingTimeData[$key]['departments'] = ArrayHelper::map($user->departments, 'id', 'name');

            $departmets = Department::find()->innerJoinWith(['eventUserWorkingTimes'])->where([
                'user_id'=>$user->id,
                'event_id'=>$this->id,
            ])->all();
            $workingTimeData[$key]['departments'] = ArrayHelper::map($departmets, 'id', 'name');
        }
        return $workingTimeData;
    }

    public function getWorkingTimeSummaryAllSums()
    {
        $sums = [];
        $data = $this->getWorkingTimeSummaryAll();

        $attributes = [
            'user',
            'userId',
            'departments',
            'rate',
            'roles',
            'addons',
            'allowances',
            'roleAddons',
            'salary',
            'sum',
        ];

        $skip = ['user', 'userId', 'departments'];
        foreach ($attributes as $col)
        {
            if(in_array($col, $skip) == true)
            {
                $sums[$col] = '';
            }
            else
            {
                $sums[$col] = array_sum(ArrayHelper::getColumn($data, $col));
            }
        }
        return $sums;
    }

    public function getWorkingTimeSummaryRealAll()
    {
        $workingTimeData = [];
        $users = $this->getUsers()->indexBy('id')->all();
        $userIds = array_keys($users);

        $workingTimes = $this->getEventUserWorkingTimes()->where(['user_id'=>$userIds])->all();

        foreach ($workingTimes as $model)
        {   /* @var $model EventUserWorkingTime; */
            /* @var $user User */

            $user = $users[$model->user_id];

            $index = $model->department->name;
            $value = ArrayHelper::getValue($workingTimeData, $index, 0);
            $salary = (floor($model->duration / ($user->rate_type * 3600))) * $user->rate_amount;
            $workingTimeData[$index] = $value + $salary;

        }

        return $workingTimeData;
    }

    public function getWorkingTimeRealSummary($userId, $formatted=false)
    {
        $user = User::findOne($userId);
        $addon = $this->getUserAddons($userId);
        $roleSalary = $this->getEventUsers()->innerJoinWith('userEventRole')->where(['user_id'=>$userId])->select(['salary'])->scalar();
        $summary = [
            'rate' =>(int)$user->rate_amount,
            'roles'=>$roleSalary,
            'addons' =>(float)$addon->expense,

        ];

        $summary['sum'] = $summary['addons'] + $summary['roles'] + $this->getUserWorkingTimeSalaryDepartment($userId);
        if ($formatted == true)
        {
            $formatter = Yii::$app->formatter;
            foreach ($summary as $k=>$v)
            {
                $summary[$k] = $formatter->asCurrency($v);
            }
        }
        return $summary;
    }

    public function getUserWorkingTimeSalaryDepartment($userId)
    {
        $salary = 0;
        $user = User::findOne($userId);
        $rate = $user->rate_amount;
        $type = $user->rate_type;

        if ($type > 0)
        {
            $duration = $user->getEventUserWorkingTimes()->where(['event_id'=>$this->id])->sum('duration');
            $salary = (floor($duration / ($type * 3600))) * $rate;
        }

        return $salary;
    }

    /**
     * Koszty dodatkowe poniesione przez użytkownika
     * @param integer $userId
     * @return array|EventUserAddon[]
     */
    public function getUserAddons($userId=null)
    {
        $models = EventUserAddon::find()->andFilterWhere([
            'user_id'=>$userId,
            'event_id' => $this->id,
        ])->all();

        return $models;
    }


    /**
     * Diety użytkownika
     * @param integer $userId
     * @return array|EventUserAllowance[]
     */
    public function getUserAllowances($userId=null)
    {
        $models = EventUserAllowance::find()->andFilterWhere([
            'user_id'=>$userId,
            'event_id' => $this->id,
        ])->all();

        return $models;
    }
    public function getEventValueAll()
    {
        if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            $offers = $this->getAcceptedAgencyOffers();
            if (isset($offers['error']) && $offers['error']) {
                return  [
                    Yii::t('app', 'Suma')=>$sum,
                    ];
            }
            foreach ($offers as $offer)
            {
                if ($offer->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                    $sum +=$offer->getNettoValue()*$offer->exchange_rate;
                }else{
                    $sum +=$offer->getNettoValue();
                }
                

            }
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
            $return = [];
            $return[Yii::t('app', 'Suma')] = 0;
            $return[Yii::t('app', 'Transport')] = 0;
            $return[Yii::t('app', 'Obsługa')] = 0;
            foreach (\common\models\EventExpense::getSectionList() as $s)
            {
                $return[$s] = 0;
            }
            $offers = $this->getOffersAccepted();
            foreach ($offers as $offer)
            {
                $values = $offer->getOfferValues();
                foreach ($values as $key=>$val)
                {
                    if (!isset($return[$key]))
                    {
                        $return[$key] = 0;
                    }
                    if ($offer->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                        $return[$key] += $val*$offer->exchange_rate;
                    }else{
                        $return[$key] += $val;
                    }
                    
                }

            }

            return $return;           
        }


        return $profit;
    }

    public function getEventPredictedCost()
    {
        

        if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
            $return = [];
            $return[Yii::t('app', 'Suma')] = 0;
            $offers = $this->getOffersAccepted();
            foreach ($offers as $offer)
            {
                $values = $offer->getOfferCosts();
                foreach ($values as $key=>$val)
                {
                    if (!isset($return[$key]))
                    {
                        $return[$key] = 0;
                    }
                    $return[$key] += $val;
                }

            }
            return $return;           
        }


        return $profit;
    }

    public function getEventPredictedProvisions()
    {
        

        if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
            $return = [];
            $return[Yii::t('app', 'Suma')] = 0;
            $offers = $this->getOffersAccepted();
            foreach ($offers as $offer)
            {
                $values = $offer->getGProvisions();
                foreach ($values as $key=>$val)
                {
                    $return[Yii::t('app', 'Suma')] += $val['value'];
                }

            }

            return $return;           
        }


        return $profit;
    }

    public function getEventValue()
    {
        if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            $offers = $this->getAcceptedAgencyOffers();
            if (isset($offers['error']) && $offers['error']) {
                return  [
                    Yii::t('app', 'Suma')=>$sum,
                    ];
            }
            foreach ($offers as $offer)
            {
                $sum +=$offer->getNettoValue();

            }
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
             $profit = [
                Yii::t('app', 'Suma')=>0,
            ];
            $offersData = [];
            $offers = $this->getOffersAccepted();
            foreach ($offers as $offer)
            {
                $offersData[] = $offer->getSummary();
                foreach ($offer->getSummary() as $key=>$val)
                {
                    $profit = ArrayHelper::setKey($profit, $key);
                    $profit[$key] += $val;
                }

            }

            $sum = $profit[Yii::t('app', 'Suma')];
            unset($profit[Yii::t('app', 'Suma')]);
            ksort($profit, SORT_NATURAL);
            $profit[Yii::t('app', 'Suma')] = $sum;           
        }


        return $profit;
    }

    public function getProfit()
    {
    if (Yii::$app->session->get('company')!=1){
            $sum = 0;
            $offers = $this->getAcceptedAgencyOffers();
            if (isset($offers['error']) && $offers['error']) {

            }else{
                foreach ($offers as $offer)
                {
                    $sum +=$offer->getProfitValue();

                }                
            }
            $profit = [
                Yii::t('app', 'Suma')=>$sum,
            ];
        }else{
            $profit = $this->getEventValueAll();
        }



        


//          Koszty dodatkowe
//          Zysk nie wliczają się w Zysk!
//          Koszty zmniejszają zysk.
        $expenses = $this->getEventExpenses()
            ->where([
                'type'=>EventExpense::TYPE_SINGLE,
            ])
            ->all();
        foreach ($expenses as $expense)
        {
            /* @var $expense EventExpense */
            $key = $expense->section;
            $profit = ArrayHelper::setKey($profit, $key);
            $profit[$key] -= $expense->amount;
            $profit[Yii::t('app', 'Suma')] -= $expense->amount;
        }

        $workingTimeData = $this->getWorkingTimeSummaryAll();
        foreach ($workingTimeData as $data)
        {
            $value = ArrayHelper::getValue($profit, Yii::t('app', 'Obsługa'), 0);
            $value -= $data['sum'];
            $profit[Yii::t('app', 'Obsługa')] = $value;
            $profit[Yii::t('app', 'Suma')] -= $data['sum'];

        }

        $profit = static::sortSummary($profit);

        return $profit;
    }

    public function getProvisionValue()
    {
        $value = 0;
        if ($this->provision_type == static::PROVISION_TYPE_OFFER)
        {
            $data = $this->getOffersSummary();
        }
        else
        {
            $data = $this->getProfit();
        }
        $sum = $data[Yii::t('app', 'Suma')];
        $value = $sum * $this->provision;
        return $value;
    }

    public function getMessage()
    {
        return new EventMessage(['event_id'=>$this->id]);
    }

    /**
     * @return ActiveDataProvider
     */
    public function getEventExpensesDataProvider()
    {
        $query = $this->getEventExpenses()
            ->where([
                'group_id'=>null,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>false,
            'sort'=>false,
        ]);

        return $dataProvider;
    }

    public function getAddons($userId=null)
    {
        $query = $this->getEventUserAddons();
        if ($userId != null)
        {
            $query->filterWhere([
                'user_id'=>$userId,
            ]);
        }


        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>false,
            'sort'=>false,
        ]);

        return $dataProvider;
    }

    public function getAllowances($userId=null)
    {
        $query = $this->getEventUserAllowances();
        if ($userId != null)
        {
           $query->filterWhere([
               'user_id'=>$userId,
           ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>false,
            'sort'=>false,
        ]);

        return $dataProvider;
    }
    public static function assignGearItem($id, $itemId, $quantity=null, $params = [])
    {
        $model = EventGearItem::findOne(['event_id'=>$id, 'gear_item_id'=>$itemId]);
        if ($model == null)
        {
            $model = new EventGearItem();
        }
        $model->event_id = $id;
        $model->gear_item_id = $itemId;
        $model->quantity = $quantity;
        $model->attributes = $params;

        $available = false;

        $start = ArrayHelper::getValue($params, 'start_time', false);
        $end = ArrayHelper::getValue($params, 'end_time', false);
        $owner = static::findOne($id);
        if ($start != false && $end != false)
        {
            $available = $model->gearItem->isAvailableInRange($start, $end, $owner);
        }
        else
        {

            $available = $model->gearItem->isAvailable($owner);
        }

        if ( $available == true && $model->gearItem->status == GearItem::STATUS_ACTIVE)
        {
            return $model->save();
        }
        else
        {
            return false;
        }

    }



    public static function assignGear($id, $itemId, $quantity=null, $params = [])
    {
        $model = EventGear::findOne(['event_id'=>$id, 'gear_id'=>$itemId]);
        $old_quantity = 0;
        if (!$model)
        {
            $model = new EventGear();
            $old_quantity = 0;
        }else{
            $old_quantity= $model->quantity;
        }
        $model->event_id = $id;
        $model->gear_id = $itemId;
        $model->quantity = $quantity;
        $model->attributes = $params;
        $available = false;
        $model->clearConflicts();
        $start = ArrayHelper::getValue($params, 'start_time', false);
        $end = ArrayHelper::getValue($params, 'end_time', false);
        if ($model->gear->type==2)
        {
            return $model->save();
        }
        if ($model->gear->type==3)
        {
            $available = $model->gear->quantity+$old_quantity;
            if ( $available >= $quantity)
            {
                $model->gear->quantity = $model->gear->quantity+$old_quantity-$quantity;
                $model->gear->save();
                return $model->save();
            }else{
                return false;
            }
        }
        if ($start != false && $end != false)
        {
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        else
        {
            $start = $model->event->getTimeStart();
            $end = $model->event->getTimeEnd();
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        $available = $available-$model->gear->getInService();
        if ( $available >= $quantity)
        {
            return $model->save();
        }
        else
        {
            //dodajemy tyle ile jest
            return false;
            /*
            if (Yii::$app->user->can('eventEventEditEyeGearConflict'))
            {
                if ($available>0)
                {
                    $model->quantity = $available;
                    $model->save();
                }
                $quantity = $quantity-$available;
                $model2 = EventConflict::findOne(['event_id'=>$id, 'gear_id'=>$itemId, 'resolved'=>0]);
                if (!$model2)
                {
                    $model2 = new EventConflict();
                    $model2->event_id = $id;
                    $model2->gear_id = $itemId;
                }
                $model2->quantity = $quantity;
                $model2->added = $available;
                $model2->save();
                return 'conflict';              
            }else{
                return false;
            }
            */
        }

    }

    public static function assignGearCon($id, $itemId, $quantity=null, $params = [])
    {
        $model = EventGear::findOne(['event_id'=>$id, 'gear_id'=>$itemId]);
        $old_quantity = 0;
        if (!$model)
        {
            $model = new EventGear();
            $old_quantity = 0;
        }else{
            $old_quantity= $model->quantity;
        }
        $quantity +=$old_quantity;
        $model->event_id = $id;
        $model->gear_id = $itemId;
        $model->quantity = $quantity;
        $model->attributes = $params;
        $available = false;
        $model->clearConflicts();
        $start = ArrayHelper::getValue($params, 'start_time', false);
        $end = ArrayHelper::getValue($params, 'end_time', false);
        if ($model->gear->type==2)
        {
            return ['result'=>$model->save(), 'conflict'=>null];
        }
        if ($model->gear->type==3)
        {
            $available = $model->gear->quantity+$old_quantity;
            if ( $available >= $quantity)
            {
                $model->gear->quantity = $model->gear->quantity+$old_quantity-$quantity;
                $model->gear->save();
                return ['result'=>$model->save(), 'conflict'=>null];
            }else{
                return ['result'=>false, 'conflict'=>null];
            }
        }
        if ($start != false && $end != false)
        {
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        else
        {
            $start = $model->event->getTimeStart();
            $end = $model->event->getTimeEnd();
            $available = $model->gear->getAvailabe($start, $end)+$old_quantity;
        }
        $available = $available-$model->gear->getInService();
        if ( $available >= $quantity)
        {
            return ['result'=>$model->save(), 'conflict'=>null];
        }
        else
        {
            //dodajemy tyle ile jest
            if (Yii::$app->user->can('eventEventEditEyeGearConflict'))
            {
                if ($available>0)
                {
                    $model->quantity = $available;
                    $model->save();
                }
                $quantity = $quantity-$available;
                $model2 = EventConflict::findOne(['event_id'=>$id, 'gear_id'=>$itemId, 'resolved'=>0]);
                if (!$model2)
                {
                    $model2 = new EventConflict();
                    $model2->event_id = $id;
                    $model2->gear_id = $itemId;
                }
                $model2->quantity = $quantity;
                $model2->added = $available;
                $model2->save();
                return ['result'=>true, 'conflict'=>$model2];             
            }else{
                return ['result'=>false, 'conflict'=>null];
            }
        }

    }

    public static function removeGearItem($id, $itemId)
    {
        $count = 0;
        $models =  EventGearItem::findAll(['event_id'=>$id, 'gear_item_id'=>$itemId]);
        foreach ($models as $model)
        {
            $count += (int)$model->delete();
        }
        return $count;
    }

    public function getUserList(){
        $return = [];
        foreach ($this->eventUsers as $eu)
        {
            $return[$eu->user_id] = $eu->user->displayLabel;
        }
        return $return;
    }

    public static function removeGear($id, $itemId)
    {
        $model =  EventGear::find()->where(['event_id'=>$id, 'gear_id'=>$itemId])->one();
        if ($model->gear->type==3)
        {
                $model->gear->quantity = $model->gear->quantity+$modelquantity;
                $model->gear->save();            
        }
        $model->clearConflicts();
        foreach (GearItem::find()->where(['gear_id' => $itemId])->all() as $gearItem) {
            $gears = EventGearItem::find()->where(['event_id' => $id])->andWhere(['gear_item_id' => $gearItem->id])->all();
            foreach ($gears as $gear) {
                        $gear->delete();
            }
        }
        $eventlog = new EventLog;
                        $gi = Gear::find()->where(['id'=>$itemId])->one();
                        $eventlog->event_id = $id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content = Yii::t('app', "Z eventu usunięto sprzęt ").$gi->name;
                        $eventlog->save();
        $count = $model->quantity;
        $model->delete();
        return $count;
    }

    public static function getAssignedQuantities($id)
    {
        $data = EventGearItem::find()
            ->where(['event_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'gear_item_id', 'quantity');
        return $list;
    }

    public function getConflictCount()
    {
        $data = EventConflict::find()
            ->where(['event_id'=>$this->id, 'resolved'=>0])
            ->count();
        if ($data)
            return ' <span class="badge badge-danger pull-right">'.$data.'</span>';
        else
            return "";
    }

    public static function getAssignedGearQuantities($id, $packlist)
    {
        $data = PacklistGear::find()
            ->where(['packlist_id'=>$packlist])
            ->all();
        $list = ArrayHelper::map($data, 'gear_id', 'quantity');
        return $list;
    }

    public function getOriginAddress()
    {
        $address = Yii::$app->settings->get('main.warehouseCity')." ".Yii::$app->settings->get('main.warehouseAddress');

        if ($this->route_start != null)
        {
            $address = $this->route_start;
        }

        return $address;
    }

    public function getDestinationAddress()
    {
        $address = Yii::t('app', 'Warszawa');
        if ($this->route_end != null)
        {
            $address = $this->route_end;
        }
        else if ($this->location !== null && $this->location->city != null)
        {
            $address = $this->location->city.', '.$this->location->address;
        }else{
            $address = $this->address;
        }

        return $address;
    }

    public static function getAssignedOuterQuantities($id)
    {
        $data = EventOuterGear::find()
            ->where(['event_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'outer_gear_id', 'quantity');
        return $list;
    }

    public static function getAssignedOuterModelQuantities($id)
    {
        $data = EventOuterGearModel::find()
            ->where(['event_id'=>$id])
            ->all();
        $list = ArrayHelper::map($data, 'id', 'quantity');
        return $list;
    }

    public function getOuterGear()
    {
        return $this->hasMany(\common\models\OuterGear::className(), ['id' => 'outer_gear_id'])->viaTable('event_outer_gear', ['event_id' => 'id']);
    }

    public static function assignOuterGear($id, $itemId, $quantity=null, $discount=null)
    {
            $model = EventOuterGear::findOne(['event_id'=>$id, 'outer_gear_id'=>$itemId]);
            if (($model===null)&&($quantity>0))
            {
                $model = new EventOuterGear();
            }
            if ($quantity>0)
            {
                $model->event_id = $id;
                $model->outer_gear_id = $itemId;
                $model->quantity = $quantity;
                $model->discount = $discount;
                $model->save();                 
            }else{
                if ($model)
                {
                    $model->delete();
                }
            }
            $expense = EventExpense::findOne(['event_id'=>$id, 'gear_id'=>$itemId]);
            if (($expense===null)&&($quantity>0))
            {
                $expense = new EventExpense();
            } 
            if ($quantity>0)
            {
                $expense->event_id = $id;
                $expense->gear_id = $itemId;
                $expense->name = $model->outerGear->name." [x".$quantity."]";
                $expense->amount = $model->outerGear->price*$quantity;
                $expense->amount_customer = $model->outerGear->selling_price*$quantity;
                $expense->profit = $expense->amount_customer-$expense->amount;
                $expense->customer_id = $model->outerGear->company_id;
                $gearItem = OuterGear::findOne($itemId);
                $gear = $gearItem->outerGearModel;
                $expense->sections = [$gear->category->getMainCategory()->name];
                $expense->save();                 
            }else{
                if ($expense)
                {
                    $expense->delete();
                }
            }

            return false;
          

    }

    public static function assignOuterGear2($id, $item)
    {
            $model = EventOuterGear::findOne(['event_id'=>$id, 'outer_gear_id'=>$item['outer_gear_id']]);
            $gearItem = OuterGear::findOne($item['outer_gear_id']);
            $eventOuterModel = EventOuterGearModel::find()->where(['outer_gear_model_id'=>$gearItem->outer_gear_model_id])->andWhere(['event_id'=>$id])->one();
            if (!$eventOuterModel)
            {
                $eventOuterModel = new EventOuterGearModel();
                $eventOuterModel->event_id = $id;
                $eventOuterModel->outer_gear_model_id = $gearItem->outer_gear_model_id;
                $eventOuterModel->quantity = $item['quantity'];
                $eventOuterModel->save();
            }
            if ((!$item['price'])||($item['price']==""))
                $item['price'] = 0;
            if (($model===null)&&($item['quantity']>0))
            {
                $model = new EventOuterGear();
            }
            if ($item['quantity']>0)
            {
                $model->event_id = $id;
                $model->outer_gear_id = $item['outer_gear_id'];
                $model->quantity = $item['quantity'];
                $model->price = $item['price'];
                $model->discount = 0;
                $model->user_id = $item['user_id'];
                $model->description = $item['description'];
                $model->return_time = $item['return_time'];
                $model->reception_time = $item['reception_time'];
                if ($eventOuterModel)
                    $model->prod = $eventOuterModel->prod;
                $model->save();                 
            }else{
                if ($model)
                {
                    $model->delete();
                }
            }
            $expense = EventExpense::findOne(['event_id'=>$id, 'gear_id'=>$item['outer_gear_id']]);
            if (($expense===null)&&($item['quantity']>0))
            {
                $expense = new EventExpense();
            } 
            if ($item['quantity']>0)
            {
                $expense->event_id = $id;
                $expense->gear_id = $item['outer_gear_id'];
                $expense->name = $model->outerGear->name." [x".$item['quantity']."]";
                $expense->amount = $item['price']*$item['quantity'];
                $expense->amount_customer = $model->outerGear->selling_price*$item['quantity'];
                $expense->profit = $expense->amount_customer-$expense->amount;
                $expense->customer_id = $model->outerGear->company_id;
                
                $gear = $gearItem->outerGearModel;
                $expense->sections = [$gear->category->getMainCategory()->name];
                $expense->save();                 
            }else{
                if ($expense)
                {
                    $expense->delete();
                }
            }

            
            if ($eventOuterModel)
            {
                $total = 0;
                $outerIds = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$gearItem->outer_gear_model_id])->asArray()->all(), 'id', 'id');
                $eogs = EventOuterGear::find()->where(['event_id'=>$id])->andWhere(['outer_gear_id'=>$outerIds])->all();
                foreach ($eogs as $eog)
                {
                    $total +=$eog->quantity;
                }
                if ($total>=$eventOuterModel->quantity)
                {
                    $eventOuterModel->resolved = 1;
                }else{
                    $eventOuterModel->resolved = 0;
                }
                $eventOuterModel->save();
            }

            return false;
          

    }
    public static function assignOuterGearModel($id, $itemId, $quantity=null, $discount=null)
    {
        $model = EventOuterGearModel::findOne(['event_id'=>$id, 'outer_gear_model_id'=>$itemId]);
        if ($model===null)
        {
            $model = new EventOuterGearModel();
        }
        $model->event_id = $id;
        $model->outer_gear_model_id = $itemId;
        $model->quantity = $quantity;
        return $model->save();
    }

    public static function removeOuterGear($id, $itemId)
    {
        EventExpense::deleteAll(['event_id'=>$id, 'gear_id'=>$itemId]);
        return EventOuterGear::deleteAll(['event_id'=>$id, 'outer_gear_id'=>$itemId]);
    }

    public static function removeOuterGearModel($id, $itemId)
    {
        return EventOuterGearModel::deleteAll(['event_id'=>$id, 'outer_gear_model_id'=>$itemId]);
    }

    public static function getList()
    {
        $model = Event::find()->all();

        $list = ArrayHelper::map($model, 'id', 'displayLabel');
        return $list;
    }

    public function getDisplayLabel()
    {
        $label = $this->name.' ['.$this->code.']';
        return $label;
    }

    public function getClassType()
    {
        return 'event';
    }
    public static function getClassTypeLabel()
    {
        return Yii::t('app', 'Wydarzenie');
    }

    public function getManagerDisplayLabel()
    {
        $label = '';
        if($this->manager !== null)
        {
            $label = $this->manager->getDisplayLabel();
        }
        return $label;
    }

    public function getRolesLabel($roleId,  $separator = ', ')
    {

        $label = '-';
        $models = User::find()
            ->innerJoinWith(['eventUsers'=>function($q){
                $q->innerJoinWith('eventUserRoles');
            }])
            ->where([
                'event_user.event_id' => $this->id,
                'user_event_role_id' => $roleId,
            ])
            ->all();
        $list = ArrayHelper::map($models, 'id', 'displayLabel');
        if (empty($list)==false)
        {
            $label = implode($separator, $list);
        }

        return $label;
    }

    public function getVehiclesLabel($separator = ', ')
    {
        $label = '-';
        $models = Vehicle::find()
            ->innerJoinWith('eventVehicles')
            ->where([
                'event_vehicle.event_id' => $this->id,
            ])
            ->all();
        $list = ArrayHelper::map($models, 'id', 'displayLabel');
        if (empty($list)==false)
        {
            $label = implode($separator, $list);
        }

        return $label;
    }

    public function getTooltipContent()
    {
        $info = "";
        $info = "<h4>".$this->name."</h4>";
        if (Yii::$app->settings->get('blackField', 'main') )
            $fields = explode(";",Yii::$app->settings->get('blackField', 'main'));
        else
            $fields = $this->getBlackFieldList();

        if ((Yii::$app->user->can('calendarDetailsBox'))&&(!Yii::$app->user->can('SiteAdministrator'))){
            return $info;
        }
        if (Yii::$app->user->can('eventsEventEditEyeClientDetails')) { 
            $name = "";
            if ((!Yii::$app->user->can('calendarEventName'))||(Yii::$app->user->can('SiteAdministrator')))
                $name .= $this->name;
            if ((!Yii::$app->user->can('calendarEventID'))||(Yii::$app->user->can('SiteAdministrator')))
                $name .= " (ID:".$this->code.")";
            $info .= Html::tag('strong',$name);
        }
        if ($this->location!== null)
        {
            if (in_array(Yii::t('app', 'Miejsce'), $fields)) {
            $info .= Html::tag('div', Yii::t('app', 'Z godzinami').': '. $this->location->getDisplayLabel());
            }
        }

        if ($this->manager!== null)
        {
            if (in_array(Yii::t('app', 'PM'), $fields)) {
            if ((!Yii::$app->user->can('calendarEventPM'))||(Yii::$app->user->can('SiteAdministrator')))
                $info .= Html::tag('div',Yii::t('app', 'Manager').': '.$this->getManagerDisplayLabel());
            }
        }
        if (in_array(Yii::t('app', 'Termin'), $fields)) {
            $info .= Html::tag('div', Yii::t('app', 'Termin').':<br />'. $this->getTimeRange());
            $info .= Html::tag('hr');
        }
        if (Yii::$app->user->can('eventsEventEditEyeClientDetails')) { 
        if ($this->customer !== null)
        {
            if (in_array(Yii::t('app', 'Klient'), $fields)) {
                $info .= Html::tag('strong', Yii::t('app', 'Klient').': ').$this->customer->getDisplayLabel();


                if ($this->contact !== null)
                {
                    $info .= Html::tag('div', $this->contact->getDisplayLabel());
                }
                $info .= Html::tag('hr');
            }
        }
        if (in_array(Yii::t('app', 'Uczestnicy'), $fields)) {
            $users = "";
            foreach ($this->users as $u)
            {
                if ($users!="")
                    $users .=", ";
                $users.=$u->displayLabel;
            }
            $info .= Html::tag('div', Yii::t('app', 'Pracownicy').': '. $users);
        }
        }
        if (in_array(Yii::t('app', 'Status'), $fields)) {
        if ((!Yii::$app->user->can('calendarEventStatut'))||(Yii::$app->user->can('SiteAdministrator')))
            $info .= Html::tag('div', Yii::t('app', 'Status').': '. $this->getStatusButton());
        }
        if (in_array(Yii::t('app', 'Poziom'), $fields)) {
        if (Yii::$app->params['companyID']!="wizja")
        {
            $info .= Html::tag('div', Yii::t('app', 'Poziom').': '. $this->level);
        }
        }
        if (Yii::$app->params['companyID']=="e4e")
        {
        $info .= Html::tag('div', Yii::t('app', 'Koordynator)'.': '. $this->getRolesLabel(3))); //typ 3
        $info .= Html::tag('div', Yii::t('app', 'Technicy prowadzący').': '. $this->getRolesLabel(2));  //typ:2
        }
        if (in_array(Yii::t('app', 'Flota'), $fields)) {
            $info .= Html::tag('div', Yii::t('app', 'Flota').': '. $this->getVehiclesLabel());
        }
        $info .= Html::tag('div', Yii::t('app', 'Dodał').': '. $this->creator->getDisplayLabel());
        return $info;
    }

    public function getProjectStatusLabel($separator=', ')
    {
        $val = [];
        $list = CalendarSearch::projectStatusList();
        foreach ($list as $attr=>$label) {
            if ($this->$attr == 1)
            {
                $val[] = $label;
            }

        }
        return implode($separator, $val);
    }

    public function getProjectStatusValue()
    {
         $list = CalendarSearch::projectStatusList();
        $attrs = [
            'offer_sent'=>1,
            'offer_accepted'=>2,
            'ready_to_invoice'=>3,
            'expense_entered'=>4,
            'project_done'=>5,
            'invoice_issued'=>6,
            'invoice_sent'=>7,
            'transfer_booked'=>8,
        ];
        $val['label'] = Yii::t('app', "W przygotowaniu");
        $val['value'] = 0.5;
        foreach ($list as $attr=>$label) {
            if ($this->$attr == 1)
            {
                $val['label'] = $label;
                $val['value'] = 100*$attrs[$attr]/8;
            }

        }
        return $val;   
    }

    public static function getProvisionTypeList()
    {
        $list = [
            self::PROVISION_TYPE_PROFIT => Yii::t('app', 'Od zysku'),
            self::PROVISION_TYPE_OFFER => Yii::t('app', 'Od obrotu'),
        ];
        return $list;
    }

    public function getProvisionTypeLabel()
    {
        $index = $this->provision_type;
        $list = static::getProvisionTypeList();
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function getFinancesOffers()
    {
        $offers =  $this->offers;
        $result = [];
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_finances'=>1])->asArray()->all(), 'id', 'id');
        foreach ( $offers as $offer) {
            if (in_array ( $offer->status, $statuts)) {
                $result[] = $offer;
            }
        }
        if (count($result) > 0) {
            return $result;
        }
        if (count($result) == 0) {
            return ['error' => 1];
        }
        return ['error' => 2];
    }
    public function getFinancesOffersCount()
    {

        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_finances'=>1])->asArray()->all(), 'id', 'id');
        return Offer::find()->where(['event_id'=>$this->id])->andWhere(['status'=>$statuts])->count();
    }

    public function getPlanningOffers()
    {
        $offers =  $this->offers;
        $result = [];
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        foreach ( $offers as $offer) {
            if (in_array ( $offer->status, $statuts)) {
                $result[] = $offer;
            }
        }
        if (count($result) > 0) {
            return $result;
        }
        if (count($result) == 0) {
            return ['error' => 1];
        }
        return ['error' => 2];
    }

    public function getAcceptedAgencyOffers()
    {
        $offers =  $this->agencyOffers;
        $result = [];
        foreach ( $offers as $offer) {
            if ($offer->status == 2) {
                $result[] = $offer;
            }
        }
        if (count($result) > 0) {
            return $result;
        }
        if (count($result) == 0) {
            return ['error' => 1];
        }
        return ['error' => 2];
    }

    /**
     * Get accepted offers.
     * @return Offer[] Array of offers.
     */
    public function getOffersAccepted()
    {
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_finances'=>1])->asArray()->all(), 'id', 'id');
        $models = $this->getOffers()
            ->where([
                'in', 'status', $statuts,
            ])
            ->all();
        return $models;
    }

    public function getOffersSummary()
    {
        $offers = $this->getOffersAccepted();
        $summary = [];
        foreach ($offers as $offer)
        {
            $s = $offer->getOfferValues();
            foreach ($s as $key=>$value)
            {
                $val = ArrayHelper::getValue($summary, $key, 0);
                if ($offer->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                    $summary[$key] = $val + $value*$offer->exchange_rate;
                }else{
                    $summary[$key] = $val + $value;
                }
                
            }
        }
        $summary[Yii::t('app', 'Brutto')] = 0;
        foreach ($offers as $offer)
        {
                if ($offer->priceGroup->currency!=Yii::$app->settings->get('defaultCurrency', 'main')){
                    $summary[Yii::t('app', 'Brutto')] += ($offer->getOfferValues()[Yii::t('app', 'Suma')]+$offer->getVatValue())*$offer->exchange_rate;
                }else{
                    $summary[Yii::t('app', 'Brutto')] += $offer->getOfferValues()[Yii::t('app', 'Suma')]+$offer->getVatValue();
                }
                
        }
        $summary = static::sortSummary($summary);

        return $summary;
    }

    public static function sortSummary($data)
    {
        ksort($data, SORT_NATURAL);
        $sortedData = $data;

        $sum = ArrayHelper::getValue($sortedData, Yii::t('app', 'Suma'), 0);
        $brutto = ArrayHelper::getValue($sortedData, Yii::t('app', 'Brutto'), 0);
        unset($sortedData[Yii::t('app', 'Suma')], $sortedData[Yii::t('app', 'Brutto')]);
        $sortedData[Yii::t('app', 'Suma')] = $sum;
        $sortedData[Yii::t('app', 'Brutto')] = $brutto;

        return $sortedData;
    }

    public function getPlaceholderMap()
    {
        $formatter = Yii::$app->formatter;
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $map = [
            'name' => $this->name,
            'timeStart'=>$formatter->asDatetime($this->getTimeStart(), 'short'),
            'timeEnd'=>$formatter->asDatetime($this->getTimeEnd(), 'short'),
            'link' => Html::a('link', Url::to(['/event/view', 'id'=>$this->id], true)),
        ];

        return $map;
    }

    public function getGalleryAttachments()
    {
        $models = $this->getAttachments()->andWhere(['type'=>Attachment::TYPE_IMAGE])->all();

        return $models;
    }

    public function getCrewNeeded($role = null)
    {
        $data = [];
        $models = OfferRole::find()
            ->innerJoinWith([
                'offer',
                'role'
            ])
            ->where([
                'offer.event_id'=>$this->id,
                'offer.status'=>1
            ])
            ->all();

        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->role_id;
            if (!isset($data[$index]))
            {
                $data[$index] = [
                    'label'=>$model->role->name,
                    'quantity'=>0,
                ];
            }
            $data[$index]['quantity'] += $model->quantity;
        }
        if ($role != null && !isset($data[$role])) {
            $role_model = UserEventRole::findOne(['id'=>$role]);
            $data[$role]['quantity'] = 0;
            $data[$role]['label'] = $role_model->name;
        }

        return $data;
    }

    public function getGearPower()
    {
        $sum = 0;
        $gears = EventGear::find()->where(['event_id'=>$this->id])->all();
        foreach ($gears as $eg) {
            $sum += $eg->quantity*$eg->gear->power_consumption;
        }
        $oGears = EventOuterGearModel::find()->where(['event_id'=> $this->id])->all();
        foreach ($oGears as $eg) {
            $sum += $eg->quantity*$eg->outerGearModel->power_consumption;
        }
        return $sum;        
    }

    public function getGearWeight()
    {
        $sum = 0;
        $gears = EventGear::find()->where(['event_id'=>$this->id])->all();
        foreach ($gears as $eg) {
            if ($eg->gear->no_items)
            {
                $sum +=$eg->quantity*$eg->gear->weight;
                $sum +=$eg->gear->getWeightCase($eg->quantity);
            }else{
                $sum +=$eg->quantity*$eg->gear->weight;
                $sum +=$eg->gear->getWeightCase($eg->quantity);
            }
        }
        $gears = EventExtraItem::find()->where(['event_id'=>$this->id])->all();
        foreach ($gears as $eg) {
            $sum += $eg->weight*$eg->quantity;
        }
        $oGears = EventOuterGearModel::find()->where(['event_id'=> $this->id])->all();
        foreach ($oGears as $eg) {
            if (isset($eg->outerGearModel))
                $sum += $eg->quantity*$eg->outerGearModel->weight;
        }
        return $sum;
    }

    public function getGearVolume()
    {
        $gears = EventGear::find()->where(['event_id'=>$this->id])->all();
        $sum = 0;
        foreach ($gears as $eg) {
            if ($eg->gear->no_items)
            {
                $sum +=$eg->gear->countVolume2($eg->quantity);
            }else{
                $items = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$eg->gear_id])->all(), 'id', 'id');
                $eventGearItems = EventGearItem::find()->where(['event_id'=>$this->id])->andWhere(['IN', 'gear_item_id', $items])->count();  
                $count =  $eg->quantity - $eventGearItems;
                if ($eventGearItems>0)
                {
                    //liczymy dodane egzemplarze i case
                    $ids = ArrayHelper::getColumn($this->getGearItems()->where(['IN', 'id', $items])->all(), 'group_id');
                    $cases = GearGroup::find()->where(['IN', 'id', $ids])->all();
                    $gearNoCase = $this->getGearItems()->where(['group_id'=>null])->andWhere(['IN', 'id', $items])->all();
                    $volumeCase = array_sum(ArrayHelper::getColumn($cases, 'calculatedVolume'));
                    $volumeNoCase = array_sum(ArrayHelper::getColumn($gearNoCase, 'calculatedVolume'));
                    $sum+=$volumeCase+$volumeNoCase;

                }
                if ($count>0)
                {
                    //jesli sprzęt został dodany ilościowo to liczymy tak mniej więcej
                    $sum +=$eg->gear->countVolume2($count);
                }

            }


        }
        $gears = EventExtraItem::find()->where(['event_id'=>$this->id])->all();
        foreach ($gears as $eg) {
            $sum += $eg->volume*$eg->quantity;
        }
        $oGears = EventOuterGearModel::find()->where(['event_id'=> $this->id])->all();
        foreach ($oGears as $eg) {
            if (isset($eg->outerGearModel))
                $sum += $eg->quantity*$eg->outerGearModel->countVolume();
        }
        return $sum;
    }

    public function getGearsSummary()
    {
        $innerGears = ArrayHelper::getColumn($this->getGearItems()->innerJoinWith('gear')->all(), 'gear');
        $weight = $this->getGearWeight();
        $volume = $this->getGearVolume();
        //$outerGears = $this->getOuterGearModels()->all();
        $outerGears = [];       
        $gears = array_merge($innerGears, $outerGears);
        $power = $this->getGearPower();
        $powerCategories = [];
        foreach ($gears as $g)
        {
            $categoryName = $g->category->getMainCategory()->name;
            $val = ArrayHelper::getValue($powerCategories, $categoryName, 0);
            $val += $g->power_consumption;
            $powerCategories[$categoryName] = $val;
        }

        $vehicleVolume = $this->getVehicles()->sum('volume');

        $data = [
            'weight' => $weight,
            'volume' => $volume,
            'power_consumption' => $power,
            'power_consumption_categories'=>$powerCategories,
            'vehicle_volume' => $vehicleVolume,
        ];

        return $data;
    }

    public function getGearCase()
    {
        $ids = ArrayHelper::getColumn($this->getGearItems()->all(), 'group_id');
        $cases = GearGroup::find()->where(['IN', 'id', $ids])->all();
        return $cases;
    }

    public function getGearNoCase()
    {
        $gears = $this->getGearItems()->where(['group_id'=>null])->all();
        return $gears;
    }

    public function getOutcomesForEvent() {
        return $this->hasMany(OutcomesForEvent::className(), ['event_id' => 'id']);
    }

    // @return sprzęt wydany z magazynu dla danego eventu
    public function getGearsSpendFromWarehouse() {
        $result = ['gears'=>[], 'gearsOuter'=>[], 'gearsGroup'=>[]];
        $outcomes_for_event = OutcomesForEvent::find()->where(['event_id' => $this->id])->all();
        foreach ($outcomes_for_event as $outcome_for_event) {
            $outcomes = OutcomesWarehouse::find()->where(['id' => $outcome_for_event->outcome_id])->all();
            foreach ($outcomes as $outcome) {
                $gears = OutcomesGearOur::find()->where(['outcome_id' => $outcome->id])->all();
                $gearsOuter = OutcomesGearOuter::find()->where(['outcome_id' => $outcome->id])->all();
                foreach ($gears as $gear) {
                    if ($gear) {
                        if (isset($result['gears'][$gear->gear_id])) {
                            $result['gears'][$gear->gear_id] += $gear->gear_quantity;
                        }
                        else {
                            $result['gears'][$gear->gear_id] = $gear->gear_quantity;
                        }
                    }
                }
                foreach ($gearsOuter as $gear) {
                    if ($gear) {
                        if ($gear) {
                            if (isset($result['gearsOuter'][$gear->outer_gear_id])) {
                                $result['gearsOuter'][$gear->outer_gear_id] += $gear->gear_quantity;
                            }
                            else {
                                $result['gearsOuter'][$gear->outer_gear_id] = $gear->gear_quantity;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    // @return sprzęt zwrócony z magazynu dla danego eventu
    public function getGearsReturnedToWarehouse() {
        $result = ['gears'=>[], 'gearsOuter'=>[], 'gearsGroup'=>[]];
        $incomes_for_event = IncomesForEvent::find()->where(['event_id' => $this->id])->all();
        foreach ($incomes_for_event as $income_for_event) {
            $incomes = IncomesWarehouse::find()->where(['id' => $income_for_event->income_id])->all();
            foreach ($incomes as $income) {
                $gears = IncomesGearOur::find()->where(['income_id' => $income->id])->all();
                $gearsOuter = IncomesGearOuter::find()->where(['income_id' => $income->id])->all();
                foreach ($gears as $gear) {
                    if ($gear) {
                        if (isset($result['gears'][$gear->gear_id])) {
                            $result['gears'][$gear->gear_id] += $gear->quantity;
                        }
                        else {
                            $result['gears'][$gear->gear_id] = $gear->quantity;
                        }
                    }
                }
                foreach ($gearsOuter as $gear) {
                    if ($gear) {
                        if (isset($result['gearsOuter'][$gear->outer_gear_id])) {
                            $result['gearsOuter'][$gear->outer_gear_id] += $gear->gear_quantity;
                        }
                        else {
                            $result['gearsOuter'][$gear->outer_gear_id] = $gear->gear_quantity;
                        }
                    }
                }
            }
        }

        return $result;
    }

    // @return [nie_zwrocone_gear, nie_zwrocone_magazyn_zewnetrzny, nie_zwrocone_gear_group]
    public function getWarehouseGearDifference() {
        $gearsOut = $this->getGearsSpendFromWarehouse();
        $gearsOurOut = $gearsOut['gears'];
        $gearsOuterOut = $gearsOut['gearsOuter'];

        $gearsIn = $this->getGearsReturnedToWarehouse();
        $gearsOurIn = $gearsIn['gears'];
        $gearsOuterIn = $gearsIn['gearsOuter'];

        // dla każdego wydanego sprzętu sprawdzamy czy została zwrócona taka sama ilość
        foreach ($gearsOurOut as $gear_id => $quantity) {
            if (isset($gearsOurIn[$gear_id])) {
                $gearsOurOut[$gear_id] -= $gearsOurIn[$gear_id];
            }
        }
        foreach ($gearsOuterOut as $gear_id => $quantity) {
            if (isset($gearsOuterIn[$gear_id])) {
                $gearsOuterOut[$gear_id] -= $gearsOuterIn[$gear_id];
            }
        }
        foreach ($gearsOurOut as $gear_id => $quantity) {
            if ($quantity <= 0) {
                unset($gearsOurOut[$gear_id]);
            }
        }
        foreach ($gearsOuterOut as $gear_id => $quantity) {
            if ($quantity <= 0) {
                unset($gearsOuterOut[$gear_id]);
            }
        }

        return [$gearsOurOut, $gearsOuterOut];
    }

    public function countNotReturnedGears() {
        $not_returned = $this->getWarehouseGearDifference();
        return count($not_returned[0]) + count($not_returned[1]);
    }

    public function getEventBreaksDataProvider()
    {
        $query = $this->getEventBreaks();
        $query->orderBy(['start_time' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>false,
            'sort' => false,
        ]);
        return $dataProvider;
    }


    public function getAvailableUsers() {
        $available = [];
        $users = User::find()->all();

        foreach ($users as $user) {
            $available[$user->id] = true;
            if (isset($this->montage_start) && isset($this->montage_end) && $available[$user->id]) {
                $available[$user->id] = $user->isAvailableInRange($this->montage_start, $this->montage_end);
            }
            if (isset($this->event_start) && isset($this->event_end) && $available[$user->id]) {
                $available[$user->id] = $user->isAvailableInRange($this->event_start, $this->event_end);
            }
            if (isset($this->disassembly_start) && isset($this->disassembly_end) && $available[$user->id]) {
                $available[$user->id] = $user->isAvailableInRange($this->disassembly_start, $this->disassembly_end);
            }
        }
        foreach ($this->eventUsers as $eventUser) {
            $available[$eventUser->user_id] = false;
        }
        return $available;
    }

    public function getOfferAcceptedHint()
    {
        $accepted = sizeof($this->getOffersAccepted());
        $all = $this->getOffers()->count();
        $hint = $accepted.'/'.$all;
        return $hint;
    }
    public function updateStatutes($save=false)
    {
        if (empty($this->offers) == false)
        {
            $this->offer_prepared = 1;
            $acceptedOffers = $this->getOffers()
                ->where([
                    'status'=>Offer::STATUS_ACCEPT,
                ])
                ->count();
            if ($acceptedOffers > 0)
            {
                $this->offer_accepted = 1;
            }
        }

        if ($this->expense_status==1 && $this->invoice_issued == static::INVOICE_VALUE_EQUAL)
        {
            $this->project_settled = 1;
        }

        $this->updateProjectPaid();
        $this->updateExpensesPaid();
        $this->updateProjectDone();
        $this->updateInvoiceNumbers();

        if ($save == true)
        {
            return $this->save();
        }
        return true;
    }

    public function offerHasBeenSent($save=false)
    {
        $this->offer_sent = 1;
        $this->offer_sent_date = new Expression('NOW()');
        $this->offer_sent_user_id = Yii::$app->user->id;
        if ($save)
        {
           return $this->save();
        }

        return true;
    }

    public static function invoiceValueList()
    {
        $list = [
            static::INVOICE_VALUE_EQUAL => Yii::t('app', 'Zafakturowane'),
            static::INVOICE_VALUE_LESS => Yii::t('app', 'Kwota faktury jest niższa niż wartości ofert'),
            static::INVOICE_VALUE_GREATER => Yii::t('app', 'Kwota faktury jest wyższa niż wartości ofert'),
            static::INVOICE_VALUE_NONE => Yii::t('app', 'Niezafakturowane'),
        ];
        return $list;
    }

    public function updateIvoiceIssued($save=false)
    {
        $status = static::INVOICE_VALUE_NONE;
        $offerValue = ArrayHelper::getValue($this->getOffersSummary(), Yii::t('app', 'Brutto'), 0);
        $invoiceValue = $this->getInvoicesValue();

        if($offerValue>$invoiceValue)
        {
            $status = static::INVOICE_VALUE_LESS;
        }
        elseif ($offerValue<$invoiceValue)
        {
            $status = static::INVOICE_VALUE_GREATER;
        }
        elseif ($offerValue>0 && $offerValue==$invoiceValue)
        {
            $status = static::INVOICE_VALUE_EQUAL;
        }
        $this->invoice_issued = $status;

        if ($save == true)
        {
            return $this->save();
        }
        return true;
    }

    /**
     * Get value from invoices.
     * @return float Value of assigned invoices.
     */
    public function getInvoicesValue()
    {
        $value = $this->getInvoices()
            ->sum('total');
        return $value;
    }

    public function getInvoiceIssuedLabel()
    {
        $list = static::invoiceValueList();
        return ArrayHelper::getValue($list, $this->invoice_issued, UNDEFINDED_STRING);
    }

    public function updateExpenseStatus($save=false)
    {
        $count = $this->getEventExpenses()
            ->where([
                '!=',
                'status',
                EventExpense::STATUS_INVOICE_BOOKED,
            ])
            ->count();

        $expensesIds = array_keys($this->getEventExpenses()->indexBy('id')->all());
        $expenseContents = ExpenseContent::find()
            ->where(['event_expense_id'=>$expensesIds])
            ->groupBy(['event_expense_id'])
            ->innerJoinWith('expense')
            ->count();

        if ($count == 0 && sizeof($expensesIds)==$expenseContents)
        {
            $this->expense_status = 1;
        }
        else
        {
            $this->expense_status = 0;
        }

        if ($save)
        {
            return $this->save();
        }
        return true;
    }

    public function updateExpensesPaid($save=false)
    {
        $eventExpenses = $this->getEventExpenses()->sum('amount');
        $expenses = $this->getExpenses()
            ->select(['total' => 'SUM(total)', 'alreadypaid' => 'SUM(alreadypaid)', 'netto'=>'SUM(netto)'])
            ->asArray()
            ->one();

        $total = $expenses['total'];
        $netto = $expenses['netto'];
        $paid = $expenses['alreadypaid'];
        if ($total <= $paid && $netto >= $eventExpenses)
        {
            $this->expenses_paid = 1;
        }
        else
        {
            $this->expenses_paid = 0;
        }


        if ($save)
        {
            return $this->save();
        }
        return true;
    }

    public function updateProjectPaid($save=false)
    {
        $sums = $this->getInvoices()
            ->select(['total' => 'SUM(total)', 'alreadypaid' => 'SUM(alreadypaid)', 'netto'=>'SUM(netto)'])
            ->asArray()
            ->one();
        $total = $sums['total'];
        $paid = $sums['alreadypaid'];

        if ($paid==0)
        {
            $this->project_paid = static::PROJECT_PAID_NONE;
        }

        if ($total <= $paid)
        {
            $this->project_paid = static::PROJECT_PAID_ALL;
        }
        else if ($total > $paid)
        {
            $this->project_paid = static::PROJECT_PAID_PARTIAL;
        }

        if ($save)
        {
            return $this->save();
        }
        return true;
    }

    public function updateProjectDone($save=false)
    {
        if (
            $this->offer_prepared == 1 &&
            $this->offer_accepted == 1 &&
            $this->ready_to_invoice == 1 &&
            $this->expense_entered == 1 &&
            $this->invoice_issued == static::INVOICE_VALUE_EQUAL &&
            $this->expense_status == 1 &&
            $this->project_settled == 1 &&
            $this->project_paid == static::PROJECT_PAID_ALL &&
            $this->expenses_paid == 1
        )
        {
            $this->project_done = 1;
        }
        else
        {
            $this->project_done = 0;
        }

        if ($save)
        {
            return $this->save();
        }
        return true;
    }

    public static function projectPaidList()
    {
        $list = [
            static::PROJECT_PAID_NONE => Yii::t('app', 'Niezapłacony'),
            static::PROJECT_PAID_PARTIAL => Yii::t('app', 'Częściowo'),
            static::PROJECT_PAID_ALL => Yii::t('app', 'W całości'),
        ];

        return $list;
    }
    public function getProjectPaidLabel()
    {
        return ArrayHelper::getValue(static::projectPaidList(), $this->project_paid, UNDEFINDED_STRING);
    }

    public function getOfferSentHint()
    {
        $hint = '-';
        if ($this->offerSentUser!== null && $this->offer_sent==1)
        {
            $user = $this->offerSentUser->getDisplayLabel();
            $date = Yii::$app->formatter->asDatetime($this->offer_sent_date);
            $hint = $user.' ('.$date.')';
        }
        return $hint;
    }

    public function getReadyToInvoiceHint()
    {
        $hint = '-';
        if ($this->readyToInvoiceUser!== null && $this->ready_to_invoice==1)
        {
            $user = $this->readyToInvoiceUser->getDisplayLabel();
            $date = Yii::$app->formatter->asDatetime($this->ready_to_invoice_date);
            $hint = $user.' ('.$date.')';
        }
        return $hint;
    }

    public function getExpenseEnteredHint()
    {
        $hint = '-';
        if ($this->expenseEnteredUser!== null && $this->expense_entered==1)
        {
            $user = $this->expenseEnteredUser->getDisplayLabel();
            $date = Yii::$app->formatter->asDatetime($this->expense_entered_date);
            $hint = $user.' ('.$date.')';
        }
        return $hint;
    }

    public function updateInvoiceNumbers()
    {
        $numbers = $this->getInvoices()->select('fullnumber')->column();
        $this->invoice_number = implode('; ', $numbers);
    }


	public function getInvoices()
	{
		return $this->hasMany(Invoice::className(), ['owner_id'=>'id'])->andWhere(['invoice.owner_type'=>Invoice::OWNER_TYPE_EVENT]);
	}

	public function getCompatibilityRoleList() {
        $roles = UserEventRole::find()->where(['compatibility' => 0])->andWhere(['active'=>1])->all();
        $roles = ArrayHelper::map($roles, 'id', 'name');

        /** @var \common\models\User $user */
        $user = Yii::$app->user->getIdentity();
        $eventUsers = EventUser::find()->where(['event_id' => $this->id])->andWhere(['user_id' => $user->id])->all();
        foreach ($eventUsers as $eventUser) {
            /** @var \common\models\EventUser $eventUser */
            foreach ($eventUser->userEventRoles as $role) {
                if (!in_array($role->id, $roles)) {
                    $roles[$role->id] = $role->name;
                }
            }
        }

        return $roles;
    }

    public function userWorkingTimeChanged() {
        /*
        if ($this->crew_working_time_changed == 0 && Yii::$app->settings->get('eventNotifications', 'main') == self::NOTIFICATIONS_OFF) {
            $this->crew_working_time_changed = 1;
            $this->saveUserWorkingHours();
            $this->save();
        }
        */
    }

    public function saveUserWorkingHours() {
        foreach ($this->users as $user) {
            $savedUser = new SavedEventUsers();
            $savedUser->user_id = $user->id;
            $savedUser->event_id = $this->id;
            $savedUser->save();
        }
        foreach ($this->eventUserPlannedWrokingTimes as $time) {
            $savedTime = new SavedUserWorkingTime();
            $savedTime->event_id = $this->id;
            $savedTime->user_id = $time->user->id;
            $savedTime->start_time = $time->start_time;
            $savedTime->end_time = $time->end_time;
            $savedTime->save();
        }
    }

    public function sendNotifications() {
        if ($this->crew_working_time_changed == 1 && Yii::$app->settings->get('eventNotifications', 'main') == self::NOTIFICATIONS_OFF) {
            $this->checkEventUsersDifferences();
            $this->crew_working_time_changed = 0;
            $this->save();
            SavedUserWorkingTime::deleteAll(['event_id' => $this->id]);
            SavedEventUsers::deleteAll(['event_id' => $this->id]);
        }
    }

    public function checkEventUsersDifferences() {
        $previousUsers = $this->getSavedUsers()->indexBy('id')->asArray()->all();
        $actualUsers = $this->getUsers()->indexBy('id')->asArray()->all();
        $newUsers = [];
        $deletedUsers = [];
        foreach ($actualUsers as $id => $user) {
            if (!key_exists($id, $previousUsers)) {
                $newUsers[] = $user;
            }
        }
        foreach ($previousUsers as $id => $user) {
            if (!key_exists($id, $actualUsers)) {
                $deletedUsers[$user['id']] = $user;
            }
        }

        foreach ($newUsers as $user) {
            $eventUser = EventUser::find()->where(['user_id' => $user['id']])->andWhere(['event_id' => $this->id])->one();
            Notification::sendUserNotifications($eventUser->user, Notification::USER_ADDED_TO_EVENT, [$this, $eventUser]);
        }
        foreach ($deletedUsers as $user) {
            $user = User::findOne($user['id']);
            Notification::sendUserNotifications($user, Notification::USER_REMOVED_FROM_EVENT, [$this, $user]);
        }

        foreach ($actualUsers as $id => $user) {
            $deletedWorkingTime = [];
            $addedWorkingTime = [];

            $workingHoursActual = [];
            $workingHoursPrev = [];
            $previousHours = SavedUserWorkingTime::find()->where(['user_id' => $id])->andWhere(['event_id' => $this->id])->all();
            $actualHours = EventUserPlannedWrokingTime::find()->where(['user_id' => $id])->andWhere(['event_id' => $this->id])->all();
            foreach ($previousHours as $time) {
                $workingHoursPrev[$time->start_time][$time->end_time] = $time;
            }
            foreach ($actualHours as $time) {
                $workingHoursActual[$time->start_time][$time->end_time] = $time;
            }

            foreach ($workingHoursPrev as $start => $arr) {
                $end = key($arr);
                if (!isset($workingHoursActual[$start][$end])) {
                    $deletedWorkingTime[] = current($arr);
                }
            }
            foreach ($workingHoursActual as $start => $arr) {
                $end = key($arr);
                if (!isset($workingHoursPrev[$start][$end])) {
                    $addedWorkingTime[] = current($arr);
                }
            }
            /** @var \common\models\EventUserPlannedWrokingTime $time */
            foreach ($addedWorkingTime as $time) {
                if (!key_exists($time->user->id, $deletedUsers)) {
                    Notification::sendUserNotifications($time->user, Notification::EVENT_SCHEDULE_CHANGE, [$this, $time]);
                }
            }
            foreach ($deletedWorkingTime as $time) {
                if (!key_exists($time->user->id, $deletedUsers)) {
                    Notification::sendUserNotifications($time->user, Notification::EVENT_SCHEDULE_CHANGE, [$this, $time]);
                }
            }

        }
    }

    public static function sendAllNotifications() {
        if (Yii::$app->settings->get('eventNotifications', 'main') == self::NOTIFICATIONS_OFF) {
            $events = Event::findAll(['crew_working_time_changed' => 1]);
            foreach ($events as $event) {
                $event->sendNotifications();
            }
        }
    }
    public function getAssignedDeals($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getDeals();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedEstimates($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getEstimates();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }
    public function getAssignedBriefs($params = [])
    {
        $params = array_merge(
            Yii::$app->request->queryParams,
            $params
        );
        $query = $this->getBriefs();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);

        return $dataProvider;
    }

    public function getAssignedTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['event_id' => 'id'])->where(['main'=>1])->orWhere(['user_id'=>Yii::$app->user->identity->id])->orderBy(['end_time'=>SORT_ASC])->all();
    }

    public function getAssignedExtraItems()
    {
        $query = $this->hasMany(\common\models\EventExtraItem::className(), ['event_id' => 'id'])->orderBy(['gear_category_id'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);
        return $dataProvider;
    }

    public function getAssignedExtraItemsPacklist($packlist)
    {
        $query = \common\models\PacklistExtra::find()->where(['packlist_id'=>$packlist]);
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort'=>false,
            'pagination'=>false,
        ]);
        return $dataProvider;
    }

    public function deleteAllTasks()
    {
        $tasks = Task::find()->where(['event_id'=>$this->id])->all();
        foreach ($tasks as $task)
        {
            $task->delete();
        }
        $tasks = TaskCategory::find()->where(['event_id'=>$this->id])->all();
        foreach ($tasks as $task)
        {
            $task->delete();
        }
    }

    public function copyTasks()
    {
        if ($this->tasks_schema_id)
        {
            $tasksSchema = TasksSchema::findOne($this->tasks_schema_id);
            foreach ($tasksSchema->tasksSchemaCats as $category)
            {
                $cat = new TaskCategory;
                $cat->name = $category->name;
                $cat->order = $category->order;
                $cat->event_id = $this->id;
                $cat->color = $category->color;
                $cat->save();
                foreach ($category->taskSchemas as $schema)
                {
                    $schema->loadLinkedObjects();
                    $task = new Task;
                    $task->title = $schema->name;
                    $task->content = $schema->description;
                    $task->order = $schema->order;
                    $task->task_category_id = $cat->id;
                    $task->event_id = $this->id;
                    $task->only_one = $schema->only_one;
                    $task->teamIds = $schema->teamIds;
                    if (($schema->time_type!=1)&&($this->getTimeStart()))
                    {
                        $secs = 3600*$schema->hours+60*$schema->minutes+24*3600*$schema->days;
                        if ($schema->time_type<4)
                        {
                            $start = $this->getTimeStart();
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);

                        }else{
                            $start = $this->getTimeEnd();
                            $rok = substr($start, 0, 4);
                            $miesiac = substr($start, 5, 2);
                            $dzien = substr($start, 8, 2);
                            $godzina = substr($start, 11, 2);
                            $time = mktime($godzina, 0, 0, $miesiac, $dzien, $rok);
                        }
                        if (($schema->time_type==2)||($schema->time_type==4))
                        {
                            $time = $time-$secs;
                        }else{
                            $time = $time+$secs;
                        }
                        $task->datetime = date("Y-m-d H:i:s", $time);
                    }
                    $task->save();
                    $task->linkTeams();
                    foreach ($schema->users as $user)
                    {
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    if (($schema->manager)&&($this->manager_id)){
                        $tu = new UserTask;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }
                    foreach ($schema->roles as $role)
                    {
                        $tr = new TaskRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role_id = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->notificationUsers as $user)
                    {
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $user->id;
                        $tu->save();
                    }
                    if (($schema->manager_notification)&&($this->manager_id)){
                        $tu = new TaskNotificationUser;
                        $tu->task_id = $task->id;
                        $tu->user_id = $this->manager_id;
                        $tu->save();
                    }
                    foreach ($schema->notificationRoles as $role)
                    {
                        $tr = new TaskNotificationRole;
                        $tr->task_id = $task->id;
                        $tr->user_event_role = $role->id;
                        $tr->save();
                    }
                    foreach ($schema->taskSchemaNotifications as $no)
                    {
                        $not = new TaskNotification;
                        $not->task_id = $task->id;
                        $not->time_type = $no->time_type;
                        $not->time = $no->time;
                        $not->email = $no->email;
                        $not->sms = $no->sms;
                        $not->push = $no->push;
                        $not->text = $no->text;
                        $not->sent = 0;
                        $not->save();
                    }

                }
            }
        }
    }

    public function getTaskProvider()
    {
        $query = Task::find()->where(['event_id'=>$this->id])->orderBy(['status'=>SORT_ASC, 'datetime'=>SORT_ASC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }

    public function getOtherTasks()
    {
        $tasks = Task::find()->where(['event_id'=>$this->id])->andWhere(['is', 'task_category_id', null])->orderBy(['order'=>SORT_ASC])->all();
        return $tasks;
    }

    public function getForTasks()
    {
        $tasks = Task::find()->where(['for_event'=>$this->id])->orderBy(['order'=>SORT_ASC])->all();
        return $tasks;
    }

    public function getVehiclesNeeded()
    {
        $data = [];
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        $models = OfferVehicle::find()
            ->innerJoinWith([
                'offer' ])
            ->where([
                'offer.event_id'=>$this->id,
                'offer.status'=>$statuts
            ])
            ->all();            


        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->vehicle_id;
            $index2 = $model->type;
            if (!isset($data[$index2][$index]))
            {
                $data[$index2][$index] = [
                    'label'=>$model->vehicle->name,
                    'quantity'=>0,
                    'added'=>0,
                    'vehicles'=>[]
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;        
    }

    public function getVehiclesNeeded2()
    {
        $data = [];         
        $models = EventOfferVehicle::find()->where(['event_id'=>$this->id])->all();

        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index2 = $model->vehicle_model_id;
            $index = $model->schedule;
            if (!isset($data[$index2][$index]))
            {
                $s = EventSchedule::find()->where(['name'=>$model->schedule, 'event_id'=>$this->id])->one();
                $data[$index2][$index] = [
                    'label'=>$model->vehicle->name,
                    'quantity'=>0,
                    'added'=>0,
                    'vehicles'=>[],
                    'schedule'=> $s
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;       
    }
    public function getCrewNeededNew()
    {
        $data = [];         
        $models = EventOfferRole::find()->where(['event_id'=>$this->id])->all();

        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->user_role_id;
            $index2 = $model->schedule;
            if (!isset($data[$index2][$index]))
            {
                $data[$index2][$index] = [
                    'label'=>$model->role->name,
                    'quantity'=>0,
                    'added'=>0,
                    'users'=>[]
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;
    }
    public function getCrewNeededNew2()
    {
        $data = [];         
        $models = EventOfferRole::find()->where(['event_id'=>$this->id])->all();

        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index2 = $model->user_role_id;
            $index = $model->schedule;
            if (!isset($data[$index2][$index]))
            {
                $s = EventSchedule::find()->where(['name'=>$model->schedule, 'event_id'=>$this->id])->one();
                $data[$index2][$index] = [
                    'label'=>$model->role->name,
                    'quantity'=>0,
                    'added'=>0,
                    'users'=>[],
                    'schedule'=> $s
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;
    }
    public function getVehicleNeededNew()
    {
        $data = [];         
        $models = EventOfferVehicle::find()->where(['event_id'=>$this->id])->all();

        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->vehicle_model_id;
            $index2 = $model->schedule;
            if (!isset($data[$index2][$index]))
            {
                $data[$index2][$index] = [
                    'label'=>$model->vehicle->name,
                    'quantity'=>0,
                    'added'=>0,
                    'vehicles'=>[]
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;
    }

    public function getCrewNeeded2()
    {
        $data = [];
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        $models = OfferRole::find()
            ->innerJoinWith([
                'offer',
                'role'
            ])
            ->where([
                'offer.event_id'=>$this->id,
                'offer.status'=>$statuts
            ])
            ->all();            


        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->role_id;
            $index2 = $model->time_type;
            if (!isset($data[$index2][$index]))
            {
                $data[$index2][$index] = [
                    'label'=>$model->role->name,
                    'quantity'=>0,
                    'added'=>0,
                    'users'=>[]
                ];
            }
            $data[$index2][$index]['quantity'] += $model->quantity;
        }

        return $data;
    }

    public function getCrewNeeded3()
    {
        $data = [];
                $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');

        $models = OfferRole::find()
            ->innerJoinWith([
                'offer',
                'role'
            ])
            ->where([
                'offer.event_id'=>$this->id,
                'offer.status'=>$statuts
            ])
            ->all();            


        foreach ($models as $model)
        {
            /* @var $model \common\models\OfferRole */

            $index = $model->role_id;
            $index2 = $model->time_type;
            if (!isset($data[$index][$index2]))
            {
                $data[$index][$index2] = [
                    'label'=>$model->role->name,
                    'quantity'=>0,
                    'added'=>0,
                    'users'=>[],
                    'user_ids'=>[]
                ];
            }
            $data[$index][$index2]['quantity'] += $model->quantity;
        }

        return $data;
    }
    public function getUsersNoRole()
    {
        $eu_ids = ArrayHelper::map(EventUserRole::find()->asArray()->all(), 'event_user_id', 'event_user_id');
        $users = EventUser::find()->where(['event_id' => $this->id])->andWhere(['NOT IN', 'id', $eu_ids])->all();
        return $users;
    } 

    public function getVehicleNoModel()
    {
        $ids = ArrayHelper::map(EventVehicleWorkingHours::find()->where(['event_id' => $this->id])->asArray()->all(), 'vehicle_id', 'vehicle_id');
        $vehicles = EventVehicle::find()->where(['event_id' => $this->id])->andWhere(['NOT IN', 'vehicle_id', $ids])->all();
        return $vehicles;
    }

    public function getAssignedUsersByTime2()
    {
        $data = $this->getCrewNeededNew2();
        $eu_ids = ArrayHelper::map(EventUser::find()->where(['event_id' => $this->id])->asArray()->all(), 'id', 'id');
        $users = EventUserRole::find()->andWhere(['event_user_id' => $eu_ids])->all();
        foreach($users as $user)
        {
            if (isset($user->working)){
            $work = $user->working;
            foreach ($this->eventSchedules as $schedule)
            {
                //if ($schedule->start_time < $work->end_time && $schedule->end_time > $work->start_time) 
                //{
                if ($schedule->id==$work->event_schedule_id) 
                {
                    $index2 = $user->user_event_role_id;
                    $index = $schedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[],
                            'schedule'=>$schedule
                        ];
                    }                    
                    $data[$index2][$index]['users'][] = $user;
                    $data[$index2][$index]['added'] += 1;
                }
            }

            }
        }
        return $data;
    }

    public function getAssignedUsersByTime()
    {
        $data2 = $this->getCrewNeededNew();
        $data = [];
        $eu_ids = ArrayHelper::map(EventUser::find()->where(['event_id' => $this->id])->asArray()->all(), 'id', 'id');
        $users = EventUserRole::find()->andWhere(['event_user_id' => $eu_ids])->all();
        foreach ($this->eventSchedules as $schedule)
        {
            $index2 = $schedule->name;
            if (isset($data2[$index2]))
                $data[$index2] = $data2[$index2];
            else
                $data[$index2] = [];
        }
        foreach ($data2 as $key => $d)
        {
            if (!isset($data[$key]))
                $data[$key] = $data2[$key];
        }
        foreach($users as $user)
        {
            if (isset($user->working)){
            $work = $user->working;
            foreach ($this->eventSchedules as $schedule)
            {
                //if ($schedule->start_time < $work->end_time && $schedule->end_time > $work->start_time) 
                //{
                if ($schedule->id==$work->event_schedule_id) 
                {
                    $index = $user->user_event_role_id;
                    $index2 = $schedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[],
                            'schedule'=>$schedule
                        ];
                    }                    
                    $data[$index2][$index]['users'][] = $user;
                    $data[$index2][$index]['added'] += 1;
                }
            }

            }
        }
        return $data;
    }

    public function getAssignedVehiclesByModel()
    {
        $data = $this->getVehiclesNeeded2();
        $vehicles = EventVehicleWorkingHours::find()->andWhere(['event_id' => $this->id])->all();
        foreach($vehicles as $vehicle)
        {
            if (isset($vehicle->vehicle_model_id)){
            $vm = $vehicle->vehicle_model_id;
                    $index2 = $vm;
                    $index = $vehicle->eventSchedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$vehicle->vehicleModel->name,
                            'quantity'=>0,
                            'added'=>0,
                            'vehicles'=>[],
                            'schedule'=>$vehicle->eventSchedule
                        ];
                    }                    
                    $data[$index2][$index]['vehicles'][] = $vehicle;
                    $data[$index2][$index]['added'] += 1;
                
            }else{
                $vm = 0;
                    $index2 = $vm;
                    $index = $vehicle->eventSchedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>Yii::t('app', 'Nieprzypisane'),
                            'quantity'=>0,
                            'added'=>0,
                            'vehicles'=>[],
                            'schedule'=>$vehicle->eventSchedule
                        ];
                    }                    
                    $data[$index2][$index]['vehicles'][] = $vehicle;
                    $data[$index2][$index]['added'] += 1;
                               
            }
        } 
        return $data;       
    }

    public function getAssignedVehiclesByTime()
    {
        $data2 = $this->getVehicleNeededNew();
        $data = [];
        $vehicles = EventVehicleWorkingHours::find()->andWhere(['event_id' => $this->id])->all();
        foreach ($this->eventSchedules as $schedule)
        {
            $index2 = $schedule->name;
            if (isset($data2[$index2]))
                $data[$index2] = $data2[$index2];
            else
                $data[$index2] = [];
        }
        foreach ($data2 as $key => $d)
        {
            if (!isset($data[$key]))
                $data[$key] = $data2[$key];
        }
        foreach($vehicles as $vehicle)
        {
            if (isset($vehicle->vehicle_model_id)){
                    $vm = $vehicle->vehicle_model_id;
                    $index = $vm;
                    $index2 = $vehicle->eventSchedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        
                        $data[$index2][$index] = [
                            'label'=>$vehicle->vehicleModel->name,
                            'quantity'=>0,
                            'added'=>0,
                            'vehicles'=>[]
                        ];
                    }                    
                    $data[$index2][$index]['vehicles'][] = $vehicle;
                    $data[$index2][$index]['added'] += 1;
            }else{
                $vm = 0;
                    $index = $vm;
                    $index2 = $vehicle->eventSchedule->name;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>Yii::t('app', 'Nieprzypisane'),
                            'quantity'=>0,
                            'added'=>0,
                            'vehicles'=>[]
                        ];
                    }                    
                    $data[$index2][$index]['vehicles'][] = $vehicle;
                    $data[$index2][$index]['added'] += 1;          
            }
        }
        return $data;
    }

    public function getAssignedUsersByTimeArray()
    {
         $data = $this->getCrewNeeded2();
        $eu_ids = ArrayHelper::map(EventUser::find()->where(['event_id' => $this->id])->asArray()->all(), 'id', 'id');
        $users = EventUserRole::find()->andWhere(['event_user_id' => $eu_ids])->all();
        foreach($users as $user)
        {
            if (isset($user->working)){
            $work = $user->working;
                if ($this->event_start < $work->end_time && $this->event_end > $work->start_time) 
                {
                    $index = $user->user_event_role_id;
                    $index2 = 3;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[]
                        ];
                    }      
                                    if (($work->start_time!=$this->event_start)||($work->end_time!=$this->event_end)){
                                                $diff = 1;
                                        }else{
                                            $diff = 0;
                                        }             
                    $data[$index2][$index]['users'][] = ['user'=>["first_name"=>$user->eventUser->user->first_name, "last_name"=>$user->eventUser->user->last_name, 'id'=>$user->eventUser->user->id, 'photo'=>$user->eventUser->user->photo, 'phone'=>$user->eventUser->user->phone, 'email'=>$user->eventUser->user->email], 'start'=>$work->start_time, 'end'=>$work->end_time, 'diff_time'=>$diff];
                    $data[$index2][$index]['added'] += 1;
                }
                if ($this->disassembly_start < $work->end_time && $this->disassembly_end > $work->start_time) 
                {
                    $index = $user->user_event_role_id;
                    $index2 = 4;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[]
                        ];
                    } 
                                    if (($work->start_time!=$this->disassembly_start)||($work->end_time!=$this->disassembly_end)){
                                                $diff = 1;
                                        }else{
                                            $diff = 0;
                                        }                   
                    $data[$index2][$index]['users'][] = ['user'=>["first_name"=>$user->eventUser->user->first_name, "last_name"=>$user->eventUser->user->last_name, 'id'=>$user->eventUser->user->id, 'photo'=>$user->eventUser->user->photo, 'phone'=>$user->eventUser->user->phone, 'email'=>$user->eventUser->user->email], 'start'=>$work->start_time, 'end'=>$work->end_time, 'diff_time'=>$diff];
                    $data[$index2][$index]['added'] += 1;
                }
                if ($this->montage_start < $work->end_time && $this->montage_end > $work->start_time) 
                {
                    $index = $user->user_event_role_id;
                    $index2 = 2;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[]
                        ];
                    }   
                                    if (($work->start_time!=$this->montage_start)||($work->end_time!=$this->montage_end)){
                                                $diff = 1;
                                        }else{
                                            $diff = 0;
                                        }                 
                    $data[$index2][$index]['users'][] = ['user'=>["first_name"=>$user->eventUser->user->first_name, "last_name"=>$user->eventUser->user->last_name, 'id'=>$user->eventUser->user->id, 'photo'=>$user->eventUser->user->photo, 'phone'=>$user->eventUser->user->phone, 'email'=>$user->eventUser->user->email], 'start'=>$work->start_time, 'end'=>$work->end_time, 'diff_time'=>$diff];
                    $data[$index2][$index]['added'] += 1;
                }
                if ($this->packing_start < $work->end_time && $this->packing_end > $work->start_time) 
                {
                    $index = $user->user_event_role_id;
                    $index2 = 1;
                    if (!isset($data[$index2][$index]))
                    {
                        $data[$index2][$index] = [
                            'label'=>$user->userEventRole->name,
                            'quantity'=>0,
                            'added'=>0,
                            'users'=>[]
                        ];
                    }  
                                     if (($work->start_time!=$this->packing_start)||($work->end_time!=$this->packing_end)){
                                                $diff = 1;
                                        }else{
                                            $diff = 0;
                                        }                 
                    $data[$index2][$index]['users'][] = ['user'=>["first_name"=>$user->eventUser->user->first_name, "last_name"=>$user->eventUser->user->last_name, 'id'=>$user->eventUser->user->id, 'photo'=>$user->eventUser->user->photo, 'phone'=>$user->eventUser->user->phone, 'email'=>$user->eventUser->user->email], 'start'=>$work->start_time, 'end'=>$work->end_time, 'diff_time'=>$diff];
                    $data[$index2][$index]['added'] += 1;
                }
            }
        }
        $arrayData = [];
        foreach ($data as $key=>$val)
        {
            $tmp = [];
            $tmp['roles'] = [];
            if ($key==1)
            {
                $tmp['name']=Yii::t('app', 'Pakowanie');
            }
            if ($key==2)
            {
                $tmp['name']=Yii::t('app', 'Montaż');
            }
            if ($key==3)
            {
                $tmp['name']=Yii::t('app', 'Event');
            }
            if ($key==4)
            {
                $tmp['name']=Yii::t('app', 'Demontaż');
            }
            foreach ($val as $key2=>$role)
            {
                $role['id'] = $key2;
                $tmp['roles'][] = $role;
            }
            $arrayData[]=$tmp;
        }
        return $arrayData;       
    }

    public function getAssignedGearsArray()
    {
        $data = [];
        foreach ($this->eventGears as $gear)
        {
            $tmp['id'] = $gear->gear->id;
            $tmp['name'] = $gear->gear->name;
            $tmp['quantity'] = $gear->quantity;
            $tmp['photo'] = $gear->gear->photo;
            $tmp['packing'] = $gear->gear->getPacking2();
            $tmp['start'] = $gear->start_time;
            $tmp['end'] = $gear->end_time;
            $category = $gear->gear->category;
            $categories = $category->parents()->all();
            if (count($categories) > 1) {
                    $category_name = $categories[1]->name;
            }else{
                    $category_name = $category->name;
            }
            $tmp['category'] = $category_name;
            $data[] = $tmp;
        }
        return $data;
    }

    public function getAssignedOuterGearsArray()
    {
        $data = [];
        foreach ($this->eventOuterGears as $gear)
        {
            $tmp['id'] = $gear->outerGear->id;
            $tmp['name'] = $gear->outerGear->outerGearModel->name;
            $tmp['quantity'] = $gear->quantity;
            $tmp['photo'] = $gear->outerGear->outerGearModel->photo;
            $tmp['start'] = $gear->reception_time;
            $tmp['end'] = $gear->return_time;
            $category = $gear->outerGear->outerGearModel->category;
            $categories = $category->parents()->all();
            if (count($categories) > 1) {
                    $category_name = $categories[1]->name;
            }else{
                    $category_name = $category->name;
            }
            $tmp['category'] = $category_name;
            $tmp['company'] = $gear->outerGear->company->displayLabel;
            $tmp['info'] = $gear->description;
            if (isset($gear->user))
                $tmp['user'] = $gear->user;
            else
                $tmp['user'] = null;
            $data[] = $tmp;
        }
        return $data;
    }

    public function saveEventCosts()
    {
        $costs= [];
        $costs[Yii::t('app', 'Obsługa')] = 0;
        $costs[Yii::t('app', 'Suma')] = 0;
        EventCost::deleteAll(['event_id'=>$this->id]);
        $expenses = $this->getEventExpenses()
            ->where([
                'type'=>EventExpense::TYPE_SINGLE,
            ])
            ->all();
        foreach ($expenses as $expense)
        {
            /* @var $expense EventExpense */
            $key = $expense->section;
            if ($key =="")
            {
                $key = Yii::t('app', 'Inne');
            }
            if (!isset($costs[$key]))
                $costs[$key] = 0;
            $costs[$key] += $expense->amount;
            $costs[Yii::t('app', 'Suma')] += $expense->amount;
        }

        $workingTimeData = $this->getWorkingTimeSummaryAll();
        foreach ($workingTimeData as $data)
        {
            if (\Yii::$app->params['companyID']=='djak')
            {
            $costs[Yii::t('app', 'Obsługa')] += $data['brutto'];
            $costs[Yii::t('app', 'Suma')] += $data['brutto']; 
            }else{
            $costs[Yii::t('app', 'Obsługa')] += $data['sum'];
            $costs[Yii::t('app', 'Suma')] += $data['sum'];                
            }


        }

        foreach ($costs as $k=>$v)
        {
            $eventCost = new EventCost();
            $eventCost->event_id = $this->id;
            $eventCost->section = $k;
            $eventCost->value = $v;

            $eventCost->save();
        }
    }

    public function getEventCosts()
    {
        $values = EventCost::find()->where(['event_id'=>$this->id])->all();
        $return[Yii::t('app', 'Suma')] = 0;
        foreach ($values as $val)
        {
            $return[$val->section] = $val->value;
        }
        return $return;
    }

    public function getEventValueSum()
    {
        $values = $this->getEventValueAll();
        return $values[Yii::t('app', 'Suma')];
    }
    public function getEventProfits()
    {
        $values = $this->getEventValueAll();
        $values2 = $this->getEventCosts();
        foreach ($values2 as $key=>$val)
        {
            
            if (!isset($values[$key]))
                $values[$key] = 0;
            $values[$key] = $values[$key]-$values2[$key];
        }
        return array_reverse($values);
    }

    public function getEventPMcost()
    {
        $offers = $this->getOffersAccepted();
        $sum = 0;
        foreach ($offers as $offer)
        {
            $sum += $offer->getPMCost();
        }
        return $sum;
    }

    public function getCurrentUserWork()
    {
        $eu = EventUser::find()->where(['event_id'=>$this->id])->andWhere(['user_id'=>Yii::$app->user->id])->one();
        if ($eu){
            $roles = EventUserRole::find()->where(['event_user_id'=>$eu->id])->all();
            return $roles;
        }
        return false;
    }

    public function getCurrentUserWorkArray()
    {
        $eu = EventUser::find()->where(['event_id'=>$this->id])->andWhere(['user_id'=>Yii::$app->user->id])->one();
        if ($eu){
            $roles = EventUserRole::find()->where(['event_user_id'=>$eu->id])->all();
            $return = [];
            foreach ($roles as $r)
            {
                $return[] = ['role'=>$r->userEventRole->name, 'start'=>$r->working->start_time, 'end'=>$r->working->end_time];
            }
            return $return;
        }
        return null;
    }

    public function getScheduleArray()
    {
        $schedules = EventSchedule::find()->where(['event_id'=>$this->id])->asArray()->all();
        
        return $schedules ;
    }

    public function getEventPaid()
    {
        $value= 0;
        foreach ($this->invoices as $invoice)
            $value +=$invoice->alreadypaid;
        return $value;
    }

    public function getEventFV()
    {
        $value= 0;
        foreach ($this->invoices as $invoice)
            $value +=$invoice->total;
        return $value;
    }

    public function changeVehicleDates($old)
    {
        foreach ($this->eventVehicles as $ev)
        {
            $ev->start_time = $this->getTimeStart();
            $ev->end_time = $this->getTimeEnd();
            $ev->save();
            $hours = EventVehicleWorkingHours::find()->where(['event_id'=>$this->id])->andWhere(['vehicle_id'=>$ev->vehicle_id])->all();
            foreach ($hours as $workingTime)
            {
                        if ($old->event_start != $this->event_start || $old->event_end != $this->event_end) {
                            if ($workingTime->start_time == $old->event_start && $workingTime->end_time == $old->event_end) {
                                $overlap = EventVehicleWorkingHours::find()->where(['<>', 'event_id', $this->id])->andWhere(['vehicle_id'=>$ev->vehicle_id])->andWhere(['<', 'start_time', $this->event_end])->andWhere(['>', 'end_time', $this->event_start])->count();
                                if (!$overlap)
                                {
                                    $workingTime->start_time = $this->event_start;
                                    $workingTime->end_time = $this->event_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                                
                            }
                        }
                        if ($old->montage_start != $this->montage_start || $old->montage_end != $this->montage_end) {
                            if ($workingTime->start_time == $old->montage_start && $workingTime->end_time == $old->montage_end) {
                                $overlap = EventVehicleWorkingHours::find()->where(['<>', 'event_id', $this->id])->andWhere(['vehicle_id'=>$ev->vehicle_id])->andWhere(['<', 'start_time', $this->montage_end])->andWhere(['>', 'end_time', $this->montage_start])->count();
                                if (!$overlap)
                                {
                                    $workingTime->start_time = $this->montage_start;
                                    $workingTime->end_time = $this->montage_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }
                        if ($old->disassembly_start != $this->disassembly_start || $old->disassembly_end != $this->disassembly_end) {
                            if ($workingTime->start_time == $old->disassembly_start && $workingTime->end_time == $old->disassembly_end) {
                                $overlap = EventVehicleWorkingHours::find()->where(['<>', 'event_id', $this->id])->andWhere(['vehicle_id'=>$ev->vehicle_id])->andWhere(['<', 'start_time', $this->disassembly_end])->andWhere(['>', 'end_time', $this->disassembly_start])->count();
                                if (!$overlap)
                                {
                                    $workingTime->start_time = $this->disassembly_start;
                                    $workingTime->end_time = $this->disassembly_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }
                        if ($old->packing_start != $this->packing_start || $old->packing_end != $this->packing_end) {
                            if ($workingTime->start_time == $old->packing_start && $workingTime->end_time == $old->packing_end) {
                                $overlap = EventVehicleWorkingHours::find()->where(['<>', 'event_id', $this->id])->andWhere(['vehicle_id'=>$ev->vehicle_id])->andWhere(['<', 'start_time', $this->packing_end])->andWhere(['>', 'end_time', $this->packing_start])->count();
                                if (!$overlap)
                                {
                                    $workingTime->start_time = $this->packing_start;
                                    $workingTime->end_time = $this->packing_end;
                                    $workingTime->save();
                                }else{
                                    $workingTime->delete();
                                }
                            }
                        }
            }
        }
    }

    public function getStatusIcon()
    {
        if (isset($this->eventStatut)){
            return "<i class='fa ".$this->eventStatut->icon."'></i>";
        }
        return "";

    }

    public function getStatusBorder()
    {
        if (isset($this->eventStatut)){
            return $this->eventStatut->border."px solid ".$this->eventStatut->color;
        }
        return "";

    }

    public function getBlocks($type)
    {
        if (isset($this->eventStatut))
        {
            if ($type=="event")
            {
                if ($this->eventStatut->blocks_event)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="revert")
            {
                if ($this->eventStatut->blocks_status_revert)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="cost")
            {
                if ($this->eventStatut->blocks_costs)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="gear")
            {
                if ($this->eventStatut->blocks_gear)
                {
                    return true;
                }else{
                    return false;
                }
            }
            if ($type=="working")
            {
                if ($this->eventStatut->blocks_working_times)
                {
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    public function getForEvents()
    {
        $event_ids = ArrayHelper::map(Task::find()->where(['event_id'=>$this->id])->andWhere(['>', 'for_event', 0])->asArray()->all(), 'for_event', 'for_event');
        $arr = [0=>"wybierz..."];
        return $arr+ArrayHelper::map(Event::find()->where(['id'=>$event_ids])->asArray()->all(), 'id', 'name');
    }

    public function getTaskList()
    {
        return ArrayHelper::map(\common\models\Task::find()->where(['event_id'=>$this->id])->asArray()->all(), 'id', 'title');
    }

    public function getTypeList()
    {
        if (Yii::$app->params['companyID']=="admin")
        {
            return [2=>Yii::t('app', 'Nowa funkcjonalność'), 3=>Yii::t('app', 'Błąd'), 4=>Yii::t('app', 'Pytanie'), 5=>Yii::t('app', 'Poprawka'), 6=>Yii::t('app', 'Nowa instancja'), 7=>Yii::t('app', 'Usługa płatna')];
        }else{
            //return [1=>Yii::t('app', 'Wydarzenie'), 2=>Yii::t('app', 'Produkcja'), 3=>Yii::t('app', 'Praca biurowa'), 4=>Yii::t('app', 'Praca grafika'), 5=>Yii::t('app', 'Prace magazynowe'), 6=>Yii::t('app', 'Wizja lokalna')];
            return ArrayHelper::map(EventModel::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
        }
        
    }

    public function getEventTypeList()
    {

            return ArrayHelper::map(EventType::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
        
    }

    public static function getStatusList($status=null, $type=1)
    {
        $permission = Yii::$app->user->can('eventEventBlockStatus');
        if ($status)
        {
            $s = \common\models\EventStatut::findOne($status);
            if (($s->blocks_status_revert)&&(!$permission))
            {
                $list = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1])->andWhere(['>=', 'position', $s->position])->orderBy(['position'=>SORT_ASC])->asArray()->all();
                
            }else{
                $list = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all();
            }
        }else{
            $list = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all();
        }
        
        $l_array = [];
        foreach ($list as $l)
        {
            if ($l['permission_users'])
            {
                $users = explode(";", $l['permission_users']);
                if (in_array(Yii::$app->user->id, $users))
                {
                    $l_array[$l['id']] = $l['name'];
                }

            }else{
                $l_array[$l['id']] = $l['name'];
            }
        }
        return $l_array;
    }

    public static function getStatusList2($status=null, $type=1)
    {
        $permission = Yii::$app->user->can('eventEventBlockStatus');
        if ($status)
        {
            $s = \common\models\EventStatut::findOne($status);
            if (($s->blocks_status_revert)&&(!$permission))
            {
                $list = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1])->andWhere(['>=', 'position', $s->position])->orderBy(['position'=>SORT_ASC])->asArray()->all();
                return $list;
            }
        }
        $list = \common\models\EventStatut::find()->where(['type'=>$type, 'active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all();
        $l_array = [];
        foreach ($list as $l)
        {
            $del = "";
            if (($l['delete_gear'])&&(!$l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację sprzętu)');
            }
            if (($l['delete_gear'])&&($l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację sprzętu i ekipy)');
            }
            if ((!$l['delete_gear'])&&($l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację ekipy)');
            }
            if ($l['permission_users'])
            {
                $users = explode(";", $l['permission_users']);
                if (in_array(Yii::$app->user->id, $users))
                {
                    $l_array[$l['id']] = $l['name'].$del;

                }

            }else{
                            $del = "";
            if (($l['delete_gear'])&&(!$l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację sprzętu)');
            }
            if (($l['delete_gear'])&&($l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację sprzętu i ekipy)');
            }
            if ((!$l['delete_gear'])&&($l['delete_crew']))
            {
                $del = Yii::t('app', ' (Usuwa rezerwację ekipy)');
            }
                $l_array[$l['id']] = $l['name'].$del;
            }

        }
        return $l_array;
    }


    public function getStatusButton()
    {
        $status = EventStatut::findOne($this->status);
        if ($status)
            return '<span class="label label-primary" style="background-color:'.$status->color.';"">'.$status->name.'</span>';
        else
            return "-";
    }

    public function getStatusHistory()
    {
        return Note::find()->where(['event_id'=>$this->id])->andWhere(['type2'=>'eventStatusChanged'])->all();
    }

    public function resolveConflictsAfterDelete()
    {
        $resolved = [];
        foreach ($this->eventGears as $gear)
        {
            $ids = ArrayHelper::map(EventGear::find()->where(['gear_id'=>$gear->gear_id])->andWhere(['>', 'end_time', $gear->start_time])->andWhere(['<>','event_id', $this->id])->andWhere(['<', 'start_time', $gear->end_time])->all(), 'event_id', 'event_id');
            $conflicts = EventConflict::find()->where(['event_id'=>$ids])->andWhere(['gear_id'=>$gear->gear_id])->andWhere(['resolved'=>0])->all();
            $gear->delete();
            
            foreach ($conflicts as $conflict)
            {
                $available = $gear->gear->getAvailabe($conflict->event->getTimeStart(), $conflict->event->getTimeEnd())-$gear->gear->getInService();
                if ($available>=$conflict->quantity)
                {
                    $oldQuantity=0;
                    $currentConlict = 0;
                    $egm = EventGear::findOne(['gear_id'=>$conflict->gear_id, 'event_id'=>$conflict->event_id]);
                    if ($egm)
                        $oldQuantity = $egm->quantity;
                    $quantity = $oldQuantity+$conflict->quantity;
                    Event::assignGear($conflict->event_id, $conflict->gear_id, $quantity);
                    $resolved[] = ['event'=>$conflict->event, 'gear'=>$conflict->gear];
                }
            }
            
        }
        return $resolved;
    }

    public function getParentEvent()
    {
        $et = EventTask::find()->where(['event_id'=>$this->id])->one();
        if ($et)
        {
            $task = Task::findOne($et->task_id);
            return $task->event;
        }else{
            return false;
        }
    }

    public function updateParentExpense()
    {
        //szukamy koszty
        $et = EventTask::find()->where(['event_id'=>$this->id])->one();
        if ($et)
        {
            $task = Task::findOne($et->task_id);
            $cost_expense = EventExpense::find()->where(['task_id'=>$et->task_id])->andWhere(['info'=>"[".Yii::t('app', 'Koszty')."]"])->one();
            if (!$cost_expense)
            {
                
                $cost_expense = new EventExpense();
                $cost_expense->event_id = $task->event_id;
                $cost_expense->task_id = $task->id;
                $cost_expense->amount = 0;
                $cost_expense->sections = ["Scenografia"];
                $cost_expense->name = "[".Yii::t('app', 'Koszty')."] ".$task->title;
                $cost_expense->info = "[".Yii::t('app', 'Koszty')."]";
                $cost_expense->save();


            }
            $working_expense = EventExpense::find()->where(['task_id'=>$et->task_id])->andWhere(['info'=>"[".Yii::t('app', 'Obsługa')."]"])->one();
            if (!$working_expense)
            {
                $working_expense = new EventExpense();
                $working_expense->event_id = $task->event_id;
                $working_expense->task_id = $task->id;
                $working_expense->amount = 0;
                $working_expense->sections = ["Scenografia"];
                $working_expense->name = "[".Yii::t('app', 'Obsługa')."] ".$task->title;
                $working_expense->info = "[".Yii::t('app', 'Obsługa')."]";
                $working_expense->save();
            }
            $cost_expense->amount = $this->getTotalExpenseCost();
            $cost_expense->save();
            $working_expense->amount = $this->getTotalWorkingCost();
            $working_expense->save();
        }
    }

    public function getTotalExpenseCost()
    {
        $total = 0;
        foreach ($this->eventExpenses as $e)
        {
            $total +=$e->amount;
        }
        return $total;
    }

    public function getTotalWorkingCost()
    {
        $total = 0;
        foreach ($this->getWorkingTimeSummaryAll() as $item)
        {
            $total +=$item['sum'];
        }
        return $total;
    }

    public function getExtraFields()
    {
        $fields = EventFieldSetting::find()->where(['active'=>1])->all();
        $return = [];
        foreach ($fields as $f)
        {
            $r = [];
            $r['name'] = $f->name;
            $r['type'] = $f->type;
            $r['id'] = $f->id;
            $ef = EventField::find()->where(['event_id'=>$this->id])->andWhere(['event_field_setting_id'=>$f->id])->one();
            if (!$ef)
            {
                $ef = new EventField();
                $ef->event_id = $this->id;
                $ef->event_field_setting_id = $f->id;
            }
            $r['field'] = $ef;
            $return[] = $r;
        }
        return $return;
    }

    public function getFieldValue($field)
    {
        $ef = EventField::find()->where(['event_id'=>$this->id])->andWhere(['event_field_setting_id'=>$field])->one();
        if (!$ef)
            return null;
        if ($ef->eventFieldSetting->type==1)
        {
            return $ef->value_int;
        }
        return $ef->value_text;
    }

    public static function getBlackFieldList()
    {
        return [
        Yii::t('app', 'Status') => Yii::t('app', 'Status'),
        Yii::t('app', 'Typ') => Yii::t('app', 'Typ'),
        Yii::t('app', 'Rodzaj') => Yii::t('app', 'Rodzaj'),
        Yii::t('app', 'Poziom') => Yii::t('app', 'Poziom'),
        Yii::t('app', 'Termin') => Yii::t('app', 'Termin'),
        Yii::t('app', 'PM') => Yii::t('app', 'PM'),
        Yii::t('app', 'Uczestnicy') => Yii::t('app', 'Uczestnicy'),
        Yii::t('app', 'Flota') => Yii::t('app', 'Flota'),
        Yii::t('app', 'Miejsce') => Yii::t('app', 'Miejsce'),
        Yii::t('app', 'Klient') => Yii::t('app', 'Klient'),
        Yii::t('app', 'Opis') => Yii::t('app', 'Opis')
        ];
    }

    public function getUserGProvision($user_id)
    {
        $total = [];
        $teams = ArrayHelper::map(TeamUser::find()->where(['user_id'=>$user_id])->asArray()->all(), 'team_id', 'team_id');
        $groups = ProvisionGroup::find()->where(['team_id'=>$teams])->andWhere(['is_pm'=>0])->andWhere(['add_to_users'=>1])->orderBy(['level'=>SORT_ASC])->all();
        foreach ($groups as $group)
        {
            $count = TeamUser::find()->where(['team_id'=>$group->team_id])->count();
            $value = $this->getGProvisions(false)[$group->id]['value']/$count;
            $total[] =['name'=>$group->name, 'value'=> $value];
        }
        if ($user_id == $this->manager_id)
        {
            $groups = ProvisionGroup::find()->where(['is_pm'=>1])->andWhere(['add_to_users'=>1])->orderBy(['level'=>SORT_ASC])->all();
        foreach ($groups as $group)
        {
            $value = $this->getGProvisions(false)[$group->id]['value'];
            $total[] =['name'=>$group->name, 'value'=> $value];
        }
        }
        return $total;
    }

    public function getGProvisions($i=true)
    {
        if ($i)
            EventProvisionValue::deleteAll(['event_id'=>$this->id]);
        $groups = EventProvisionGroup::find()->where(['event_id'=>$this->id])->orderBy(['level'=>SORT_ASC])->all();
        $values = $this->getEventValueAll();

        $profits = $this->getEventProfits();

        $costs = $this->getEventCosts();
        //exit;
        $level = 0;
        $total_level = [];

        foreach ($profits as $key => $val)
        {
            $total_level[$key] = 0;
        }
        foreach ($profits as $key => $val)
        {
            if(!isset($values[$key]))
                $values[$key] = 0;
            if(!isset($costs[$key]))
                $costs[$key] = 0;
        }

        $provisions = [];
        foreach ($groups as $group)
        {
            if ((!$group->customer_group_id)||($this->customer->isInGroup($group->customer_group_id))){
            $section_value = [];
            foreach ($profits as $key => $val)
            {
                    $section_value[$key] = 0;
            }
            if ($group->level!=$level)
            {
                $level = $group->level;
                foreach ($profits as $key => $val)
                {
                    $profits[$key] = $val-$total_level[$key];
                    $total_level[$key] = 0;
                }
            }
            if ($group->is_pm)
            {
                $provisions[$group->provision_group_id] = $this->getProvisionPM($profits);
                $provisions[$group->provision_group_id]['group'] = $group;
                foreach ($profits as $k => $val)
                {
                    $total_level[$key] += $provisions[$group->provision_group_id]['sections'][$k];
                    $section_value[$key] += $provisions[$group->provision_group_id]['sections'][$k];
                }
            }else{

            if (!$group->main_only)
            {
                //prowizja jednakowa dla wszystkich sekcji
                if ($group->type==1)
                {
                    //prowizja od zysku
                    $value = round($profits[Yii::t('app', 'Suma')]*$group->provision/100,2);
                    foreach ($profits as $key => $val)
                    {
                        $total_level[$key] += round($profits[$key]*$group->provision/100,2);
                        $section_value[$key] += round($profits[$key]*$group->provision/100,2);
                    }
                }
                if ($group->type==2){
                    //prowizja od wartości
                    $value = round($values[Yii::t('app', 'Suma')]*$group->provision/100,2);
                    foreach ($profits as $key => $val)
                    {
                        $total_level[$key] += round($values[$key]*$group->provision/100,2);
                        $section_value[$key] += round($values[$key]*$group->provision/100,2);
                    }
                }
                if ($group->type==3){
                    //prowizja od wartości
                    $value = round($costs[Yii::t('app', 'Suma')]*$group->provision/100,2);
                    foreach ($profits as $key => $val)
                    {
                        $total_level[$key] += round($costs[$key]*$group->provision/100,2);
                        $section_value[$key] += round($costs[$key]*$group->provision/100,2);
                    }
                }
            }else{
                //inna prowizja dla niektórych sekcji
                if ($group->type==1)
                {
                    //prowizja od zysku
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = EventProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['event_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += round($profits[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($profits[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($profits[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==2){
                                    $value += round($values[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($values[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($values[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==3){
                                    $value += round($costs[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($costs[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($costs[$key]*$pgs->value/100,2);
                                }                                
                            }else{
                                $value += round($profits[$key]*$group->provision/100,2);
                                $total_level[$key] += round($profits[$key]*$group->provision/100,2);
                                $section_value[$key] += round($profits[$key]*$group->provision/100,2);
                            }
                        }

                        
                    }
                }
                if ($group->type==2){
                    //prowizja od zysku
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = EventProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['event_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += round($profits[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($profits[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($profits[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==2){
                                    $value += round($values[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($values[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($values[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==3){
                                    $value += round($costs[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($costs[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($costs[$key]*$pgs->value/100,2);
                                }
                            }else{
                                $value += round($values[$key]*$group->provision/100,2);
                                $total_level[$key] += round($values[$key]*$group->provision/100,2);
                                $section_value[$key] += round($values[$key]*$group->provision/100,2);
                            }
                        }

                        
                    }                    
                }
                if ($group->type==3){
                    //prowizja od zysku
                    $value = 0;
                    foreach ($profits as $key => $val)
                    {
                        //szukamy czy jest nietypowy procent
                        if ($key!=Yii::t('app', 'Suma')){
                            $pgs = EventProvisionGroupProvision::find()->where(['section'=>$key])->andWhere(['event_provision_group_id'=>$group->id])->one();
                            if ($pgs)
                            {
                                if ($pgs->type==1)
                                {
                                    $value += round($profits[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($profits[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($profits[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==2){
                                    $value += round($values[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($values[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($values[$key]*$pgs->value/100,2);
                                }
                                if ($pgs->type==3){
                                    $value += round($costs[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($costs[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($costs[$key]*$pgs->value/100,2);
                                }
                            }else{
                                    $value += round($costs[$key]*$pgs->value/100,2);
                                    $total_level[$key] += round($costs[$key]*$pgs->value/100,2);
                                    $section_value[$key] += round($costs[$key]*$pgs->value/100,2);
                            }
                        }

                        
                    }                    
                }
                $total_level[Yii::t('app', 'Suma')] +=$value;
                $section_value[Yii::t('app', 'Suma')] =$value;

            }
            $provisions[$group->provision_group_id]['group'] = $group;
            $provisions[$group->provision_group_id]['value'] = $value;
            $provisions[$group->provision_group_id]['sections'] = $section_value;
        }

        }
        }
        if ($i)
        {
        foreach ($provisions as $key=>$p)
        {
            foreach ($p['sections'] as $s => $v)
            {
                $epv = new EventProvisionValue();
                $epv->event_id = $this->id;
                $epv->section = $s;
                $epv->value = $v; 
                $epv->provision_group_id = $key;
                $epv->save();
            }

        }
        }

        return $provisions;
    }

    public function getTotalProductionCrewCost()
    {
        $expenses = EventExpense::find()->where(['event_id'=>$this->id])->andWhere(['like', 'name', '['.Yii::t('app', 'Obsługa').']'])->all();
        $sum = 0;
        foreach ($expenses as $expense)
        {
            $sum +=$expense->amount;
        }
        return $sum;
    }

    public function getTotalProductionBudget()
    {
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['is_accepted'=>1])->asArray()->all(), 'id', 'id');
        $offers = Offer::find()->where(['event_id'=>$this->id])->andWhere(['status'=>$statuts])->all();
        if (!$offers)
            return 0;
        $sum = 0;
        foreach ($offers as $o)
        {
            $sum +=$o->getTotalProductionBudget();
        }
        return $sum;
    }

    public function getOfferVehicles()
    {
        $statuts = ArrayHelper::map(OfferStatut::find()->where(['visible_in_planning'=>1])->asArray()->all(), 'id', 'id');
        $ids = ArrayHelper::map($this->getOffers()
            ->where([
                'in', 'status', $statuts,
            ])
            ->asArray()->all(), 'id', 'id');
        return OfferVehicle::find()->where(['offer_id'=>$ids])->orderBy(['type'=>SORT_ASC])->all();
    }

    public function getPayingDateList()
    {
        $a = [];
        for($i=date("Y")+1; $i>=2016; $i--)
        {

            $a[$i."-0"] = date("Y", mktime(0, 0, 0, 1, 1, $i))." - cały";
            for ($j=12; $j>0; $j--)
            {
                $a[date("Y-m-d", mktime(0, 0, 0, $j, 1, $i))] = date("Y-m", mktime(0, 0, 0, $j, 1, $i));
            }
            

        }
        return $a;
    }

    public function getAdditionalStatut($id, $name = false)
    {
        $s = EventASResult::findOne(['event_id'=>$this->id, 'event_additional_statut_id'=>$id]);
        if ($s)
        {
            if ($name)
                return "<i class='fa ".$s->eventAdditionalStatutName->icon."'></i> ".$s->eventAdditionalStatutName->name;
            else
                return $s->event_additional_statut_name_id;
        }else
        {
            return "";
        }
    }

    public function copySchedules($offer)
    {
        foreach ($offer->offerSchedules as $s)
        {
            $schedule = new EventSchedule();
            $schedule->attributes = $s->attributes;
            $schedule->event_id = $this->id;
            $schedule->save();
        }
    }

    public function deleteEmptyPacklists()
    {
        foreach ($this->packlists as $p)
        {
            if (!$p->main)
            {
                if ($p->color=="#555555")
                {
                    if ((!$p->packlistGears)&&(!$p->packlistOuterGears)&&(!$p->packlistExtras))
                    {
                        $p->delete();
                    }
                }
            }
        }
    }

    public static function assignGearToPacklist($packlist, $itemId, $quantity, $startTime, $endTime, $oldQuantity=0)
    {
        $packlist = Packlist::findOne($packlist);


        $model = PacklistGear::findOne(['packlist_id'=>$packlist->id, 'gear_id'=>$itemId]);

        
        $old_quantity = 0;
        if (!$model)
        {
            $model = new PacklistGear();
            $old_quantity = 0;
        }else{
            $old_quantity= $model->quantity;
            EventConflict::deleteAll(['packlist_gear_id'=>$model->id]);
        }
        $model->packlist_id = $packlist->id;
        $model->gear_id = intval($itemId);
        $model->quantity = intval($quantity);
        $model->start_time = $startTime;
        $model->end_time = $endTime;
        if ($model->gear->type==2)
        {
            return $model->save();
        }
        if ($model->gear->type==3)
        {
            $available = $model->gear->quantity+$old_quantity;
            if ( $available >= $quantity)
            {
                $model->gear->quantity = $model->gear->quantity+$old_quantity-$quantity;
                $model->gear->save();
                return $model->save();
            }else{
                return false;
            }
        }
        if ($oldQuantity)
        {
            $quantity +=$old_quantity;
            $model->quantity = $quantity;
        }
        $available = $model->gear->getAvailabe($startTime, $endTime)+$old_quantity;
        $available = $available-$model->gear->getInService();
        if ( $available >= $quantity)
        {
            if (!$model->save())
            {
                return false;
            }
            return true;
        }
        else
        {
            return false;
        }

    }
    public static function assignGearToPacklistMax($packlist, $itemId, $quantity, $startTime, $endTime, $oldQuantity=0)
    {
        $packlist = Packlist::findOne($packlist);

        $model = PacklistGear::findOne(['packlist_id'=>$packlist->id, 'gear_id'=>$itemId]);
        $old_quantity = 0;
        if (!$model)
        {
            $model = new PacklistGear();
            $old_quantity = 0;
        }else{
            $old_quantity= $model->quantity;
        }
        $model->packlist_id = $packlist->id;
        $model->gear_id = intval($itemId);
        $model->quantity = intval($quantity);
        $model->start_time = $startTime;
        $model->end_time = $endTime;
        if ($model->gear->type==2)
        {
            return $model->save();
        }
        if ($model->gear->type==3)
        {
            $available = $model->gear->quantity+$old_quantity;
            if ( $available >= $quantity)
            {
                $model->gear->quantity = $model->gear->quantity+$old_quantity-$quantity;
                $model->gear->save();
                return $model->save();
            }else{
                return false;
            }
        }
        if ($oldQuantity)
        {
            $quantity +=$old_quantity;
            $model->quantity = $quantity;
        }
        $available = $model->gear->getAvailabe($startTime, $endTime)+$old_quantity;
        $available = $available-$model->gear->getInService();
        if ( $available >= $quantity)
        {
            if (!$model->save())
            {
                return false;
            }
            return true;
        }
        else
        {
            $model->quantity = $available;
            $model->save();
            $model2 = new EventConflict();
            $model2->event_id = $packlist->event_id;
            $model2->gear_id = $model->gear->id;
            $model2->packlist_gear_id = $model->id;
            $model2->quantity = $quantity-$model->quantity;
            $model2->added = $model->quantity;
            $model2->save();
        }

    }

    public function recalculateGears()
    {
        $gears = EventGear::find()->where(['event_id'=>$this->id])->all();
        foreach ($gears as $gear)
        {
            $gear->updateCount();
        }
        $extra - EventExtraItem::find()->where(['event_id'=>$this->id])->all();
         foreach ($extra as $gear)
        {
            $gear->updateCount();
        }  
                $outer - EventOuterGear::find()->where(['event_id'=>$this->id])->all();
         foreach ($outer as $gear)
        {
            $gear->updateCount();
        }      
    }

    public function checkItems($items, $packlist)
    {

        $newItems = [];
        foreach ($items as $id=>$quantity)
        {
            $gearItem = GearItem::findOne($id);
            if ($gearItem->gear->no_items)
            {
                $q = EventGearOutcomed::find()->where(['gear_id'=>$gearItem->gear_id, 'event_id'=>$this->id, 'packlist_id'=>$packlist])->one();
                if ($q)
                {
                    if ($q->quantity>$quantity)
                    {
                        $newItems[$id] = $quantity;
                    }else{
                        if ($q->quantity>0)
                            $newItems[$id] = $q->quantity;
                    }
                }
            }   else{
                if (($gearItem->event_id==$this->id)&&($gearItem->packlist_id==$packlist))
                {
                    $newItems[$id] = $quantity;
                }
            }
        }
        return $newItems;
    }

    public function checkGroups($groups, $packlist)
    {
        $newgroups = [];
        foreach ($groups as $id=>$value)
        {
            $items = GearItem::find()->where(['group_id'=>$id])->all();
            $return = true;
            foreach ($items as $gearItem)
            {
                    if (($gearItem->event_id==$this->id)&&($gearItem->packlist_id==$packlist))
                    {
                        $newgroups[$id] = $value;
                    }else{
                        $return = false;
                    }
                
            }
            if ($return)
                $newgroups[$id] = $value;
        }
        return $newgroups;
        
    }
}

