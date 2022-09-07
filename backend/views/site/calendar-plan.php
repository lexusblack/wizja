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


$this->title = Yii::t('app', 'Kalendarz');
$this->params['breadcrumbs'][] = $this->title;
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


?>


<div class="ibox" id="cal-plan">
<div class="ibox-content">
<div class="sk-spinner sk-spinner-double-bounce">
                                <div class="sk-double-bounce1"></div>
                                <div class="sk-double-bounce2"></div>
                            </div>
<div class="row">
	<div class="col-sm-12" id="calendar-container">

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
    if (e.target.classList.contains("fc-title")) {
      event_id = $(e.target).data("id");
      event_type = $(e.target).data("type");
        return true;
    }else{
    if ($(e.target).parent().hasClass("fc-list-item-title")) {
      event_id = $(e.target).parent().data("id");
      event_type = $(e.target).parent().data("type");
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
    'items'=>[['label'=>Yii::t('app', 'Szczegóły'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-details']]],
    'options'=>['tag'=>'div'],
    'pluginOptions'=>[
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
            }
            if (event_type=="rent")
            {
              window.open(
              "/admin/rent/view?id="+event_id,
              "_blank" // <- This is what makes it open in a new window.
                );
            }
            if (event_type=="meeting")
            {
              window.open(
              "/admin/meeting/view?id="+event_id,
              "_blank" // <- This is what makes it open in a new window.
                );
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
  var calendarEl = document.getElementById('calendar');

  // initialize the external events
  // -----------------------------------------------------------------


  // initialize the calendar
  // -----------------------------------------------------------------

  calendar = new Calendar(calendarEl, {
    plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list'],
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    defaultView: 'listMonth',
    locale: 'pl',
    editable: false,
    eventResizableFromStart: false,
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
      //tutaj pobieranie wydarzeń
      $('#cal-plan').children('.ibox-content').addClass('sk-loading');
      current_datetime = info.view.activeStart;
        start_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
        current_datetime = info.view.activeEnd;
        end_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();
      $.post('/admin/event/get-events-for-calendar?start='+start_date+"&end="+end_date, [], function(response){
                        for (i=0; i<response.length; i++)
                        {
                          ev = calendar.getEventById(response[i].id);
                          if (ev){

                          }else{
                            event = calendar.addEvent(response[i]);
                          }
                          
                          
                        }
                        $('#cal-plan').children('.ibox-content').removeClass('sk-loading');
                    });
    },

    events: [],
    eventClick: function(info) {
    //location.href = '/admin/event/view?id='+info.event.id;
    if (info.event.extendedProps.type=="event")
    {
      window.open(
      '/admin/event/view?id='+info.event.extendedProps.e_id,
      '_blank' // <- This is what makes it open in a new window.
        );
    }
    
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
      
      $(info.el).find(".fc-title").attr("data-id",info.event.id);
      $(info.el).find(".fc-title").attr("data-type",info.event.extendedProps.type);     
      $(info.el).find(".fc-list-item-title").attr("data-id",info.event.id);
      $(info.el).find(".fc-list-item-title").attr("data-type",info.event.extendedProps.type); 
        
      
      
    }

  });

  calendar.render();
});


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
  .fc-event.rent{
      background-color:'.Yii::$app->settings->get('main.rentColor').';
    }

    .fc-list-item.rent>.fc-list-item-marker>.fc-event-dot{
      background-color:'.Yii::$app->settings->get('main.rentColor').';
    }
  ');

$this->registerCss('
  .fc-event.meeting{
      background-color:'.Yii::$app->settings->get('main.meetingColor').';
    }

    .fc-list-item.meeting>.fc-list-item-marker>.fc-event-dot{
      background-color:'.Yii::$app->settings->get('main.meetingColor').';
    }
  ');


$this->registerCss('
  .task.ui-sortable-handle{
      margin-left:15px;
          padding: 0px 5px;
    }
  ');
        
    