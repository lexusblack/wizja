<?php
/* @var $this yii\web\View */
\common\assets\Fullcalendar4Asset::register($this);
\sammaye\qtip\QtipAsset::register($this);
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use kartik\form\ActiveForm;
use kartik\select2\Select2;

$this->title = Yii::t('app', 'Kalendarz');
$this->params['breadcrumbs'][] = $this->title;
Modal::begin([
    'id' => 'edit-users',
    'header' => Yii::t('app', 'Przypisz użytkowników'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'change-status',
    'header' => Yii::t('app', 'Zmień status'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>


<div class="ibox">
<div class="ibox-content">
<div class="row">
	<div class="col-sm-3" id="external-events">
  <h3><?=Yii::t('app', 'Legenda')?></h3>
  <?php foreach (\common\models\Event::getTypeList() as $key =>$val)
  {
    if (isset($colors[$key]))
     echo "<span class='label label-info' style='background-color:".$colors[$key]."'>".$val."</span> ";
    } ?>
  <h2><?=Yii::t('app', 'Zadania do przydzielenia')?></h2>
  <p><?=Html::a(Yii::t('app', 'Dodaj zadanie'), ['/event/create'], ['class'=>'btn btn-info btn-sm'])?></p>
  <?php foreach ($projects as $project){ 
    ?>
    <h3><?=$project->name?></h3>
    <ul class="sortable-list connectList agile-list ui-sortable" id="project<?=$project->id?>" data-project="<?=$project->id?>">
    <?php foreach ($events as $event){
      if ($event->project_id==$project->id){ ?>
      <li class="success-element ui-sortable-handle fc-event" id="event<?=$event->id?>"  data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$colors[$event->type]?>">
                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
      </li>
    <?php } }?>
    </ul>
  <?php } ?>
  
    <h3><?=Yii::t('app', 'Pozostałe')?></h3>
    <ul class="sortable-list connectList agile-list ui-sortable" id="project0" data-project="0">
    <?php foreach ($events as $event){ ?>
    <?php
    if (!$event->project_id){ 
      if ($event->type>1){ ?>
<li class="success-element ui-sortable-handle fc-event" id="event<?=$event->id?>"  data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$colors[$event->type]?>">                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
      </li>
  <?php } } }?>
    </ul>
	 </div>
	<div class="col-sm-9" id="calendar-container">
  <div class="filters">
  <?php
    $form = ActiveForm::begin([
        'id' => 'calendar-filter-form',
        'type' => ActiveForm::TYPE_INLINE,
    ]);
    echo $form->field($model, 'type')->widget(Select2::className(), [
            'data'=>\common\models\Event::getTypeList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Typ'),
                'multiple'=>true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true

            ]

        ]);

    echo $form->field($model, 'status')->widget(Select2::className(), [
            'data'=>\common\models\Event::getStatusList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Status'),
                'multiple'=>true,

            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true

            ]

        ]);

    echo $form->field($model, 'users')->widget(Select2::className(), [
            'data'=>\common\models\User::getList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Użytkownik'),
                'multiple'=>true,

            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true

            ]

        ]);
    ?>
    <?= Html::submitButton(Yii::t('app', 'Zastosuj'), ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
  
  </div>
		<div id="calendar">
		</div>
	</div>
</div>

</div>
</div>
<script type="text/javascript">
var calendar;
document.addEventListener('DOMContentLoaded', function() {
  var Calendar = FullCalendar.Calendar;
  var Draggable = FullCalendarInteraction.Draggable;

  var containerEl = document.getElementById('external-events');
  var calendarEl = document.getElementById('calendar');

  // initialize the external events
  // -----------------------------------------------------------------

  new Draggable(containerEl, {
    itemSelector: '.fc-event',
    eventData: function(eventEl) {
      return {
        title: eventEl.innerText,
        id:eventEl.dataset["eventid"]
      };
    }
  });

  // initialize the calendar
  // -----------------------------------------------------------------

  calendar = new Calendar(calendarEl, {
    plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list'],
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    locale: 'pl',
    editable: true,
    eventResizableFromStart: true,
    droppable: true, // this allows things to be dropped onto the calendar
    timeFormat: 'H(:mm)', 
    eventTimeFormat: {
    hour: 'numeric',
    minute: '2-digit',
    meridiem: false,
    hour12:false,

    },
    textEscape: false,
    drop: function(info) {
        event_id = info.draggedEl.dataset["eventid"]
        current_datetime = info.date;
        dateFormat = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
        //dateFormat = current_datetime.toLocaleDateString();
        // if so, remove the element from the "Draggable Events" list
        info.draggedEl.parentNode.removeChild(info.draggedEl);
        $.ajax({
                      data: {"id": event_id, "date_start":dateFormat, 'whole':1},
                      type: "POST",
                      url: "<?=Url::to(['/event/save-calendar-date'])?>",
                      success: function(success){
                        ev = calendar.getEventById(success.id);
                        ev.remove();
                        //ev.setStart(success.start);
                        //ev.setEnd(success.end);
                        //calendar.fullCalendar('clientEvents', success.id)[0].start = success.start;
                        //calendar.fullCalendar('clientEvents', success.id)[0].end = success.end;
                        event = calendar.addEvent(success);
                      }
                  });
    },
    events: <?=$eventsArray?>,
    eventResize: function(eventDropInfo) {
            changeEventDates(eventDropInfo.event, null);

    },
    eventDrop: function(eventDropInfo) {
            changeEventDates(eventDropInfo.event, null);
    },
    eventClick: function(info) {
    //location.href = '/admin/event/view?id='+info.event.id;
    /*window.open(
      '/admin/event/view?id='+info.event.id,
      '_blank' // <- This is what makes it open in a new window.
        );*/
    },
    eventRender: function(info) {
      $(info.el).qtip({
                content: info.event.extendedProps.description,
                show: {
                    solo: true
                },
                position: {
                    target: "mouse",
                    viewport: $(window),
                    adjust: {
                        mouse:true,
                        x: 5,
                        y: 5,

                    }
                },
                style: {
                    classes: "qtip-tipsy"
                }
                
            });
      $(info.el).find(".fc-title").append(" <span class='users-label'>"+info.event.extendedProps.users+"</span>");
      $(info.el).find(".fc-title").append(" <i class='fa fa-comment'></i> "+info.event.extendedProps.notes);
      $(info.el).find(".fc-title").append(" <i class='fa fa-paperclip'></i> "+info.event.extendedProps.files);
      $(info.el).find(".fc-list-item-title").append(" <span class='users-label'>"+info.event.extendedProps.users+"</span>");
        $(info.el).find(".fc-list-item-title").append(" <i class='fa fa-comment'></i> "+info.event.extendedProps.notes);
      $(info.el).find(".fc-list-item-title").append(" <i class='fa fa-paperclip'></i> "+info.event.extendedProps.files);
      $(info.el).find(".fc-list-item-marker").append(" <a href='#' class='btn btn-xs btn-default change-status'><?=Yii::t('app', 'Zmień')?></a>");
      $(info.el).find(".fc-title").contextmenu(function(e){
        e.preventDefault();
        assignUsers(info.event.id);


      })
      $(info.el).find(".change-status").click(function(e){ e.preventDefault();
        changeStatusModal(info.event.id);
      });
      $(info.el).find(".fc-title").click(function(e){e.preventDefault(); window.open(
      '/admin/event/view?id='+info.event.id,
      '_blank' // <- This is what makes it open in a new window.
        );});
      $(info.el).find(".fc-list-item-title").click(function(e){e.preventDefault(); window.open(
      '/admin/event/view?id='+info.event.id,
      '_blank' // <- This is what makes it open in a new window.
        );
      });
    }

  });

  calendar.render();
});

function changeEventDates(event, revertFunc)
{ 
    current_datetime = event.start;
    start_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
    start_hour = current_datetime.getHours() + ":" + current_datetime.getMinutes()+ ":" + current_datetime.getSeconds();
    current_datetime = event.end;
    //alert(event.end);
    if (current_datetime===null){
      current_datetime = event.start;
      current_datetime.setHours(current_datetime.getHours()+1)
    }
      end_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
      end_hour = current_datetime.getHours() + ":" + current_datetime.getMinutes()+ ":" + current_datetime.getSeconds();
    data = {'date_start':start_date, 'hour_start':start_hour, 'date_end':end_date, 'hour_end':end_hour, 'whole':0, 'id':event.id};
    $.post('/admin//event/save-calendar-date', data, function(response){
                        
                    });
}

function assignUsers(id)
{
  $("#edit-users").modal("show").find(".modalContent").load("/admin/event/assign-users?id="+id);
}

function changeStatusModal(id)
{
  $("#change-status").modal("show").find(".modalContent").load("/admin/event/change-status-modal?id="+id);
}

</script>


<?php 
$this->registerJs('

            $(".ui-sortable.connectList").sortable({
                connectWith: ".connectList",
                update: function( event, ui ) {
                    data = $(this).sortable("toArray");
                    $.ajax({
                      data: {"events": data},
                      type: "POST",
                      url: "'.Url::to(['/event/add-to-projects']).'?id="+$(this).data("project")
                  });

                }
            }).disableSelection();');

$this->registerCss(
  '.agile-list .fc-event{color:#333; border:2px;}
  .agile-list .fc-event a{color:#333; background-color:transparent;}
  .fc-event.typ-2{
    background-color:'.$colors[2].'
  }
    .fc-event.typ-3{
    background-color:'.$colors[3].'
  }
    .fc-event.typ-4{
    background-color:'.$colors[4].'
  }





  '
  );

foreach (\common\models\EventStatut::find()->all() as $status)
{
  $this->registerCss('
    .fc-event.status-'.$status->id.'{
      border-color:'.$status->color.'
    }

    .fc-list-item.status-'.$status->id.'>.fc-list-item-marker>.fc-event-dot{
      background-color:'.$status->color.'
    }

    ');
}
        
    