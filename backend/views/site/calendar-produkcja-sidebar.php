<?php
/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\cmenu\ContextMenu;
Modal::begin([
    'id' => 'add-purchase',
    'header' => Yii::t('app', 'Dodaj zakupy'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo '<div class="modalContent"></div>';
Modal::end();
$this->registerJs('
    $(".add-purchaase").click(function(e){
        $("#add-purchase").find(".modalContent").empty();
        e.preventDefault();
        $("#add-purchase").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".add-purchaase").on("contextmenu",function(){
       return false;
    }); ');
?>
    <?php
  $script = <<< 'JS'
function (e, element, target) {
    e.preventDefault();
    if (($(e.target).hasClass("success-element"))||($(e.target).parent().hasClass("success-element"))) {
      if ($(e.target).hasClass("success-element"))
      {
        event_id = $(e.target).data("eventid");
        event_type = $(e.target).data("type");
      }else{
        event_id = $(e.target).parent().data("eventid");
        event_type = $(e.target).parent().data("type");
      }
      
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
    'menuContainer'=>['id'=>'boczny'],
    'items'=>[['label'=>Yii::t('app', 'Szczegóły'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-details']], ['label'=>Yii::t('app', 'Zmień status'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-status']], ['label'=>Yii::t('app', 'Przypisz użytkowników'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-users']],['label'=>Yii::t('app', 'Godziny pracy'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-working']], ['label'=>Yii::t('app', 'Usuń z kalendarza'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-delete-from-calendar']], ['label'=>Yii::t('app', 'Usuń'), 'url'=>['#'], 'linkOptions'=>['class'=>'event-delete']]],
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
          if ($(e.target).hasClass("event-status"))
          {
            if (event_type=="event")
            {
               changeStatusModal(event_id);
            }else{
               changeTaskStatusModal(event_id);
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
  <div id="scroll-events" style="overflow-y:scroll;">
  <h2><?=Yii::t('app', 'Zadania do przydzielenia')?></h2>
  <p><?=Html::a(Yii::t('app', 'Dodaj zadanie'), ['/event/create', 'type'=>2], ['class'=>'btn btn-info btn-sm'])?></p>

    <?php foreach ($projects as $project){ 
    ?>
    <h5>
    <?=Html::a("<i class='fa fa-shopping-cart'></i> ".$project->getPurhaseListItemNumber(), ['event/get-purchase-items', 'id'=>$project->id], ['class'=>'btn btn-warning btn-xs pull-right get-purchases', 'target'=>'_blank']) ?>
    <?=Html::a("<i class='fa fa-print'></i>", ['event/print-prod-tasks', 'id'=>$project->id], ['class'=>'btn btn-danger btn-xs pull-right', 'target'=>'_blank']) ?><?=Html::a('<i class="fa fa-plus"></i>', ['event/add-prod', 'id'=>$project->id, 'type'=>$model->type[0]], ['class'=>'btn btn-primary btn-xs pull-right add-task-button'])?><label class="label label-info pull-right"><?=Yii::t('app', 'Wydatki: ').Yii::$app->formatter->asCurrency($project->getTotalProductionCrewCost())."<br/>".Yii::t('app', 'Budżet: ').Yii::$app->formatter->asCurrency($project->getTotalProductionBudget())?></label> <?=Html::a('['.$project->code.'] '.$project->name, ['event/view', 'id'=>$project->id], ['target'=>'_blank'])." <br/>".Yii::$app->formatter->asDateTime($project->getTimeStart(),'short')." - ".Yii::$app->formatter->asDateTime($project->getTimeEnd(),'short').")"?></h5>
    <ul class="sortable-list connectList agile-list ui-sortable" id="project<?=$project->id?>" data-project="<?=$project->id?>">
    <?php foreach ($events as $event){
      if ($event->getTaskFor()){
      if ($event->getTaskFor()->event_id==$project->id){ ?>
      <li class="success-element ui-sortable-handle fc-event" id="event<?=$event->id?>" data-type="event"  data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$event->eventStatut->color?>">
                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['event/edit-name', 'id'=>$event->id], ['class'=>'event-edit-name']) ?>
                                    <?=Html::a('<i class="fa fa-plus"></i>', ['event/add-prod-task', 'id'=>$event->id], ['class'=>'add-task-button'])?>
                                   <?=Html::a('<i class="fa fa-shopping-cart"></i>', ['/outer-warehouse/add-to-event', 'task_id'=>$event->getTaskFor()->id, 'event_id'=>$event->getTaskFor()->event_id, 'prod'=>true], ['style'=>'color:#5e5e5e', 'class'=>'add-purchaase'])?>
      </li>
      <?php foreach ($event->tasks as $task){ 
        if ($task->datetime)
          $class = " fc-event";
        else
          $class = " fc-event";
        if ($task->status==10)
          $c = "#009900";
        else
          $c = "#990000";
        ?>
      <li class="success-element ui-sortable-handle <?=$class?> task" id="task<?=$task->id?>" data-type="etask"  data-eventid="<?=$task->id?>" data-id="item-<?=$task->id?>" data-event='{ title: "<?=$task->title?>", id: <?=$task->id?> }' style="border-left-color:<?=$c?>;">
                                    <?=Html::a($task->title, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['task/edit-name', 'id'=>$task->id], ['class'=>'event-edit-name']) ?>

                                    <?php if ($task->datetime){ ?>
                                    <span class="label pull-right"><i class="fa fa-clock-o"></i><?=substr($task->datetime, 0, 11)?></span>
                                    <?php } ?>
      </li>
      <?php  } ?>
    <?php } } }?>
    <?php foreach ($events2 as $event){
      if ($event->getTaskFor()){
      if ($event->getTaskFor()->event_id==$project->id){ ?>
      <li class="success-element ui-sortable-handle  fc-event" id="event<?=$event->id?>" data-type="event"  data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$event->eventStatut->color?>">
                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['event/edit-name', 'id'=>$event->id], ['class'=>'event-edit-name']) ?>
                                    <?=Html::a('<i class="fa fa-plus"></i>', ['event/add-prod-task', 'id'=>$event->id], ['class'=>'add-task-button'])?>

                                    <span class="label pull-right"><i class="fa fa-clock-o"></i><?=substr($event->event_start, 0, 11)?></span>
      </li>
      <?php foreach ($event->tasks as $task){ 
        if ($task->datetime)
          $class = " fc-event";
        else
          $class = " fc-event";
        if ($task->status==10)
          $c = "#009900";
        else
          $c = "#990000";
        ?>
      <li class="success-element ui-sortable-handle <?=$class?> task" id="task<?=$task->id?>" data-type="etask"  data-eventid="<?=$task->id?>" data-id="item-<?=$task->id?>" data-event='{ title: "<?=$task->title?>", id: <?=$task->id?> }' style="border-left-color:<?=$c?>;">
                                    <?=Html::a($task->title, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['task/edit-name', 'id'=>$task->id], ['class'=>'event-edit-name']) ?>
                                    
                                    <?php if ($task->datetime){ ?>
                                    <span class="label pull-right"><i class="fa fa-clock-o"></i><?=substr($task->datetime, 0, 11)?></span>
                                    <?php } ?>
      </li>
      <?php  } ?>
    <?php } } }?>
    </ul>
  <?php } 
  ?>
    <h3><?=Yii::t('app', 'Pozostałe')?></h3>
    <ul class="sortable-list connectList agile-list ui-sortable" id="project0" data-project="0">
    <?php foreach ($events2 as $event){ 
      ?>
    <?php
    if (!$event->getTaskFor()){ 
      if ($event->type>1){ ?>
    <li class="success-element ui-sortable-handle fc-event" id="event<?=$event->id?>"  data-type="event" data-eventid="<?=$event->id?>" data-id="item-<?=$event->id?>" data-event='{ title: "<?=$event->name?>", id: <?=$event->id?> }' style="border-left-color:<?=$event->eventStatut->color?>">                                    <?=Html::a($event->name, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
    <?=Html::a("<i class='fa fa-pencil'></i>", ['event/edit-name', 'id'=>$event->id], ['class'=>'event-edit-name']) ?>
      </li>
      <?php foreach ($event->tasks as $task){ 
        if ($task->datetime)
          $class = " fc-event";
        else
          $class = " fc-event";
        if ($task->status==10)
          $c = "#009900";
        else
          $c = "#990000";
        ?>
      <li class="success-element ui-sortable-handle <?=$class?> task" id="task<?=$task->id?>" data-type="etask"  data-eventid="<?=$task->id?>" data-id="item-<?=$task->id?>" data-event='{ title: "<?=$task->title?>", id: <?=$task->id?> }' style="border-left-color:<?=$c?>;">
                                    <?=Html::a($task->title, ['/event/view', 'id'=>$event->id],['target'=>'_blank'])?>
                                    <?=Html::a("<i class='fa fa-pencil'></i>", ['task/edit-name', 'id'=>$task->id], ['class'=>'event-edit-name']) ?>
                                    
                                    <?php if ($task->datetime){ ?>
                                    <span class="label pull-right"><i class="fa fa-clock-o"></i><?=substr($task->datetime, 0, 11)?></span>
                                    <?php } ?>
      </li>
      <?php  } ?>
  <?php } } }?>
    </ul>
    </div>
	<?php ContextMenu::end(); ?>

<?php $this->registerJs('

  $(".event-edit-name").click(function(e){
    e.preventDefault();
    showEditNamesModal($(this).attr("href"));
  });

  $(".get-purchases").click(function(e){
    e.preventDefault();
    showEditNamesModal($(this).attr("href"));
  });
  $(".add-task-button").click(function(e){
    e.preventDefault();
    $("#add-task").modal("show").find(".modalContent").load($(this).attr("href"));
  });


  $("#scroll-events").height($("#calendar").height());
  ');

?>
        
<script type="text/javascript">
  
  function changeRow(data)
  {
    $("#event"+data.id+ " a").each(function()
    {
      if ($(this).hasClass("event-edit-name")){

      }else{
        if ($(this).hasClass("add-task-button"))
        {

        }else{
          $(this).html(data.name);
        }
        
      }
    });
  }
  function changeRowTask(data)
  {
    $("#task"+data.id+ " a").each(function()
    {
      if ($(this).hasClass("event-edit-name")){

      }else{
        $(this).html(data.title);
      }
    });
  }

  function addRow(data, id)
  {
    $("#project"+id).append(data);
  }
  function addRowTask(data, id)
  {
    $("#event"+id).after(data);
  }
</script>