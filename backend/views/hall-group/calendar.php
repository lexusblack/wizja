<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use yii\bootstrap\Html;
$user = Yii::$app->user;
use yii\helpers\Json;
use yii\helpers\Url;
$now = date("Y-m-d");
\common\assets\FullcalendarSchedulerAsset::register($this);
$this->title = Yii::t('app', 'Kalendarz dostępności powierzchni');
$this->params['breadcrumbs'][] = $this->title;
  ?>
<div class="row">
<siv class="col-md-12">
<div class="ibox">
<div class="ibox-content">
<h1><?=$this->title?></h1>
<p><?=Html::a(Yii::t('app', 'Dodaj rezerwację'), ['/event/create'], ['class'=>'btn btn-primary'])?></p>
<div id="hall-calendar" class="calendar-div">
</div>
</div>
</div>
</div>

<?php
$eventUrl = Url::to(['/event/view']);
$saveDatesUrl = Url::to(['/hall-group/book-dates']);
$viewHallUrl = Url::to(['/hall-group/view']);
$this->registerJs("$('#hall-calendar').fullCalendar({
      schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
      editable: true,
      aspectRatio: 1.8,
      now: '".$now."',
      height:'auto',
      firstDay:".date('w', strtotime($now)).",
      slotDuration:'01:00',
      resourceAreaWidth:'200px',
      slotWidth: 12,
      slotLabelInterval:'02:00',
      scrollTime: '00:00', // undo default 6am scrollTime
      customButtons: {
        prevButton: {
          text: '-1',
          click: function() {
            $('#hall-calendar').fullCalendar('incrementDate', { days: -1 });
          }
        },
        nextButton: {
          text: '+1',
          click: function(calendar) {
            $('#hall-calendar').fullCalendar('incrementDate', { days: +1 });
          }
        }
      },
      header: {
        left: 'today prev,next prevButton nextButton prevDay',
        center: 'title',
        right: 'timelineFourDays, timeline7Days, timelineFourteenDays, timeline30Days'
      },
      defaultView: 'timelineFourteenDays',
        views: {
        timelineFourDays: {
          type: 'timeline',
          duration: { days: 4 }
        },
        timeline7Days: {
          type: 'timeline',
          duration: { days: 7 }
        },
        timelineFourteenDays: {
          type: 'timeline',
          duration: { days: 14 }
        },
        timeline30Days: {
          type: 'timeline',
          duration: { days: 30 }
        }
      },
      resourceLabelText: 'Dostępność',
      resources: ".json_encode($halls).",
      events: '".Url::to(['/hall-group/calendar-array'])."',
              eventResize: function(event, delta, revertFunc) {

            changeEventHallDates(event, revertFunc);

        },
        eventDrop: function(event, delta, revertFunc) {
            changeEventHallDates(event, revertFunc);
        },
    eventAfterAllRender: function() {
      $('.fc-body .fc-resource-area').find('tr').each(function(){
        var label = $(this);
        var id = $(this).data('resource-id');
        if ($(this).find('.fc-cell-text').length)
        {
            var text = $(this).find('.fc-cell-text').html();
            label.find('.fc-cell-content').each(
            function(){
              $(this).empty().html('<a href=\"".$viewHallUrl."?id='+id+'\" target=\"_blank\">'+text+'</a>'); 
            });
        }

        
      });
    },
  eventClick: function(calEvent, jsEvent, view) {
    //showEditModal(calEvent.id, calEvent.resourceId);
      var url = '".$eventUrl."?id='+calEvent.event_id; 
      window.open(url, '_blank');


  },
    eventRender:function(event, element) {

        } 
    });



    "); 

$this->registerCss('

.fc-resource-area{ width:100px;}
.fc-time-area .fc-slats .fc-minor{border-style:solid; border-color:#eee !important;}
.fc-unthemed td.fc-major{border-style:solid; border-color:#ddd !important;}
.fc-content{padding:0px;}
.fc-event, .fc-agenda .fc-event-time, .fc-event a{padding:0px !important;}
.fc-ltr .fc-timeline-event .fc-title{padding-left:20px; padding-top:8px;}
'); 
?>

<script type="text/javascript">
  
  function changeEventHallDates(event, revertFunc)
{
  
    data ={'start':event.start.format(), 'end':event.end.format()};
    $.post('<?=$saveDatesUrl?>?id='+event.book_id, data, function(response){
                             toastr.success('<?=Yii::t('app', 'Zmiana zapisana!')?>');
                             $('#hall-calendar').fullCalendar('refetchEvents');

                            
                        
                    });
}
</script>



