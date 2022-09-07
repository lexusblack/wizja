<?php
use yii\bootstrap\Html;
use yii\helpers\Url;

use kartik\widgets\ColorInput;
use backend\modules\permission\models\BasePermission;
use common\models\Task;

$user = Yii::$app->user;
/* @var $model \common\models\Event; */
use yii\bootstrap\Modal;
Modal::begin([
    'id' => 'new-service',
    'header' => Yii::t('app', 'Dodaj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
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
    'id' => 'edit-service',
    'header' => Yii::t('app', 'Edytuj zadanie'),
    'class' => 'modal',
        'size' => 'modal-lg',
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
    'id' => 'new-service-category',
    'header' => Yii::t('app', 'Dodaj grupę zadań'),
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
    'id' => 'edit-service-category',
    'header' => Yii::t('app', 'Edytuj grupę zadań'),
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
    'id' => 'edit-events',
    'header' => Yii::t('app', 'Przypisz wydarzenie'),
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
    $(".add-service").click(function(e){
        $("#new-service").find(".modalContent").empty();
        e.preventDefault();
        $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');

$this->registerJs('
    $(".edit-users-button").click(function(e){
        $("#edit-users").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-users").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-users-button").on("contextmenu",function(){
       return false;
    });
    $(".add-purchaase").click(function(e){
        $("#add-purchase").find(".modalContent").empty();
        e.preventDefault();
        $("#add-purchase").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".add-purchaase").on("contextmenu",function(){
       return false;
    });
    $(".edit-for-event-button").click(function(e){
        $("#edit-events").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-events").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".edit-for-event-button").on("contextmenu",function(){
       return false;
    });

    $(".edit-produkcja-button").click(function(e){
        $(this).attr("disabled", true);
        e.preventDefault();
        $.post($(this).attr("href"), {}, function(response){
                editServiceRow(response);
        });
        });
        $(".edit-produkcja-button").on("contextmenu",function(){
                    return false;
        })

'); 

$this->registerJs('
    $(".add-service-category").click(function(e){
        e.preventDefault();
        $("#new-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');

$this->registerJs('
    $(".edit-service-category").click(function(e){
        e.preventDefault();
        $("#edit-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
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
        <?php if ($user->can('menuTasksAdd')) { ?>
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj grupę zadań'), ['/task/create-cat', 'event_id'=>$model->id], ['class'=>'btn btn-primary add-service-category'])." "; ?> 
        <?php } ?>
<?php if ($model->type>1) {echo Html::dropDownList('for_event', null, $model->getForEvents(),[
            'class' => 'form-control pull-right',
            'style' => 'width:200px',
            'id'=>'for-event-select'
        ]); } ?> 
        <ul class="todo-list ui-sortable event-list" id="list">
            <?php foreach($model->taskCategories as $category): 
            ?>
            <li class="checklist-item no-padding" draggable="true" id="bigitem-<?=$category->id?>">
            <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg" style="background-color:<?=$category->color?>">
                            <h5><?=$category->name?></h5>
                                    <div class="ibox-tools white">
                                    <?php if ($user->can('menuTasksAdd')) { ?>
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj zadanie'), ['/task/create', 'category_id'=>$category->id, 'event_id'=>$model->id], ['class'=>'white-button add-service']); ?>
                                    <?php } ?>
                                    <?php if ($user->can('menuTasksEdit')) { ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i> ', ['/task/cat-update', 'id'=>$category->id], ['class'=>'white-button edit-service-category']); ?>
                                    <?php } ?>
                                    <?php if ($user->can('menuTasksDelete')) { ?>
                                    <?= Html::a(Html::icon('trash'), ['/task/cat-delete', 'id' => $category->id], [
                                            'class'=>'delete-category'
                                        ])
                                        ?>
                                    <?= Html::a('<i class="fa fa-print"></i> ', ['/event/print-tasks', 'category_id'=>$category->id, 'event_id'=>$model->id], ['class'=>'white-button', 'target'=>'_blank']); ?>
                                    <?php } ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content no-padding">
                    <div class="dd" id="list-<?=$category->id?>">
                    <ol class="dd-list main">
                        
                        <?php 
                        foreach ($category->tasks as $task): 
                        if (($task->isMine(Yii::$app->user->id))||($task->creator_id==Yii::$app->user->id)||((Yii::$app->user->can('menuTasksView'.BasePermission::SUFFIX[BasePermission::ALL])))){
                            if ($task->type==1)
                                echo Yii::$app->controller->renderPartial('/task/smallview', ['task' => $task, 'content'=>false]);
                        } 
                        endforeach; ?>
                        </ol>

                    </div>
                    <div class="task-schema-form" style="margin-top:10px">
                    <?php $new_task = new Task();
                        $new_task->task_category_id = $category->id;
                        $new_task->event_id = $model->id;
                        echo Yii::$app->controller->renderPartial('/task/_smallform', ['model' => $new_task]);
                        ?>
                        

                    </div>
                    </div>
            </div>
            </li>
        <?php endforeach; ?>
        </ul>
<div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                            <h5><?=Yii::t('app', 'Pozostałe')?></h5>
                                    <div class="ibox-tools white">
                                    <?php if ($user->can('menuTasksAdd')) { ?>
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj zadanie'), ['/task/create', 'event_id'=>$model->id], ['class'=>'white-button add-service']); ?>
                                    <?php } ?>
                                    <?= Html::a('<i class="fa fa-print"></i> ', ['/event/print-tasks', 'category_id'=>null, 'event_id'=>$model->id], ['class'=>'white-button', 'target'=>'_blank']); ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content no-padding">
                    <div class="dd" id="list-0">
                    <ol class="dd-list main">
                    <?php 
                        $i=0;
                        foreach ($model->getOtherTasks() as $task): 
                             if (($task->isMine(Yii::$app->user->id))||($task->creator_id==Yii::$app->user->id)||((Yii::$app->user->can('menuTasksView'.BasePermission::SUFFIX[BasePermission::ALL])))){
                           if ($task->type==1)
                                echo Yii::$app->controller->renderPartial('/task/smallview', ['task' => $task, 'content'=>false]);
                            }
                        endforeach; ?>


                                    
                                </ol>
                            </div>
                            <?php if ($user->can('menuTasksAdd')) { ?>
                            <div class="task-schema-form" style="margin-top:10px">
                            <?php $new_task = new Task();
                                $new_task->event_id = $model->id;
                                echo Yii::$app->controller->renderPartial('/task/_smallform', ['model' => $new_task]);
                        ?>
                        

                    </div>
                    <?php } ?>
        </div>
    </div>

<div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                            <h5><?=Yii::t('app', 'Powiązane')?></h5>
                                    <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content no-padding">
                    <div class="dd" id="list-0">
                    <ol class="dd-list main">
                    <?php 
                        $i=0;
                        foreach ($model->getForTasks() as $task): 
                             if (($task->isMine(Yii::$app->user->id))||($task->creator_id==Yii::$app->user->id)||((Yii::$app->user->can('menuTasksView'.BasePermission::SUFFIX[BasePermission::ALL])))){
                           if ($task->type==1)
                                echo Yii::$app->controller->renderPartial('/task/smallviewforevent', ['task' => $task, 'content'=>false]);
                            }
                        endforeach; ?>


                                    
                                </ol>
                            </div>
                            
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


    $('#list-0').nestable({
                 group: 1,
                 maxDepth:2
             }).on('change', function(){sendOrder($('#list-0').nestable('serialize'),0);});;

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
$('.new-task-input').on('change', function(e) {
    var form = $(this).closest('form');
    var formData = form.serialize();
    var input = $(this);
    $.ajax({
        url: '".Url::to(['/task/create'])."',
        type: 'POST',
        data: formData,
        success: function (data) {
            addNewRow(data);
            input.val('');
            
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});

$('#for-event-select').change(function(e){
    var val = $(this).val();
    $('.dd-item').hide();
    if (val>0)
    {
        $('.dd-item.for-event-'+val).show();
    }else{
        $('.dd-item').show();
    }
});
");



foreach($model->taskCategories as $category):
$this->registerJs("
    $('#list-".$category->id."').nestable({
                 group: 1,
                 maxDepth:2
             }).on('change', function(){sendOrder($('#list-".$category->id."').nestable('serialize'),".$category->id.");});


    ");
endforeach;

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">

function sendOrder(list, category_id)
{
    $.post('/admin/task/order?event_id=<?=$model->id?>&cat='+category_id, {data:JSON.stringify(list)}, function(response){
        for (i=0; i<list.length; i++)
        {
            if (jQuery.isEmptyObject(list[i].children))
            {
                $("#"+list[i].id+" .edit-produkcja-button").parent().show();
            }else{
                for (j=0; j<list[i].children.length; j++)
                {
                    $("#"+list[i].children[j].id+" .edit-produkcja-button").parent().hide();
                }
            }

        }
    });
}

    function addNewRow(data)
    {
        $.post('/admin/task/small-view?id='+data.id, data, function(response){
                        if (data.task_category_id!=null)
                        {
                            $("#list-"+data.task_category_id).find('.dd-list.main').append(response);
                        }else{
                            $("#list-0").find('.dd-list.main').append(response);                            
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
                             $("#item-"+data.id+" .edit-users-button").click(function(e){
                                $("#edit-users").find(".modalContent").empty();
                                e.preventDefault();
                                $("#edit-users").modal("show").find(".modalContent").load($(this).attr("href"));
                                });
                            $("#item-"+data.id+" .edit-users-button").on("contextmenu",function(){
                                   return false;
                                })
                            $("#item-"+data.id+" .edit-for-event-button").click(function(e){
                                $("#edit-events").find(".modalContent").empty();
                                e.preventDefault();
                                $("#edit-events").modal("show").find(".modalContent").load($(this).attr("href"));
                                });
                            $("#item-"+data.id+" .edit-for-event-button").on("contextmenu",function(){
                                   return false;
                                })
                            $("#item-"+data.id+" .edit-produkcja-button").click(function(e){
                                e.preventDefault();
                                $.post($(this).attr("href"), {}, function(response){
                                    editServiceRow(response);
                                });
                                });
                            $("#item-"+data.id+" .edit-produkcja-button").on("contextmenu",function(){
                                   return false;
                                })
                    });
        
        $(".task-schema-details").empty().load('/admin/task/view?id='+data.id);
    }


    function editServiceRow(data)
    {
                $.post('/admin/task/small-view?id='+data.id+'&content=true', data, function(response){
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
    function addNewCategoryRow(data)
    {

        $("#list").append('<li class="checklist-item no-padding" draggable="true" id="bigitem-'+data.id+'"><div class="ibox float-e-margins"><div class="ibox-title navy-bg"><h5>'+data.name+'</h5><div class="ibox-tools white"><a class="white-button add-service" href="/admin/task/create?category_id='+data.id+'&event_id=<?=$model->id?>"><i class="fa fa-plus"></i> Dodaj zadanie</a><a class="white-button edit-service-category" href="/admin/taskcat/update-cat?id='+data.id+'"><i class="fa fa-pencil"></i> </a><a class="delete-category" href="/admin/task/delete-cat?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div></div><div class="ibox-content no-padding"><div class="dd" id="list-'+data.id+'"><ol class="dd-list main"></ol></div></div></div>');
        $("#bigitem-"+data.id).find(".add-service").click(function(e){
            e.preventDefault();
            $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
        });
        $("#bigitem-"+data.id).find(".edit-service-category").click(function(e){
            e.preventDefault();
            $("#edit-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
        });
         $("#bigitem-"+data.id).find('.delete-category').on('click', function(e){
            e.preventDefault();
            data=[];
            deleteCategory($(this));
          })
         $("#bigitem-"+data.id).find(".ibox-title").css("background-color", data.color);
         $('html,body').animate({
          scrollTop: $("#bigitem-"+data.id).offset().top
        }, 1000)
         $("#list-"+data.id).parent().append('<div class="task-schema-form" style="margin-top:10px"><form id="TaskSmallForm"  method="post"><div class="form-group field-task-title required"><input type="text" id="task-title" class="new-task-input form-control" name="Task[title]" maxlength="255" placeholder="Nazwa zadania" autocomplete="off" aria-required="true"><div class="help-block"></div></div><div class="form-group field-task-task_category_id"><input type="text" id="task-task_category_id" class="form-control" name="Task[task_category_id]" value="'+data.id+'" style="display:none"></div><div class="form-group field-task-event_id"><input type="text" id="task-event_id" class="form-control" name="Task[event_id]" value="<?=$model->id?>" style="display:none"></div></form></div>');
         $("#bigitem-"+data.id).find(".new-task-input").on('change', function(e) {
            var form = $(this).closest('form');
            var formData = form.serialize();
            var input = $(this);
            $.ajax({
                url: "<?=Url::to(['/task/create'])?>",
                type: 'POST',
                data: formData,
                success: function (data) {
                    addNewRow(data);
                    input.val('');
                    
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
    });
    }
    function UpdateCategoryRow(data)
    {
        var $row = $("#bigitem-"+data.id).find(".ibox-title");
        $row.find('h5').empty().append(data.name);
        $row.css("background-color", data.color);

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
    function deleteCategory(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć grupę z wszystkimi pozycjami?",
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
                        row = $('#bigitem-'+response.id);
                        row.remove();
                    });
              break;       
          }
        });        
    }
</script>

