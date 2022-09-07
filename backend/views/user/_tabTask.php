<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\widgets\ColorInput;
$user = Yii::$app->user;
/* @var $model \common\models\Event; */
Modal::begin([
    'id' => 'new-service',
    'header' => Yii::t('app', 'Dodaj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'edit-service',
    'header' => Yii::t('app', 'Edytuj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();


$this->registerJs('
    $(".add-service").click(function(e){
        $("#new-service").find(".modalContent").empty();
        e.preventDefault();
        $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');

$this->registerJs('
    $(".show-service").click(function(e){
        e.preventDefault();
        $(".task-schema-details").empty().load($(this).attr("href"));
    });
');
$this->registerJs('
    $(".done-button").click(function(e){
        e.preventDefault();
        data = [];
        $.post($(this).attr("href"), data, function(response){
            editServiceRow(response);
                    });
    });
');
?>
<div class="panel-body"> 
        <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                            <h5><?=Yii::t('app', 'Zadania')?></h5>
                                    <div class="ibox-tools white">
                                    <?php if ($user->can('menuTasksAdd')) { ?>
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj zadanie'), ['/task/create', 'user_id'=>$model->id], ['class'=>'white-button add-service']); ?>
                                    <?php } ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content no-padding">
                        <ul class="todo-list ui-sortable event-list" id="list-0">
                        <?php 
                        $i=0;
                        foreach ($model->uTasks as $task): 
                            $i++;
                        $class = "normal-element";
                        if ($task->status==10)
                            $class="success-element";
                        if ($task->status==5)
                        {
                            $class="danger-element";
                        }
                        if (($task->status==0)&&($task->datetime<date('Y-m-d')&&($task->datetime)))
                        {
                            $class="warning-element";
                        }
                        ?>                    
                            <li class="checklist-item <?=$class?>" draggable="true" id="item-<?=$task->id?>">
                            <div class="row" style="margin:0">
                                
                                <div class="pull-left" style="margin-right:10px;">
                                <?php if (isset($task->creator)) { ?>
                                <img alt="image" class="img-circle  img-very-small" src="<?php echo $task->creator->getUserPhotoUrl();?>" title="<?=$task->creator->first_name." ".$task->creator->last_name; ?>">
                                <?php } ?>
                                </div>
                                
                                <div class="pull-right team-members" style="margin:0px;">
                                <?php $members = $task->getAllUsers();
                                $user_num = count($members);
                                if ($user_num>0)
                                { ?>
                                   <img alt="image" class="img-circle  img-very-small" src="<?php echo $members[0]->getUserPhotoUrl();?>" title="<?=$members[0]->first_name." ".$members[0]->last_name; ?>"> 
                                <?php }  
                                if ($user_num>1)
                                { ?>
                                   <img alt="image" class="img-circle  img-very-small" src="<?php echo $members[1]->getUserPhotoUrl();?>" title="<?=$members[1]->first_name." ".$members[1]->last_name; ?>"> 
                                <?php }
                                if ($user_num>2)
                                { ?>
                                    <button class="btn btn-default btn-circle" type="button">+<?=$user_num-2?> </button>
                                <?php }  ?>
                                <?php if (($task->status==10)||($task->checkStatusForUser(Yii::$app->user->id))){ ?>
                                    <?= Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$task->id], ['class'=>'btn btn-primary btn-circle done-button']); ?>
                                <?php }else { ?>
                                    <?= Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$task->id], ['class'=>'btn btn-primary btn-circle btn-outline done-button']); ?>
                                <?php } ?>
                                </div>
                                <?=Html::a($task->title, ['/task/view', 'id'=>$task->id], ['class'=>'show-service'])?>

                                <?php if ($task->datetime){ ?>
                                <div class="agile-detail">
                                        <i class="fa fa-clock-o"></i> <?=substr($task->datetime,0,11)?>
                                </div>
                                <?php } ?>
                                </div>

                            </li>
                        <?php endforeach; ?>
                        </ul>
                        
                    </div>
            </div>
        </div>
        <div class="col-sm-6 task-schema-details">
                                    <blockquote>
                                    <p><?=Yii::t('app', 'Kliknij w nazwę zadania, żeby wyświetlić szczegóły')?>.</p>
                                </blockquote>
        </div>
        </div>
</div>

    <?php

$this->registerJs("
$( function() {
    $( '#list').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/task/order-cat'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );

  $('.delete-category').on('click', function(e){
    e.preventDefault();
    data=[];
    deleteCategory($(this));
  })
    ");

$this->registerJs("
$( function() {
    $( '#list-0').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/task/order'])."'
        });
    }
});
    $( '#list-0').disableSelection();
  } );



    ");
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function addNewRow(data)
    {
        $.post('/admin/task/small-view?id='+data.id, data, function(response){
                        if (data.task_category_id!=null)
                        {
                            $("#list-"+data.task_category_id).append('<li class="checklist-item normal-element" draggable="true" id="item-'+data.id+'">'+response+'</li>');
                        }else{
                            $("#list-0").append('<li class="checklist-item normal-element" draggable="true" id="item-'+data.id+'">'+response+'</li>');                            
                        }
                        
                            $("#item-"+data.id+" .done-button").click(function(e){
                                    e.preventDefault();
                                    data = [];
                                    $.post($(this).attr("href"), data, function(response){
                                        editServiceRow(response);
                                                });
                                });
                            $("#item-"+data.id+" .show-service").click(function(e){
                                e.preventDefault();
                                $(".task-schema-details").empty().load($(this).attr("href"));
                            });
                    });
        
        $(".task-schema-details").empty().load('/admin/task/view?id='+data.id);
    }


    function editServiceRow(data)
    {
                $.post('/admin/task/small-view?id='+data.id, data, function(response){
                        $("#item-"+data.id).empty().append(response);
                            $("#item-"+data.id+" .done-button").click(function(e){
                                    e.preventDefault();
                                    data = [];
                                    $.post($(this).attr("href"), data, function(response){
                                        editServiceRow(response);
                                                });
                                });
                            $("#item-"+data.id+" .show-service").click(function(e){
                                e.preventDefault();
                                $(".task-schema-details").empty().load($(this).attr("href"));
                            });
                    });
        $(".task-schema-details").empty().load('/admin/task/view?id='+data.id);
    }

    function deleteItem(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć?",
            icon:"warning",
          buttons: {
            cancel: "Nie",
            yes: {
              text: "Tak",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post(item.attr('href'), data, function(response){
                        row = $('#item-'+response.id);
                        row.remove();
                    });
              break;       
          }
        });
    }
</script>