<?php

namespace common\models;
use yii\helpers\Html;

use Yii;
use \common\models\base\Note as BaseNote;

/**
 * This is the model class for table "note".
 */
class Note extends BaseNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'event_id', 'rent_id', 'project_id', 'customer_id', 'note_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe']
        ]);
    }

    public function createNote($type, $type2, $model, $id)
    {
        $note = new Note();
        $note->type = $type;
        $note->type2 = $type2;
        $note->auto = 1;
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $note->datetime = date('Y-m-d H:i:s');
        $note->user_id = Yii::$app->user->id;
        if ($type2 =='offerToProject')
        {
            //ok
            $note->text = Yii::t('app', 'Do projektu ').$model->project->name.Yii::t('app', ' podpięto ofertę ').$model->name.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;
            $note->permission = 'menuOffers';
        }
        if ($type2 =='offerFromProject')
        {
            //ok
            $note->text = Yii::t('app', 'Z projektu ').$model->project->name.Yii::t('app', ' odpięto ofertę ').$model->name.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;
            $note->permission = 'menuOffers';
        }
        if ($type2 =='eventToProject')
        {
            //ok
            $note->text = Yii::t('app', 'Do projektu ').$model->project->name.Yii::t('app', ' podpięto wydarzenie ').$model->name.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;
            $note->permission = 'menuProjects';
        }
        if ($type2 =='eventFromProject')
        {
            //ok
            $note->text = Yii::t('app', 'Z projektu ').Yii::t('app', ' odpięto wydarzenie ').$model->name.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$id]);
            $note->project_id = $id;
            $note->permission = 'menuProjects';

        }
        if ($type2 =='projectUserAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do projektu ').$model->project->name.Yii::t('app', ' dodano użytkownika ').$model->user->displayLabel.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;
            $note->permission = 'menuProjects';

        }
        if ($type2 =='projectUserDeleted')
        {
            //ok
            $note->text = Yii::t('app', 'Z projektu ').$model->project->name.Yii::t('app', ' usunięto użytkownika ').$model->user->displayLabel.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;
            $note->permission = 'menuProjects';

        }
        if ($type2 =='projectAddFile')
        {
            //ok
            $note->text = Yii::t('app', 'Dodano do projektu plik.').$model->name.".".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);;
            $note->project_id = $id;
            $note->permission = 'menuProjects';

        }
        if ($type2=='eventScheduleChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Zmieniono harmonogram wydarzenia ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-calendar']);
            $note->event_id = $id;  
            $note->permission = 'eventEventEditEye';        
        }
        if ($type2=='eventCreate')
        {
            $note->text = Yii::t('app', 'Dodano wydarzenie ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id]);
            $note->event_id = $id;    
            $note->permission = 'eventEventEditEye';        
        }
        if ($type2=='eventDelete')
        {
            //ok
            $note->text = Yii::t('app', 'Usunięto wydarzenie ').$model->name;
            $note->event_id = $id;     
            $note->permission = 'eventEventEditEye';       
        }
        if ($type2=='eventDescriptionChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Zmieniono opis wydarzenia ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-calendar']);
            $note->event_id = $id;    
            $note->permission = 'eventEventEditEye';        
        } 
        if ($type2=='eventOuterGear')
        {
            //ok
            $note->text = Yii::t('app', 'Wybrano wypożyczalnię dla sprzętu ').$model->outerGear->name.Yii::t('app', ' w wydarzeniu ').$model->event->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$id, '#'=>'eventTabs-dd3-tab1']);
            $note->event_id = $id;    
            $note->permission = 'eventEventEditEyeOuterGear';       
        }   
        if ($type2=='eventGearChanged')
        {
            $note->text = Yii::t('app', 'Zmieniono rezerwację sprzętu w wydarzeniu ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-gear']);
            $note->event_id = $id; 
            $note->permission = 'eventEventEditEyeGear';         
        } 
        if ($type2=='rentGearChanged')
        {
            $note->text = Yii::t('app', 'Zmieniono rezerwację sprzętu w wypożyczeniu ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->id, '#'=>'tab-gear']);
            $note->rent_id = $id;           
        }
         if ($type2=='eventConflictCreated')
        {
            //ok
            $note->text = Yii::t('app', 'W wydarzeniu ').$model->event->name.Yii::t('app', ' jest konflikt na sprzęt ').$model->gear->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'eventTabs-dd3-tab2']);
            $note->event_id = $id;    
            $note->permission = 'eventEventEditEyeOuterGear';        
        }
         if ($type2=='eventConflictResolved')
        {
            //ok
            $note->text = Yii::t('app', 'Rozwiązano konflikt w wydarzeniu ').$model->event->name.Yii::t('app', ' na sprzęt ').$model->gear->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-gear']);
            $note->event_id = $id;  
            $note->permission = 'eventEventEditEyeOuterGear';          
        } 
         if ($type2=='eventConflictPartialResolved')
        {
            //ok
            $note->text = Yii::t('app', 'Częściowo rozwiązano konflikt w wydarzeniu ').$model->event->name.Yii::t('app', ' na sprzęt ').$model->gear->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-gear']);
            $note->event_id = $id;  
            $note->permission = 'eventEventEditEyeOuterGear';          
        }
         if ($type2=='eventAttachmentAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-attachment']);
            $note->event_id = $id;  
            $note->permission = 'eventEventEditEyeAttachment';         
        } 
         if ($type2=='eventOfferAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano ofertę ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$model->id]);
            $note->event_id = $id;   
            $note->permission = 'menuOffers';        
        }
         if ($type2=='eventOfferChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Oferta ').$model->name.Yii::t('app', ' w wydarzeniu ').$model->event->name.Yii::t('app', ' zmieniła status na ').$model->statusLabel.". ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$model->id]);
            $note->event_id = $id;   
            $note->permission = 'menuOffers';        
        } 
        if ($type2=='eventCrewAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano pracownika ').$model->user->displayLabel.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$id, '#'=>'tab-crew']);
            $note->event_id = $id;   
            $note->permission = 'eventsEventEditEyeCrew';       
        }  
        if ($type2=='eventCarAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano pojazd ').$model->vehicle->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event_id, '#'=>'tab-vehicle']);
            $note->event_id = $id;  
            $note->permission = 'eventsEventEditEyeVehicles';       
        } 
        if ($type2=='eventCostAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano koszt ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-finances']);
            $note->event_id = $id;  
            $note->permission = 'eventsEventEditEyeFinance';        
        } 
        if ($type2=='eventCostDeleted')
        {
            //ok
            $note->text = Yii::t('app', 'Z wydarzenia ').$model->event->name.Yii::t('app', ' usunięto koszt ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-finances']);
            $note->event_id = $id;  
            $note->permission = 'eventsEventEditEyeFinance';         
        } 
        if ($type2=='eventInvoiceAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->owner->name.Yii::t('app', ' dodano fakturę ').$model->fullnumber.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->owner->id, '#'=>'tab-finances']);
            $note->event_id = $id;      
            $note->permission = 'eventsEventEditEyeFinance';     
        } 
        if ($type2=='eventExpenseAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->name.Yii::t('app', ' dodano fakturę kosztową ').". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-finances']);
            $note->event_id = $id;  
            $note->permission = 'eventsEventEditEyeFinance';         
        }
        if ($type2=='eventTaskAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' dodano zadanie ').$model->title.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event->id, '#'=>'tab-task']);
            $note->event_id = $id;   
            $note->permission = 'menuEvents';     
        }
        if ($type2=='eventTaskDone')
        {
            //ok
            $note->text = Yii::t('app', 'Zadanie ').$model->name.Yii::t('app', ' w wydarzeniu ').$model->event->name.Yii::t('app', ' zostało wykonane').". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event_id, '#'=>'tab-task']);
            $note->event_id = $id;  
            $note->permission = 'menuEvents';          
        }   
        if ($type2=='eventTaskComment')
        {
            //ok
            $note->text = Yii::t('app', 'Do zadania ').$model->task->title.Yii::t('app', ' w wydarzeniu ').$model->task->event->name.Yii::t('app', ' dodano komentarz: ').$model->text.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->task->event_id, '#'=>'tab-task']);
            $note->event_id = $id;       
            $note->permission = 'menuEvents';     
        }
        if ($type2=='eventWorkingHours')
        {
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->name.Yii::t('app', ' dodano godziny pracy').". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-finances']);
            $note->event_id = $id;   
            $note->permission = 'usersPayments';       
        } 
        if ($type2=='eventWorkingHoursChange')
        {
            $note->text = Yii::t('app', 'W wydarzeniu ').$model->name.Yii::t('app', ' zmieniono godziny pracy').". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-finances']);
            $note->event_id = $id;  
            $note->permission = 'usersPayments';          
        }
        if ($type2=='eventWorkingHoursDelete')
        {
            $note->text = Yii::t('app', 'W wydarzeniu ').$model->name.Yii::t('app', ' usunięto godziny pracy').". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id, '#'=>'tab-finances']);
            $note->event_id = $id;   
            $note->permission = 'usersPayments';         
        }  
        if ($type2=='offerAdded')
        {
            //ok
            $note->text = Yii::t('app', 'Stworzono ofertę ').$model->name.Yii::t('app', ' dla klienta ').$model->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$model->id]);
            $note->offer_id = $id;  
            $note->customer_id = $model->customer_id;    
            $note->permission = 'menuOffers';     
        } 
        if ($type2=='offerSend')
        {
            //ok
            $note->text = Yii::t('app', 'Wysłano ofertę ').$model->name.Yii::t('app', ' do klienta ').$model->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$model->id]);
            $note->offer_id = $id;  
            $note->rent_id = $model->rent_id;  
            $note->event_id = $model->event_id; 
            $note->customer_id = $model->customer_id;  
            $note->permission = 'menuOffers';    
        }
         if ($type2=='offerChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Oferta ').$model->name.Yii::t('app', ' zmieniła status').". ".Html::a(Yii::t('app', 'Zobacz'), ['/offer/default/view', 'id'=>$model->id]);
            $note->offer_id = $id; 
            $note->rent_id = $model->rent_id;  
            $note->event_id = $model->event_id;
            $note->customer_id = $model->customer_id;  
            $note->permission = 'menuOffers';         
        } 
         if ($type2=='offerDeleted')
        {
            //ok
            $note->text = Yii::t('app', 'Oferta ').$model->name.Yii::t('app', ' została usunięta').". ";
            $note->offer_id = $id; 
            $note->rent_id = $model->rent_id;  
            $note->event_id = $model->event_id;
            $note->customer_id = $model->customer_id;  
            $note->permission = 'menuOffers';         
        } 
        if ($type2=='taskAdded')
        {
            //ok
            $text = Yii::t('app', 'Dodano zadanie ').$model->title.". ".Yii::t('app', 'Zadanie przypisane do: ');
            $first = true;
            foreach ($model->users as $user)
            {
                if (!$first)
                    $text.=", ";
                $text .=$user->displayLabel;

                $first = false;
            }
            $text.=". ";
            if ($model->rent_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z wypożyczeniem: ').$model->rent->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->rent_id, '#'=>'tab-task']);
                $note->permission = 'eventRents'; 
            }
            if ($model->customer_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z klientem: ').$model->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$model->customer_id, '#'=>'tab-task']);
                $note->permission = 'menuClients';
            }
            if ($model->project_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z projektem: ').$model->project->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->project_id]);
                $note->permission = 'menuProjects';
            }
            $note->text = $text;
        }
        if ($type2=='taskDone')
        {
            //ok
            $note->text = Yii::t('app', 'Zadanie ').$model->title.Yii::t('app', ' zostało wykonane').". ";
            if ($model->rent_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z wypożyczeniem: ').$model->rent->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->rent_id, '#'=>'tab-task']);
                $note->permission = 'eventRents'; 

            }
            if ($model->customer_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z klientem: ').$model->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$model->customer_id, '#'=>'tab-task']);
                $note->permission = 'menuClients';
            }
            if ($model->project_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z projektem: ').$model->project->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->project_id]);
                $note->permission = 'menuProjects';
            }
            $note->rent_id = $model->rent_id;  
            $note->customer_id = $model->customer_id; 
            $note->project_id = $model->project_id; 
            $note->text = $text;
        }
        if ($type2=='taskComment')
        {
            //ok
            $text = Yii::t('app', 'Do zadania ').$model->task->title.Yii::t('app', ' dodano komentarz: ').$model->text.". ";
            if ($model->task->rent_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z wypożyczeniem: ').$model->task->rent->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->task->rent_id, '#'=>'tab-task']);
                $note->permission = 'eventRents';
            }
            if ($model->task->customer_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z klientem: ').$model->task->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$model->task->customer_id, '#'=>'tab-task']);
                $note->permission = 'menuClients';
            }
            if ($model->task->project_id)
            {
                $text.= Yii::t('app', 'Zadanie powiązane z projektem: ').$model->task->project->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->task->project_id]);
                $note->permission = 'menuProjects';
            }
            $note->rent_id = $model->task->rent_id;  
            $note->customer_id = $model->task->customer_id; 
            $note->project_id = $model->task->project_id; 
            $note->text = $text;
        }  
        if ($type2=='gearService')
        {
            //ok
            $text = Yii::t('app', 'Sprzęt')." ".$model->gearItem->gear->name.Yii::t('app', ' został wysłany na serwis.').$model->description.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear-service/view', 'id'=>$model->id]); 
            $note->text = $text;
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2=='gearQuantityChanged')
        {
            //ok
            $text = Yii::t('app', 'Sprzęt')." ".$model->name.Yii::t('app', ' zmienił liczbę sztuk na ').$model->quantity.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear/view', 'id'=>$model->id]); 
            $note->text = $text;
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2=='gearAdded')
        {
            //ok
            $text = Yii::t('app', 'Sprzęt')." ".$model->name.Yii::t('app', ' został dodany do systemu z liczbą sztuk ').$model->quantity.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear/view', 'id'=>$model->id]); 
            $note->text = $text;
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2=='gearItemAdded')
        {
            //ok
            $text = Yii::t('app', 'Egzemplarz sprzętu')." ".$model->gear->name.Yii::t('app', ' został dodany do systemu z numerem ').$model->number.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear-item/view', 'id'=>$model->id]); 
            $note->text = $text;
            $note->gear_id =  $model->gear_id; 
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2=='gearItemDeleted')
        {
            //ok
            $text = Yii::t('app', 'Egzemplarz sprzętu')." ".$model->gear->name.Yii::t('app', ' o numerze')." ".$model->number.Yii::t('app', ' został usuniety z systemu').$model->description.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear-item/view', 'id'=>$model->id]); 
            $note->text = $text;
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2=='rentScheduleChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Zmieniono termin wypożyczenia ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->id]);
            $note->rent_id = $id;  
            $note->permission = 'eventRents';         
        }
        if ($type2=='rentCreate')
        {
            //ok
            $note->text = Yii::t('app', 'Dodano wypożyczenie ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->id]);
            $note->rent_id = $id; 
            $note->permission = 'eventRents';          
        }
        if ($type2=='rentDelete')
        {
            //ok
            $note->text = Yii::t('app', 'Usunięto wypożyczenie ').$model->name;
            $note->rent_id = $id;  
            $note->permission = 'eventRents';         
        }
        if ($type2=='rentStatus')
        {
            //ok
            $note->text = Yii::t('app', 'Wypożyczenie ').$model->name.Yii::t('app', ' zmieniło status na ').$model->statusLabel.". ".Html::a(Yii::t('app', 'Zobacz'), ['/rent/view', 'id'=>$model->id]);
            $note->rent_id = $id;   
            $note->permission = 'eventRents';        
        }
        if ($type2=='eventStatusChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Wydarzenie ').$model->name.Yii::t('app', ' zmieniło status na ').$model->eventStatut->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->id]);
            $note->event_id = $id;   
            $note->permission = 'eventEventEditEye';        
        }
        if ($type2=='workerMonth')
        {
            
            if ($model['status'])
                $note->text = Yii::t('app', 'Miesiąc ').$model['month'].".".$model['year'].Yii::t('app', ' rozliczony').". ".Html::a(Yii::t('app', 'Zobacz'), ['/settlement/user/show', 'userId'=>$id, 'year'=>$model['year'], 'month'=>$model['month']]);
            else
                $note->text = Yii::t('app', 'Miesiąc ').$model['month'].".".$model['year'].Yii::t('app', ' cofnięcie statusu rozliczony').". ".Html::a(Yii::t('app', 'Zobacz'), ['/settlement/user/show', 'userId'=>$id, 'year'=>$model['year'], 'month'=>$model['month']]);
            $note->worker_id = $id;  
            $note->permission = 'usersPayments';          
        }
        if ($type2=="vehicleService")
        {
            //ok
            $note->text = Yii::t('app', 'Pojazd ').$model->name.Yii::t('app', ' został wysłany na serwis' ).". ".Html::a(Yii::t('app', 'Zobacz'), ['/vehicle/view', 'id'=>$model->id]); 
            $note->permission = 'menuFleet';           
        }
        if ($type2=="vehicleServiceBack")
        {
            //ok
            $note->text = Yii::t('app', 'Pojazd ').$model->name.Yii::t('app', ' wrócił z serwisu' ).". ".Html::a(Yii::t('app', 'Zobacz'), ['/vehicle/view', 'id'=>$model->id]);  
            $note->permission = 'menuFleet';           
        }
        if ($type2 =="customerAttachmentAdded")
        {
            $note->text = Yii::t('app', 'Do klienta ').$model->customer->name.Yii::t('app', ' dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$model->customer_id, '#'=>'tab-attachment']);
            $note->customer_id = $id;  
            $note->permission = 'menuClients';           
        }
        if ($type2 =="gearAttachmentAdded")
        {
            $note->text = Yii::t('app', 'Do sprzętu ').$model->gear->name.Yii::t('app', ' dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear/view', 'id'=>$model->gear_id, '#'=>'tab-attachment']);
            $note->permission = 'gearOurWarehouse';
        }
        if ($type2 =="expenseAttachmentAdded")
        {
            $note->text = Yii::t('app', 'Do faktury ').$model->expense->number.Yii::t('app', ' dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/finances/expense/update', 'id'=>$model->expense_id, '#'=>'tab-attachment']);
            $note->permission = 'menuInvoices'; 
        }
        if ($type2 =="invoiceAttachmentAdded")
        {
            $note->text = Yii::t('app', 'Do wydarzenia ').$model->event->name.Yii::t('app', ' w finansach dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/event/view', 'id'=>$model->event_id, '#'=>'tab-finances']);
            $note->event_id = $id;
            $note->permission = 'menuInvoices'; 
        }
        if ($type2 =="customerAdded")
        {
            $note->text = Yii::t('app', 'Dodano nowego klienta ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$model->id]);
            $note->customer_id = $id; 
            $note->permission = 'menuClients';           
        }
        if ($type2 =="invoiceAdded")
        {
            $note->text = Yii::t('app', 'Wystawiono nową fakturę ').$model->fullnumber.". ".Html::a(Yii::t('app', 'Zobacz'), ['/finances/invoice/view', 'id'=>$model->id]);
            $note->customer_id = $id;  
            $note->permission = 'menuInvoices';       
        }
        if ($type2 =="customerDeleted")
        {
            $note->text = Yii::t('app', 'Usunięto klienta ').$model->name.". ";
            $note->permission = 'menuClients';
        }
        if ($type2 =="contactAdded")
        {
            $note->text = Yii::t('app', 'Dodano nowy kontakt ').$model->displayLabel.Yii::t('app', ' w kliencie ').$model->customer->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$id]);
            $note->customer_id = $id;   
            $note->permission = 'menuClients';         
        }
        if ($type2 =="customerDiscountAdded")
        {
            $note->text = Yii::t('app', 'Klientowi ').$model->customer->name.Yii::t('app', ' dodano rabat').". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$id]);
            $note->customer_id = $id;  
            $note->permission = 'menuClients';          
        }
        if ($type2 =="customerDiscountChange")
        {
            $note->text = Yii::t('app', 'Klientowi ').$model->name.Yii::t('app', ' zmieniono rabat').". ".Html::a(Yii::t('app', 'Zobacz'), ['/customer/view', 'id'=>$id]);
            $note->customer_id = $id; 
            $note->permission = 'menuClients';           
        }
        if ($type2 =="vacationAdded")
        {
            $note->text = Yii::t('app', 'Dodano wniosek urlopowy na okres od ').substr($model->start_date, 0, 10).Yii::t('app', ' do ').substr($model->end_date,0,10).". ".Html::a(Yii::t('app', 'Zobacz'), ['/vacation/view', 'id'=>$model->id]);
            $note->worker_id = $id;  
            $note->permission = 'eventVacationsView';         
        }
        if ($type2 =="vacationAccepted")
        {
            $note->text = Yii::t('app', 'Wniosek urlopowy na okres od ').$model->start_date.Yii::t('app', ' do ').$model->end_date.Yii::t('app', ' pracownika ').$model->user->displayLabel.Yii::t('app', ' został zaakceptowany').". ".Html::a(Yii::t('app', 'Zobacz'), ['/vacation/view', 'id'=>$model->id]);
            $note->worker_id = $id;    
            $note->permission = 'eventVacationsView';         
        }
        if ($type2 =="vacationRejected")
        {
            $note->text = Yii::t('app', 'Wniosek urlopowy na okres od ').$model->start_date.Yii::t('app', ' do ').$model->end_date.Yii::t('app', ' pracownika ').$model->user->displayLabel.Yii::t('app', ' został odrzucony').". ".Html::a(Yii::t('app', 'Zobacz'), ['/vacation/view', 'id'=>$model->id]);
            $note->worker_id = $id;   
            $note->permission = 'eventVacationsView';          
        }
        if ($type2 =="vacationDeleted")
        {
            $note->text = Yii::t('app', 'Wniosek urlopowy na okres od ').substr($model->start_date, 0, 10).Yii::t('app', ' do ').substr($model->end_date,0,10).Yii::t('app', ' pracownika ').$model->user->displayLabel.Yii::t('app', ' został usunięty').". ";
            $note->worker_id = $id;   
            $note->permission = 'eventVacationsView';          
        }
        if ($type2 =="meetingAdded")
        {
            $text = Yii::t('app', 'Utworzono spotkanie ').$model->name.Yii::t('app', ' w terminie ').substr($model->start_time, 0, 16).Yii::t('app', ' Zaproszeni uczestnicy: ');
            $first = true;            
            foreach ($model->users as $user)
            {
                if (!$first)
                    $text.=", ";
                $text.=$user->displayLabel;
                $first = false;
            }
            if (isset($model->contact))
            {
                 if (!$first)
                    $text.=", ";
                $text.=$model->contact->displayLabel;               
            }
            $text .=". ".Html::a(Yii::t('app', 'Zobacz'), ['/meeting/view', 'id'=>$model->id]);
            $note->text = $text;
            $note->customer_id = $id;  
            $note->permission = 'eventsMeetings';       
        }
        if ($type2 =="meetingScheduleChange")
        {
            $text = Yii::t('app', 'Zmieniono termin spotkania ').$model->name.Yii::t('app', ' na ').substr($model->start_time, 0, 16);
            $text .=". ".Html::a(Yii::t('app', 'Zobacz'), ['/meeting/view', 'id'=>$model->id]);
            $note->text = $text;
            $note->customer_id = $id; 
            $note->permission = 'eventsMeetings';           
        }
        if ($type2 =="meetingDeleted")
        {
            $text = Yii::t('app', 'Usunięto spotkanie ').$model->name.Yii::t('app', ' odbywające się w terminie ').substr($model->start_time, 0, 16);
            $text .=". ";
            $note->text = $text;
            $note->customer_id = $id;   
            $note->permission = 'eventsMeetings';         
        }
        if ($type2 =="meetingDeleted")
        {
            $text = Yii::t('app', 'Wysłano zamówienie na sprzęt zewnętrzny do ').$model->customer->name;
            $text .=". ".Html::a(Yii::t('app', 'Zobacz'), ['/order/view', 'id'=>$model->id]);;
            $note->text = $text;
            $note->customer_id = $id;    
            $note->permission = 'eventsMeetings';        
        }
        if ($type2=='projectScheduleChanged')
        {
            //ok
            $note->text = Yii::t('app', 'Zmieniono harmonogram projektu ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id;   
            $note->permission = 'menuProjects';        
        }
        if ($type2=='projectCreate')
        {
            //ok
            $note->text = Yii::t('app', 'Dodano projekt ').$model->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/project/view', 'id'=>$model->id]);
            $note->project_id = $id; 
            $note->permission = 'menuProjects';          
        }
        if ($type2=='projectDelete')
        {
            //ok
            $note->text = Yii::t('app', 'Usunięto project ').$model->name;
            $note->permission = 'menuProjects';
        }
if ($type2=='gearOutcomed')
        {
            if ($model->gear->gear->no_items)
            {
                $note->text = Yii::t('app', 'Wydano sprzęt ').$model->gear->gear->name." ".Yii::t('app', 'sztuk')." ".$model->gear_quantity.Yii::t('app', ' na ')." ".$model->outcome->getForWhoIsOutcome().". ".Html::a(Yii::t('app', 'Zobacz'), ['/outcomes-warehouse/view', 'id'=>$model->outcome_id]);
            }else{
                $note->text = Yii::t('app', 'Wydano egzemplarz sprzętu ').$model->gear->gear->name." ".Yii::t('app', 'numer')." ".$model->gear->number.Yii::t('app', ' na ')." ".$model->outcome->getForWhoIsOutcome().". ".Html::a(Yii::t('app', 'Zobacz'), ['/outcomes-warehouse/view', 'id'=>$model->outcome_id]);
            }
            
            $note->gear_id = $model->gear->gear_id; 
            $note->in_feed = 0;
            $note->permission = 'gearOurWarehouse';    
            }      
        
        if ($type2=='gearIncomed')
        {
            //ok
            if ($model->gear->gear->no_items)
            {
                $note->text = Yii::t('app', 'Przyjęto sprzęt ').$model->gear->gear->name." ".Yii::t('app', 'sztuk')." ".$model->quantity.Yii::t('app', ' na ')." ".$model->income->getEventFor()->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/incomes-warehouse/view', 'id'=>$model->income_id]);
            }else{
                $note->text = Yii::t('app', 'Przyjęto egzemplarz sprzętu ').$model->gear->gear->name." ".Yii::t('app', 'numer')." ".$model->gear->number.Yii::t('app', ' na ')." ".$model->income->getEventFor()->name.". ".Html::a(Yii::t('app', 'Zobacz'), ['/incomes-warehouse/view', 'id'=>$model->income_id]);
            }
            
            $note->gear_id =  $model->gear->gear_id; 
            $note->in_feed = 0;
            $note->permission = 'gearOurWarehouse';          
        }
                if ($type2 =="gearAttachmentAdded")
        {
            $note->text = Yii::t('app', 'Do sprzętu ').$model->gear->name.Yii::t('app', ' dodano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear/view', 'id'=>$model->gear_id, '#'=>'tab-attachment']);
            $note->permission = 'gearOurWarehouse';
            $note->gear_id = $id; 
            $note->in_feed = 0;
        }  
                        if ($type2 =="gearAttachmentDeleted")
        {
            $note->text = Yii::t('app', 'Ze sprzętu ').$model->gear->name.Yii::t('app', ' skasowano załącznik ').$model->filename.". ".Html::a(Yii::t('app', 'Zobacz'), ['/gear/view', 'id'=>$model->gear_id, '#'=>'tab-attachment']);
            $note->permission = 'gearOurWarehouse';
            $note->gear_id = $id; 
            $note->in_feed = 0;
        }     
        $note->save();
        return $note->id;
       // return true;

    }
	
}
