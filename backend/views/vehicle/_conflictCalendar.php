<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;



?>
<div style="height:100%; width:100%; background-color:white; margin-top:10px;">
<div id="conflict-vehicle-calendar-<?=$vehicle->id?>">


</div>
</div>
<?php
if ($schedule)
{
  $now = $schedule->start_time;
}else{
  $now = $event->getTimeStart();
}
$events = $vehicle->getEventsConflictedArray($event, $schedule);
$this->registerJs("$('#conflict-vehicle-calendar-".$vehicle->id."').fullCalendar({
      schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
      editable: true,
      aspectRatio: 1.8,
      now: '".$now."',
      height:250,
      firstDay:".date('w', strtotime($now)).",
      slotDuration:'01:00',
      slotWidth: 12,
      slotLabelInterval:'02:00',
      scrollTime: '00:00', // undo default 6am scrollTime
      header: {
        left: 'today prev,next',
        center: 'title',
        right: 'timelineFourDays, timelineWeek, timelineFourteenDays, timelineMonth'
      },
      defaultView: 'timelineWeek',
        views: {
        timelineFourDays: {
          type: 'timeline',
          duration: { days: 4 }
        },
        timelineFourteenDays: {
          type: 'timeline',
          duration: { days: 14 }
        }
      },
      resourceLabelText: 'Wydarzenia',
      resources: ".json_encode($events['res']).",
      events:".json_encode($events['events']).",
        eventResize: function(event, delta, revertFunc) {

            changeEventDates(event, revertFunc);

        },
        eventDrop: function(event, delta, revertFunc) {
            changeEventDates(event, revertFunc);
        },
    eventAfterAllRender: function() {

    },
});
    "); 

$this->registerCss('

.fc-resource-area{ width:200px;}
.fc-time-area .fc-slats .fc-minor{border-style:solid; border-color:#eee !important;}
.fc-unthemed td.fc-major{border-style:solid; border-color:#ddd !important;}
.fc-content{padding:0px;}
.fc-event, .fc-agenda .fc-event-time, .fc-event a{padding:0px !important;}
.fc-ltr .fc-timeline-event .fc-title{padding-left:5px; padding-top:8px;}
'); 
?>

<?php $url = Url::to(['vehicle/get-conflicted', 'event_id'=>$event->id, 'vehicle_id'=>$vehicle->id]); ?>
<script type="text/javascript">
function reloadCalendar()
{
    $('#conflict-vehicle-calendar-<?=$vehicle->id?>').fullCalendar('removeEvents');
    $.post('<?=$url?>', {}, function(response){
                                $('#conflict-vehicle-calendar-<?=$vehicle->id?>').fullCalendar('addEventSource',response);
                            });
}

function changeEventDates(event, revertFunc)
{
    if (event.resourceId=='b')
        {
            
            type = 'vacation';
        }else{
            type = 'event';
        } 
    if (event.resourceId=='a')
    {
        data ={'start':event.start.format(), 'end':event.end.format(), 'type':type};
        $.post('/admin/vehicle/change-dates2?vehicle_id=<?=$vehicle->id?>&event_id='+event.id+"&role_id=<?=$role_id?>", data, function(response){
                        if (response.success==0)
                        {
                            toastr.error("<?=Yii::t('app', 'Nie można zapisać. Pracownik zajęty w tym czasie.')?>");
                        }else{
                             toastr.success(response.message);
                             vehicleChanged = true;
                             $('#conflict-vehicle-calendar-<?=$vehicle->id?>').parent().parent().hide();
                            
                        }
                    });
    } else{
    data ={'start':event.start.format(), 'end':event.end.format(), 'type':type};
    $.post('/admin/vehicle/change-dates?vehicle_id=<?=$vehicle->id?>&event_id='+event.id, data, function(response){
                        if (response.success==0)
                        {
                            toastr.error("<?=Yii::t('app', 'Pracownik zajęty w tym terminie')?>");
                            revertFunc();
                        }else{
                             toastr.success('<?=Yii::t('app', 'Zmiana zapisana')?>');
                             vehicleChanged = true;
                            
                        }
                    });      
    }

}
</script>
