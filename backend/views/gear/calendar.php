<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */
use yii\bootstrap\Html;
$user = Yii::$app->user;
use yii\helpers\Json;
use yii\helpers\Url;

\common\assets\FullcalendarSchedulerAsset::register($this);
if ($start)
  $now = $start;
else
  $now = date("Y-m-d");

if (!$keys)
  $models = [$model];
else{
  $models = $model;
  ?>
  <div class="row" style="margin-top:20px;">
  <div class="col-sm-3" style="text-align:center;">
  <?=Yii::t('app', 'Nawigacja grupowa:')?>
  </div>
    <div class="col-sm-4" style="text-align:center;">
    <?=Html::a("<", '#', ['class'=>'btn btn-primary btn-sm all-left'])?>
    <?=Html::a("-1", '#', ['class'=>'btn btn-primary btn-sm one-left'])?>
    <?=Html::a("+1", '#', ['class'=>'btn btn-primary btn-sm one-right'])?>
    <?=Html::a(">", '#', ['class'=>'btn btn-primary btn-sm all-right'])?>
    </div>
    <div class="col-sm-5" style="text-align:center;">
    <?=Html::a("4 dni", '#', ['class'=>'btn btn-primary btn-sm 4-days'])?>
    <?=Html::a("7 dni", '#', ['class'=>'btn btn-primary btn-sm 7-days'])?>
    <?=Html::a("14 dni", '#', ['class'=>'btn btn-primary btn-sm 14-days'])?>
    <?=Html::a("30 dni", '#', ['class'=>'btn btn-primary btn-sm 30-days'])?>
    </div>
  </div>
  <?php
  $this->registerJs("
    $('.one-left').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('incrementDate', { days: -1 });
    });
    $('.one-right').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('incrementDate', { days: +1 });
    }); 
    $('.all-left').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('prev');
    });
    $('.all-right').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('next');
    });

    $('.4-days').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('changeView', 'timelineFourDays');
    }); 
    $('.7-days').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('changeView', 'timeline7Days');
    }); 
    $('.14-days').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('changeView', 'timelineFourteenDays');
    }); 
    $('.30-days').click(function(e){
      e.preventDefault();
      $('.calendar-div').fullCalendar('changeView', 'timeline30Days');
    }); 
    ");
}

?>
<?php foreach ($models as $model){ ?>
<div style=" width:100%; background-color:white; margin-top:10px;">
<h3><?=$model->name?>
<?php if ((!$connected)&&(!$keys)){ ?>
<?=Html::a(Yii::t('app', 'Pokaż dla podobnych'), ['#'], ['class'=>'btn btn-sm btn-primary show-all-calendars pull-right'])?>
<?php } ?>
</h3>
<div id="gear-calendar<?=$model->id?>" class="calendar-div">


</div>
</div>
<?php
$r = $model->getGearCalendarArrayRes($start, $end);
$this->registerJs("$('#gear-calendar".$model->id."').fullCalendar({
      schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
      editable: true,
      aspectRatio: 1.8,
      now: '".$now."',
      height:'auto',
      firstDay:".date('w', strtotime($now)).",
      slotDuration:'12:00',
      slotWidth: 12,
      slotLabelInterval:'12:00',
      scrollTime: '00:00', // undo default 6am scrollTime
      customButtons: {
        prevButton: {
          text: '-1',
          click: function() {
            $('#gear-calendar".$model->id."').fullCalendar('incrementDate', { days: -1 });
          }
        },
        nextButton: {
          text: '+1',
          click: function(calendar) {
            $('#gear-calendar".$model->id."').fullCalendar('incrementDate', { days: +1 });
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
      resources: ".json_encode($r['resources']).",
      events: '".Url::to(['/gear/calendar-array', 'id'=>$model->id])."',
    eventAfterAllRender: function() {
        $('.fc-time-area tr:first-child .fc-event-container').css('height', '34px');
        $('.fc-time-area tr:first-child .fc-event-container a').css('height', '32px');
    },
  eventClick: function(calEvent, jsEvent, view) {
    //showEditModal(calEvent.id, calEvent.resourceId);


  },
    eventRender:function(event, element) {

        } 
    });

    $('.show-all-calendars').click(function(e){
      e.preventDefault();
      loadConnectedCalendars();
    })


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
<?php if (!$connected){ ?>
<script type="text/javascript">

function loadConnectedCalendars(){
<?php 
foreach($model->gearSimilars as $g)
{
?>
  $("#gear-calendar<?=$model->id?>").parent().append('<div style="height:100%; width:100%; background-color:white; margin-top:10px;" id="cal<?=$g->id?>"></div>');
  $("#cal<?=$g->id?>").load("<?=Url::to(['/gear/calendar', 'id'=>$g->similar_id, 'start'=>$start, 'end'=>$end, 'connected'=>1])?>");
<?php 
}
?>
}
</script>

<?php } } ?>



