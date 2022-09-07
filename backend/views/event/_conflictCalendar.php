<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use yii\bootstrap\Html;
$user = Yii::$app->user;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Modal;

\common\assets\FullcalendarSchedulerAsset::register($this);
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Edycja rezerwacji')."</h4>",
    'id' => 'conflict_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<div style="height:100% width:100%; background-color:white; margin-top:10px;">
<div id="conflict-calendar-<?=$conflict->id?>">


</div>
</div>
<?php
$array = $conflict->getEventsConflictedArray();
$this->registerJs("$('#conflict-calendar-".$conflict->id."').fullCalendar({
      schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
      editable: true,
      aspectRatio: 1.8,
      now: '".$conflict->event->getTimeStart()."',
      height:'auto',
      firstDay:".date('w', strtotime($conflict->event->getTimeStart())).",
      slotDuration:'01:00',
      slotWidth: 12,
      slotLabelInterval:'02:00',
      scrollTime: '00:00', // undo default 6am scrollTime
            customButtons: {
        prevButton: {
          text: '-1',
          click: function() {
            $('#conflict-calendar-".$conflict->id."').fullCalendar('incrementDate', { days: -1 });
          }
        },
        nextButton: {
          text: '+1',
          click: function(calendar) {
            $('#conflict-calendar-".$conflict->id."').fullCalendar('incrementDate', { days: +1 });
          }
        }
      },
      header: {
        left: 'today prev,next prevButton nextButton prevDay',
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
      resourceLabelText: 'Konflikty',
      resources:".json_encode($array['resources']).",
      events:".json_encode($array['events']).",
        eventResize: function(event, delta, revertFunc) {

            changeEventGearDates(event, revertFunc);

        },
        eventDrop: function(event, delta, revertFunc) {
            changeEventGearDates(event, revertFunc);
        },
    eventAfterAllRender: function() {
        /*$('.fc-time-area tr .fc-event-container').css('height', '24px');
        $('.fc-time-area tr .fc-event-container a').css('height', '22px');
        $('.fc-time-area tr .fc-event-container a .fc-content').css('height', '22px');*/
        $('.fc-time-area tr:first-child .fc-event-container').css('height', '34px');
        $('.fc-time-area tr:first-child .fc-event-container a').css('height', '32px');
    },
  eventClick: function(calEvent, jsEvent, view) {
    showEditModal(calEvent.id, calEvent.resourceId);

  },
    eventRender:function(event, element) {
        var content = '';

        if(event.packing !== undefined){
            content += event.packing;
         } 
        if(event.montage !== undefined){
           content += event.montage;
        } 
        if(event.event !== undefined){
           content += event.event;
        } 
        if(event.disassembly !== undefined){
           content += event.disassembly;
        }
        element.find('.fc-content').html(element.find('.fc-content').html()+'<div><div class\"timeline-packing\">'+content+'</div></div>')
        } 
    });
    reloadCalendar();
    "); 

$this->registerCss('

.fc-resource-area{ width:100px;}
.fc-time-area .fc-slats .fc-minor{border-style:solid; border-color:#eee !important;}
.fc-unthemed td.fc-major{border-style:solid; border-color:#ddd !important;}
.fc-content{padding:0px;}
.fc-event, .fc-agenda .fc-event-time, .fc-event a{padding:0px !important;}
.fc-ltr .fc-timeline-event .fc-title{padding-left:20px; padding-top:8px;}
'); 
$url = Url::to(['warehouse/get-conflicted', 'conflict_id'=>$conflict->id]);
$checkurl = Url::to(['warehouse/check-conflict', 'conflict_id'=>$conflict->id]);
$showEditConflict = Url::to(['warehouse/edit-conflict']);
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
function reloadCalendar()
{
    $('#conflict-calendar-<?=$conflict->id?>').fullCalendar('removeEvents');
    $.post('<?=$url?>', {}, function(response){
                                $('#conflict-calendar-<?=$conflict->id?>').fullCalendar('addEventSource',response);
                            });
}

function changeEventGearDates(event, revertFunc)
{
    if (event.resourceId.charAt(0)=='e')
        {
            type = 'event';
        }else{
            type = 'rent';
        }  
    data ={'start':event.start.format(), 'end':event.end.format(), 'type':type};
    $.post('/admin/warehouse/change-dates?gear_id=<?=$conflict->gear_id?>&event_id='+event.id, data, function(response){
                        if (response.success==0)
                        {
                            alert("<?=Yii::t('app', 'Zmiana spowodowałaby powstanie kolejnego konfliktu. Cofam.')?>");
                            revertFunc();
                        }else{
                             reloadCalendar();
                                $.post('<?=$checkurl?>', data, function(response){
                                    if (response.success==1)
                                    {
                                        showResolveConflict();
                                    }
                                     if (response.success==2)
                                    {
                                        showResolvePartial();
                                    }                                   
                                });
                            
                        }
                    });
}

    function showResolveConflict()
    {
        swal({
            title: "<?=Yii::t('app', 'Konflikt może zostać rozwiązany. Czy zarezerwować brakujący sprzęt na wydarzenie: ').str_replace($conflict->event->name, '"', '')."?"?>",
            icon:"info",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              $.post("<?=Url::to(['warehouse/resolve-conflict', 'conflict_id'=>$conflict->id]);?>", {}, function(response){
                                    if (response.success)
                                    {
                                        location.reload();
                                    }               
              });
              break;       
          }
        });
    }

      function showResolvePartial()
    {
        swal({
            title: "<?=Yii::t('app', 'Zmiana spowodowała, że zwoniło się trochę sprzętu. Czy zarezerwować go na wydarzenie: ').str_replace($conflict->event->name, '"', '')."?"?>",
            icon:"info",
          buttons: {
            cancel: "<?=Yii::t('app', 'Nie')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              $.post("<?=Url::to(['warehouse/resolve-conflict-partial', 'conflict_id'=>$conflict->id]);?>", {}, function(response){
                                    if (response.success)
                                    {
                                        location.reload();
                                    }               
              });
              break;       
          }
        });
    }

    function showEditModal(id, type)
    {
        if (type=='b')
        {
            type = 'event';
        }else{
            type = 'rent';
        }
        var modal = $("#conflict_modal");
        modal.find(".modalContent").empty();
        $.post("<?=$showEditConflict?>", {'gear_id':<?=$conflict->gear_id?>, 'event_id':id, 'conflict_id':<?=$conflict->id?>, 'type':type}, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
        });
    }
</script>


