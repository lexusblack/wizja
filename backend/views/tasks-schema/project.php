<?php

use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
//use Symfony\Component\VarDumper\VarDumper;
use kartik\widgets\ColorInput;

use kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Offer */
/* @var $offerForm \backend\modules\offers\models\OfferForm */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schematy zadań'), 'url' => ['/tasks-schema/index']];
$this->params['breadcrumbs'][] = $this->title;

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
Modal::begin([
    'id' => 'new-service-category',
    'header' => Yii::t('app', 'Dodaj grupę zadań'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
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
    $(".edit-service").click(function(e){
        $("#edit-service").find(".modalContent").empty();
        e.preventDefault();
        $("#edit-service").modal("show").find(".modalContent").load($(this).attr("href"));
    });
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


?>

<div class="panel_mid_blocks">
        <div class="panel_block">
        <div>
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj grupę zadań'), ['/tasks-schema-cat/create', 'schema_id'=>$model->id], ['class'=>'btn btn-primary add-service-category'])." "; ?>            
        </div>
        <div class="row">
        <div class="col-sm-6">
        <ul class="todo-list ui-sortable" id="list">
            <?php foreach($model->tasksSchemaCats as $category): 
            ?>
            <li class="checklist-item" draggable="true" id="bigitem-<?=$category->id?>">
            <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg" style="background-color:<?=$category->color?>">
                            <h5><?=$category->name?></h5>
                                    <div class="ibox-tools white">
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj pozycję'), ['/task-schema/create', 'category_id'=>$category->id], ['class'=>'white-button add-service']); ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i> ', ['/tasks-schema-cat/update', 'id'=>$category->id], ['class'=>'white-button edit-service-category']); ?>
                                    <?= Html::a(Html::icon('trash'), ['/tasks-schema-cat/delete', 'id' => $category->id], [
                                            'class'=>'delete-category'
                                        ])
                                        ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content">
                        <ul class="todo-list small-list ui-sortable" id="list-<?=$category->id?>">
                        <?php 
                        $i=0;
                        foreach ($category->taskSchemas as $service): 
                            $i++;
                        ?>                    
                            <li class="checklist-item" draggable="true" id="item-<?=$service->id?>">
                                <div class="row">
                                <div class="col-xs-6"><?=$service->name?></div>
                                <div class="col-xs-6" style="text-align:right">
                                <?= Html::a("<i class='fa fa-folder'></i> ".Yii::t('app', 'Zobacz'), ['/task-schema/view', 'id' => $service['id']], [
                                    'class' => 'btn btn-white btn-xs show-service',
                                ])
                                ?>
                                <?= Html::a(Html::icon('pencil')." ".Yii::t('app', 'Edytuj'), ['/task-schema/update', 'id' => $service['id']], [
                                    'class' => 'btn btn-white btn-xs edit-service',
                                ])
                                ?>
                                <?= Html::a(Html::icon('trash')." ".Yii::t('app', 'Usuń'), ['/task-schema/delete', 'id' => $service['id']], [
                                    'class' => 'btn btn-danger btn-xs delete-item',
                                ])
                                ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
            </div>
            </li>
        <?php endforeach; ?>
        </ul>
        </div>
        <div class="col-sm-6 task-schema-details">
        </div>
        </div>
        <div>            
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
            url: '".Url::to(['/tasks-schema-cat/order'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  $('.delete-item').on('click', function(e){
    e.preventDefault();
    deleteItem($(this));
  });
  $('.delete-category').on('click', function(e){
    e.preventDefault();
    data=[];
    deleteCategory($(this));
  })
    ");

foreach($model->tasksSchemaCats as $category):
$this->registerJs("
$( function() {
    $( '#list-".$category->id."').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/task-schema/order'])."'
        });
    }
});
    $( '#list-".$category->id."').disableSelection();
  } );



    ");

endforeach;
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function addNewRow(data)
    {
        $("#list-"+data.tasks_schema_cat_id).append('<li class="checklist-item" draggable="true" id="item-'+data.id+'"><div class="row"><div class="col-xs-9">'+data.name+'</div><div class="col-xs-3" style="text-align:right"><a class="btn btn-primary btn-sm edit-service" href="/admin/task-schema/update?id='+data.id+'"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger btn-sm delete-item" href="/admin/task-schema/delete?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a></div></div></li>');
        $("#item-"+data.id).find('.delete-item').on('click', function(e){
            e.preventDefault();
            data=[];
            deleteItem($(this));
          });
        $("#item-"+data.id).find('.edit-service').on('click', function(e){
            $("#edit-service").find(".modalContent").empty();
            e.preventDefault();
            $("#edit-service").modal("show").find(".modalContent").load($(this).attr("href"));
          });
        $(".task-schema-details").empty().load('/admin/task-schema/view?id='+data.id);
    }


    function editServiceRow(data)
    {
        var $row = $("#item-"+data.id).find(".col-xs-9");
        $row.empty().append(data.name);
        $(".task-schema-details").empty().load('/admin/task-schema/view?id='+data.id);
    }
    function addNewCategoryRow(data)
    {

        $("#list").append('<li class="checklist-item" draggable="true" id="bigitem-'+data.id+'"><div class="ibox float-e-margins"><div class="ibox-title navy-bg"><h5>'+data.name+'</h5><div class="ibox-tools white"><a class="white-button add-service" href="/admin/task-schema/create?category_id='+data.id+'"><i class="fa fa-plus"></i> Dodaj zadanie</a><a class="white-button edit-service-category" href="/admin/tasks-schema-cat/update?id='+data.id+'"><i class="fa fa-pencil"></i> </a><a class="delete-category" href="/admin/tasks-schema-cat/delete-category?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div></div><div class="ibox-content"><ul class="todo-list small-list ui-sortable" id="list-'+data.id+'"></ul></div></div>');
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