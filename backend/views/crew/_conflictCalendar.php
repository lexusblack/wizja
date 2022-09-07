<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;



?>
<div style="height:100%; width:100%; background-color:white; margin-top:10px;">
<div id="conflict-user-calendar-<?=$user->id?>">


</div>
</div>
<?php
if ($schedule)
{
  $now = $schedule->start_time;
}else{
  $now = $event->getTimeStart();
}
$events = $user->getEventsConflictedArray($event, $schedule);
$this->registerJs("$('#conflict-user-calendar-".$user->id."').fullCalendar({
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

<?php $url = Url::to(['crew/get-conflicted', 'event_id'=>$event->id, 'user_id'=>$user->id]); ?>
<script type="text/javascript">
function reloadCalendar()
{
    $('#conflict-user-calendar-<?=$user->id?>').fullCalendar('removeEvents');
    $.post('<?=$url?>', {}, function(response){
                                $('#conflict-user-calendar-<?=$user->id?>').fullCalendar('addEventSource',response);
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
        $.post('/admin/crew/change-dates2?user_id=<?=$user->id?>&event_id='+event.id+"&role_id=<?=$role_id?>", data, function(response){
                        if (response.success==0)
                        {
                            toastr.error("<?=Yii::t('app', 'Nie można zapisać. Pracownik zajęty w tym czasie.')?>");
                        }else{
                             toastr.success(response.message);
                             CrewChanged = true;
                             $('#conflict-user-calendar-<?=$user->id?>').parent().parent().hide();
                            
                        }
                    });
    } else{
    data ={'start':event.start.format(), 'end':event.end.format(), 'type':type};
    $.post('/admin/crew/change-dates?user_id=<?=$user->id?>&event_id='+event.id, data, function(response){
                        if (response.success==0)
                        {
                            toastr.error("<?=Yii::t('app', 'Pracownik zajęty w tym terminie')?>");
                            revertFunc();
                        }else{
                             toastr.success('<?=Yii::t('app', 'Zmiana zapisana')?>');
                             CrewChanged = true;
                            
                        }
                    });      
    }

}
</script>
