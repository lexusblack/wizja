<?php
/* @var $this yii\web\View */
\common\assets\Fullcalendar4Asset::register($this);
\sammaye\qtip\QtipAsset::register($this);
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\cmenu\ContextMenu;


Modal::begin([
    'header' => Yii::t('app', 'Godziny pracy'),
    'id' => 'working_hours',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

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

Modal::begin([
    'id' => 'edit-name',
    'header' => Yii::t('app', 'Zmień nazwę'),
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
    'id' => 'add-task',
    'header' => Yii::t('app', 'Dodaj zadanie'),
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
<div class="col-sm-4" id="external-events">
<h3><?=Yii::t('app', 'Legenda')?></h3>
<?php foreach (\common\models\EventStatut::find()->where(['type'=>3])->all() as $status)
{
     echo "<span class='label label-info' style='background-color:".$status->color."; margin-bottom:10px;'>".$status->name."</span> ";
    } ?>
	<div id="external-events">
	 </div>
   </div>
	<div class="col-sm-8" id="calendar-container">
  <div class="filters">
  <?php
    $form = ActiveForm::begin([
        'id' => 'calendar-filter-form',
        'type' => ActiveForm::TYPE_INLINE,
    ]);

    echo $form->field($model, 'status')->widget(Select2::className(), [
            'data'=>\common\helpers\ArrayHelper::map(\common\models\EventStatut::find()->where(['type'=>3])->asArray()->all(), 'id', 'name'),
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
  <?php
  $script = <<< 'JS'
function (e, element, target) {
    e.preventDefault();
    if ((e.target.classList.contains("fc-title"))||($(e.target).parent().hasClass("fc-title"))) {
      if ($(e.target).hasClass("fc-title"))
      {
        event_id = $(e.target).data("id");
        event_type = $(e.target).data("type");
      }else{
        event_id = $(e.target).parent().data("id");
        event_type = $(e.target).parent().data("type");
      }
      
        return true;
    }else{
    if (($(e.target).parent().hasClass("fc-list-item-title"))||($(e.target).parent().hasClass("fc-list-item-title"))) {
      if ($(e.target).hasClass("fc-list-item-title"))
      {
        event_id = $(e.target).data("id");
        event_type = $(e.target).data("type");
      }else{
        event_id = $(e.target).parent().data("id");
        event_type = $(e.target).parent().data("type");
      }
      
        return true;
    }else{
      e.preventDefault();
        this.closemenu();
        return false;
    }
    }
    
}
JS;
  ContextMenu::begin([
    'items'=>[['label'=>Yii::t('app', 'Szczegóły'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-details']], ['label'=>Yii::t('app', 'Przypisz użytkowników'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-users']],['label'=>Yii::t('app', 'Godziny pracy'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-working']], ['label'=>Yii::t('app', 'Usuń z kalendarza'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-delete-from-calendar']], ['label'=>Yii::t('app', 'Usuń'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-delete']]],
    'options'=>['tag'=>'div'],
    'pluginOptions'=>[
    'id'=>'left',
        'before'=>$script,
        'onItem'=>'function(context, e)
        {    
          e.preventDefault();
          if ($(e.target).hasClass("event-details"))
          {
            if (event_type=="event")
            {
              window.open(
              "/admin/event/view?id="+event_id,
              "_blank" // <- This is what makes it open in a new window.
                );
            }else{

            }
          }
          if ($(e.target).hasClass("event-users"))
          {
            if (event_type=="event")
            {
               assignUsers(event_id);
            }else{
               assignTaskUsers(event_id);
            }
          }
          if ($(e.target).hasClass("event-delete-from-calendar"))
          {
              setNoDates(event_id, event_type);
          }
          if ($(e.target).hasClass("event-delete"))
          {
            if (event_type=="event")
            {
              deleteEvent(event_id);
            }else{
              deleteTask(event_id);
            }
          }
          if ($(e.target).hasClass("event-working"))
          {
            openTimeModal(event_id, event_type);
          }

        }',
    ]
]); ?>
		<div id="calendar">
		</div>
<?php ContextMenu::end(); ?>
	</div>
</div>

</div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
var calendar;
var event_id;
var event_type;
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
    defaultView: 'timeGridWeek',
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
    datesRender: function (info)
    {
      current_datetime = info.view.activeStart;
      start_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
      current_datetime = info.view.activeEnd;
      end_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
      //$("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>?start="+start_date+"&end="+end_date);
      $("#scroll-events").height($("#calendar").height());
      $(".fc-list-heading-main").each(function(){
        $(this).append(" <a class='btn btn-xs btn-danger print-day-plan' href='/admin/event/day-plan?day="+$(this).parent().parent().attr("data-date")+"' target='_blank'><i class='fa fa-print'></i></a>");
      });
      //tutaj pobieranie wydarzeń
    },
    drop: function(info) {
        event_id = info.draggedEl.dataset["eventid"];
        ev = calendar.getEventById(event_id);
        if (ev)
            ev.remove();
        current_datetime = info.date;
        dateFormat = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
        start_hour = current_datetime.getHours() + ":" + current_datetime.getMinutes()+ ":" + current_datetime.getSeconds();

        type =  info.draggedEl.dataset["type"];
        if (type=='event')
        {
          $.ajax({
                      data: {"id": event_id, "date_start":dateFormat, 'hour_start':start_hour, 'whole':1},
                      type: "POST",
                      url: "<?=Url::to(['/event/save-calendar-date'])?>",
                      success: function(success){
                        ev = calendar.getEventById(success.id);
                        ev.remove();
                        event = calendar.addEvent(success);
                      }
                  });
        }else{
          $.ajax({
                      data: {"id": event_id, "date_start":dateFormat, 'hour_start':start_hour, 'whole':1},
                      type: "POST",
                      url: "<?=Url::to(['/task/save-calendar-date'])?>",
                      success: function(success){
                        ev = calendar.getEventById(success.id);
                        ev.remove();
                        event = calendar.addEvent(success);
                      }
                  });
        }
        current_datetime = info.view.activeStart;
        start_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
        current_datetime = info.view.activeEnd;
        end_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
        $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>?start="+start_date+"&end="+end_date);
        
    },
    events: <?=json_encode($eventsArray)?>,
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
      
      if (info.event.extendedProps.type =="event")
      {
          $(info.el).find(".fc-list-item-marker").append(" <a href='#' class='btn btn-xs btn-default change-status'><?=Yii::t('app', 'Zmień')?></a>");
          $(info.el).find(".change-status").click(function(e){ e.preventDefault();
            changeStatusModal(info.event.id);
          });
      }
      if (info.event.extendedProps.type =="task")
      {
          
          $(info.el).find(".fc-list-item-marker").append(" <a href='#' class='btn btn-xs btn-default change-status-task'><?=Yii::t('app', 'Zmień')?></a>");
          $(info.el).find(".change-status-task").click(function(e){ e.preventDefault();
            changeTaskStatusModal(info.event.id);
          });
      }
      $(info.el).find(".fc-title").append(" <i class='fa fa-comment'></i> "+info.event.extendedProps.notes);
      $(info.el).find(".fc-title").append(" <i class='fa fa-paperclip'></i> "+info.event.extendedProps.files);
      $(info.el).find(".fc-list-item-title").append(" <i class='fa fa-comment'></i> "+info.event.extendedProps.notes);
      $(info.el).find(".fc-list-item-title").append(" <i class='fa fa-paperclip'></i> "+info.event.extendedProps.files);
      $(info.el).find(".fc-title").append(" <span class='users-label'>"+info.event.extendedProps.users+"</span>");
      $(info.el).find(".fc-list-item-title").append(" <span class='users-label'>"+info.event.extendedProps.users+"</span>"); 
      $(info.el).find(".fc-title").attr("data-id",info.event.id);
      $(info.el).find(".fc-title").attr("data-type",info.event.extendedProps.type);     
      $(info.el).find(".fc-list-item-title").attr("data-id",info.event.id);
      $(info.el).find(".fc-list-item-title").attr("data-type",info.event.extendedProps.type); 
        
      
      
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
    if (event.extendedProps.type=="event")
      $.post('/admin/event/save-calendar-date', data, function(response){
                        
                    });
    if (event.extendedProps.type=="task")
      $.post('/admin/task/save-calendar-date', data, function(response){
                        
                    });
    $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>");
}

function assignUsers(id)
{
  $("#edit-users").modal("show").find(".modalContent").load("/admin/event/assign-users?id="+id);
}
function assignTaskUsers(id)
{
  $("#edit-users").modal("show").find(".modalContent").load('/admin/task/edit-users?cal=1&id='+id);
}

function openTimeModal(id, type){
    var modal = $("#working_hours");
    if (type=="event")
      modal.find(".modalContent").load("/admin/event-user-working-time/create?cal=1&user_id=<?=Yii::$app->user->id?>&id="+id);
    else
      modal.find(".modalContent").load("/admin/event-user-working-time/create?cal=1&user_id=<?=Yii::$app->user->id?>&id="+id+"&task_id="+id);
    modal.modal("show");
    //alert(id);
}


function deleteTask(id)
{
  swal({
            title: "<?=Yii::t('app', 'Czy na pewno chcesz usunąć to zadanie?')?>",
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
              $.post('/admin/task/delete?id='+id, {id:id, ajax:1}, function(response){
                        ev = calendar.getEventById(id);
                        ev.remove();
                        $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>");
                    });
              break;       
          }
        });
}

function deleteEvent(id)
{
  swal({
            title: "<?=Yii::t('app', 'Czy na pewno chcesz usunąć to zadanie?')?>",
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
              $.post('/admin/event/delete?id='+id, {id:id, ajax:1}, function(response){
                  for(var i=0; i<response.tasks.length; i++)
                  {
                      ev = calendar.getEventById(response.tasks[i].id);
                      if (ev)
                        ev.remove();
                  }
                        ev = calendar.getEventById(id);
                        ev.remove();
                        $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>");
                    });
              break;       
          }
        });
}

function setNoDates(id, type)
{
        if (type=="event")
        {
          $.ajax({
                      data: {"id": id, "no-date":1},
                      type: "POST",
                      url: "<?=Url::to(['/event/save-calendar-date'])?>",
                      success: function(success){
                        ev = calendar.getEventById(id);
                        ev.remove();
                        $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>");
                      }
                  });
        }else{
          $.ajax({
                      data: {"id": id, "no-date":1},
                      type: "POST",
                      url: "<?=Url::to(['/task/save-calendar-date'])?>",
                      success: function(success){
                        ev = calendar.getEventById(id);
                        ev.remove();
                        $("#external-events").empty().load("<?=Url::to(['/site/get-for-calendar'])?>");
                      }
                  });
        }
        
}

function changeStatusModal(id)
{
  $("#change-status").modal("show").find(".modalContent").load("/admin/event/change-status-modal?id="+id);
}

function changeTaskStatusModal(id)
{
  $("#change-status").modal("show").find(".modalContent").load("/admin/task/change-status-modal?id="+id);
}

function showEditNamesModal(href)
{
  $("#edit-name").modal("show").find(".modalContent").load(href);
}

</script>


<?php 


$this->registerCss(
  '.agile-list .fc-event{color:#333; border:2px;}
  .agile-list .fc-event a{color:#333; background-color:transparent;}





  '
  );

foreach (\common\models\EventStatut::find()->all() as $status)
{
  $this->registerCss('
    .fc-event.event.status-'.$status->id.'{
      background-color:'.$status->color.'
    }

    .fc-list-item.event.status-'.$status->id.'>.fc-list-item-marker>.fc-event-dot{
      background-color:'.$status->color.'
    }

    ');
}

$this->registerCss('
  .fc-event.task.status-10{
      background-color:#009900;
    }

    .fc-list-item.task.status-10>.fc-list-item-marker>.fc-event-dot{
      background-color:#009900;
    }
  ');

$this->registerCss('
  .fc-event.task.status-0{
      background-color:#990000;
    }

    .fc-list-item.task.status-0>.fc-list-item-marker>.fc-event-dot{
      background-color:#990000;
    }
  ');

$this->registerCss('
  .task.ui-sortable-handle{
      margin-left:15px;
          padding: 0px 5px;
    }
  ');

$this->registerJs('
    $("#external-events").empty().load("'.Url::to(['/site/get-for-calendar']).'?type='.$model->type[0].'");
  ');
        
    