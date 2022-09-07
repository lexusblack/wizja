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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schematy oferty'), 'url' => ['/offer-schema/index']];
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'id' => 'new-service',
    'header' => Yii::t('app', 'Dodaj usługę'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'new-service-category',
    'header' => Yii::t('app', 'Dodaj grupę usług'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'edit-service-category',
    'header' => Yii::t('app', 'Edytuj grupę usług'),
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

?>

<div class="panel_mid_blocks">
        <div class="panel_block">
        <div>
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj grupę usług'), ['/service-category/create', 'schema_id'=>$model->id], ['class'=>'btn btn-primary add-service-category'])." "; ?>            
        </div>
        <ul class="todo-list ui-sortable" id="list">
            <?php foreach($categories as $category): 
                            if ($category->in_offer)
                            {
                                $class = "in-offer";

                            }else{
                                $class = "not-in-offer";
                            }
            ?>
            <li class="checklist-item" draggable="true" id="bigitem-<?=$category->id?>">
            <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg <?=$class?>" style="background-color:<?=$category->color?>">
                            <h5><?=$category->name?></h5>
                                    <div class="ibox-tools white">
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj pozycję'), ['/service/create', 'category_id'=>$category->id], ['class'=>'white-button add-service']); ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i> ', ['/service-category/update', 'id'=>$category->id], ['class'=>'white-button edit-service-category']); ?>
                                    <?= Html::a(Html::icon('trash'), ['/service-category/delete-category', 'id' => $category->id], [
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
                        foreach ($category->services as $service): 
                            $text = Yii::t('app', 'Ustaw widoczność');
                            $i++;
                            if ($service->in_offer)
                            {
                                $class = "in-offer";
                                $class2 = "fa fa-eye-slash";

                            }else{
                                $class = "not-in-offer";
                                $class2 = "fa fa-eye";
                            }
                        ?>                    
                            <li class="checklist-item <?=$class?>" draggable="true" id="item-<?=$service->id?>">
                                <div class="row">
                                <div class="col-xs-9"><?=$service->name?></div>
                                <div class="col-xs-3" style="text-align:right">
                                <?= Html::a('<i class="'.$class2.'"></i>', ['/service/visible', 'id' => $service['id']], [
                                    'class' => 'btn btn-sm change-visible',
                                    'title'=>$text,
                                ])
                                ?>
                                <?= Html::a(Html::icon('trash'), ['/service/delete-service', 'id' => $service['id']], [
                                    'class' => 'btn btn-danger btn-sm delete-item',
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
            url: '".Url::to(['/service-category/order'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  $('.change-visible').on('click', function(e){
    e.preventDefault();
    data=[];
    $.post($(this).attr('href'), data, function(response){
        changeRow(response);
    });
  });
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

foreach($categories as $category):
$this->registerJs("
$( function() {
    $( '#list-".$category->id."').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/service/order'])."?id=<?=$category->id?>'
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
        var $class = "";
        if (data.in_offer=="1")
        {
            $class="in-offer";
        }else{
            $class="not-in-offer";
        }
        $("#list-"+data.service_category_id).append('<li class="checklist-item '+$class+'" draggable="true" id="item-'+data.id+'"><div class="row"><div class="col-xs-9">'+data.name+'</div><div class="col-xs-3" style="text-align:right"><a class="btn btn-sm change-visible" href="/admin/service/visible?id='+data.id+'" title="Ustaw widoczność"><i class="fa fa-eye-slash"></i></a><a class="btn btn-danger btn-sm delete-item" href="/admin/service/delete-service?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a></div></div></li>');
        $("#item-"+data.id).find('.change-visible').on('click', function(e){
            e.preventDefault();
            data=[];
            $.post($(this).attr('href'), data, function(response){
                changeRow(response);
            });
          });
        $("#item-"+data.id).find('.delete-item').on('click', function(e){
            e.preventDefault();
            data=[];
            deleteItem($(this));
          });
    }
    function addNewCategoryRow(data)
    {
        var $class = "";
        if (data.in_offer=="1")
        {
            $class="in-offer";
        }else{
            $class="not-in-offer";
        }
        $("#list").append('<li class="checklist-item" draggable="true" id="bigitem-'+data.id+'"><div class="ibox float-e-margins"><div class="ibox-title navy-bg '+$class+'"><h5>'+data.name+'</h5><div class="ibox-tools white"><a class="white-button add-service" href="/admin/service/create?category_id='+data.id+'"><i class="fa fa-plus"></i> Dodaj pozycję</a><a class="white-button edit-service-category" href="/admin/service-category/update?id='+data.id+'"><i class="fa fa-pencil"></i> </a><a class="delete-category" href="/admin/service-category/delete-category?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div></div><div class="ibox-content"><ul class="todo-list small-list ui-sortable" id="list-'+data.id+'"></ul></div></div>');
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
        if (data.in_offer==1)
        {
            $row.removeClass("not-in-offer");
            $row.addClass("in-offer");
        }else{
            $row.removeClass("in-offer");
            $row.addClass("not-in-offer");
        }
        $row.find('h5').empty().append(data.name);
        $row.css("background-color", data.color);

    }
    function changeRow(data)
    {
        $row = $("#item-"+data.id);
        $icon = $row.find("i");
        if (data.in_offer==1)
        {
            $row.removeClass("not-in-offer");
            $row.addClass("in-offer");
            $icon.removeClass("fa-eye");
            $icon.addClass("fa-eye-slash");
        }else{
            $row.removeClass("in-offer");
            $row.addClass("not-in-offer");
            $icon.removeClass("fa-eye-slash");
            $icon.addClass("fa-eye");           
        }
        

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