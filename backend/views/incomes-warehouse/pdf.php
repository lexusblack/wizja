<?php
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */
 $customer = $model->getForWhoIsOutcome2();
 $event = $model->getEventFor();
        $content = "
        
            <div style='text-align: center; width: 100%; font-size: 30px; padding-top: 30px;'>".Yii::t('app', 'Przyjęcie do magazynu')."</div>
        

            <div class='half_page '>
                ".Yii::t('app', 'Numer').": PZ-".$model->id."<br/>
            ".Yii::t('app', 'Wydanie na').": ".$model->getForWhoIsOutcome()."<br/>";
                if ($event)
                {
                    $content .=substr($event->getTimeStart(), 0, 16)." - ".substr($event->getTimeEnd(), 0, 16)."<br/>";
                }
                
                $content .=Yii::t('app', 'PM').": ".$model->getPM()."
            </div>
            <div class='half_page '>
            ".Yii::t('app', 'Klient').":<br/>".
            $customer->name."<br/>".$customer->address."<br/>".$customer->zip." ".$customer->city."<br/>";
            
            if ($event)
            {
                if ($event->contact_id)
                {
                    $content .=$event->contact->first_name." ".$event->contact->last_name;
                    if ($event->contact->phone)
                    {
                        $content .=Yii::t('app', " tel. ").$event->contact->phone;
                    }
                }
            }
           $content .= "</div>
            
            <div class='whole_page'>
                ".Yii::t('app', 'Sprzęt').":
                <hr>
            </div>
            
            ".$model->getGearWithLeaveWarehouse()."
            
            <div class='whole_page'>
                ".Yii::t('app', 'Uwagi').":
                <hr>
            </div>
            <div>".nl2br($model->getComments())."</div>
            

            <div class='half_page '>
                <hr>
                ".Yii::t('app', 'Data').": ".$model->datetime."<br/>".Yii::t('app', 'Przyjął').": ".$user->displayLabel."
            </div>
            
            <div class='half_page '>
                <hr>
                ".Yii::t('app', 'Oddał').": 
            </div>
            
        ";

echo $content;
?>


