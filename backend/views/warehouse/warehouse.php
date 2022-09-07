<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\components\grid\GridView;
use common\models\GearItem;
use common\models\GearService;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\editable\Editable;
use kartik\dynagrid\DynaGrid;
$warehouses = \common\models\Warehouse::find()->all();
$this->title = $wwarehouse->name;
$user = Yii::$app->user;
use yii\bootstrap\Modal;
use kop\y2sp\ScrollPager;
use kartik\grid\CheckboxColumn;
use backend\modules\permission\models\BasePermission;


$gearTypes = \common\models\Gear::getTypeList();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn łącznie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $wwarehouse->name;
Modal::begin([
    'id' => 'gear-item-modal',
    'header' => Yii::t('app', 'Egzemplarz'),
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
    'id' => 'gear-location',
    'header' => Yii::t('app', 'Edytuj miejsce magazynowe'),
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
    'id' => 'gear-movement',
    'header' => Yii::t('app', 'Przesunięcie magazynowe z ').$wwarehouse->name,
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

<div class="menu-pils">
<?= $this->render('_categoryMenu'); ?>
</div>
<br>
<?php
echo $this->render('_tools', ['warehouse'=>$warehouse, 'w'=>$wwarehouse->id]); ?>
<div class="warehouse-container">
<br/>
    <div class="gear gears">
         <div class="col-md-12">
        <div class="ibox float-e-margins">        
        <?php

        if ($s == null) {
            $visible_order = true;
        }
        else {
            $visible_order = false;
        }

        $gearColumns = [
                    ['class' => CheckboxColumn::className()],
                                [
                'content'=>function($model, $key, $index, $grid) use ($warehouse, $wwarehouse)
                {
                    if ($model->no_items==1)
                    {
                            return Html::icon('ban-circle');
                        

                    }
                    else
                    {

                        $icon = 'arrow-down';
                        $id = $model->id;
                        $view = '';

                        if (Yii::$app->user->can('gearItemView')) {
                            $view = Html::a(Html::icon($icon), ['active-modelw','activeModel' => $id, 'w'=> $wwarehouse->id, 'q'=>Yii::$app->request->get('q', "")], ['class' => $icon." show-items"]);
                        }
                        return $view;
                    }


                },
                'contentOptions'=>['class'=>'text-center', 'style'=>'white-space:nowrap;'],
            ],
            [
                    'label'=>'U',
                    'content'=>function($model)
                    {
                            if ($model->isFavorite())
                            {
                                return Html::a("<i class='fa fa-heart'></i>", ['/gear/favorite', 'id'=>$model->id], ['class'=>'add-favorite btn btn-primary btn-xs']);
                            }
                            else
                            {
                                return Html::a("<i class='fa fa-heart-o'></i>", ['/gear/favorite', 'id'=>$model->id], ['class'=>'add-favorite btn btn-default btn-xs']);
                            }
                    }
            ],
            [
                'label' => Yii::t('app', 'Zdjęcie'),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'70px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
                'visible'=>(Yii::$app->session->get('gear-photos')==1) 
            ],
            [
                'label'=> Yii::t('app', 'Nazwa'),
                'attribute' =>'name',
                'value' => function ($model, $key, $index, $column) use ($warehouse, $wwarehouse, $user) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    if ($model->no_items){
                        if (isset($model->gearItems[0]))
                        {
                            $gear = $model->gearItems[0];
                            if ($user->can('gearServiceUpdate'))
                                $content .= " ".Html::a(Yii::t('app', 'Wyślj na serwis'), ['/gear-service/create', 'id'=>$gear->id], ['class'=>'label label-primary']);
                            if ($user->can('gearWarehouseOutcomes'))
                                $content .= " ".Html::a(Yii::t('app', 'Przesuń'), ['/gear/add-to-move', 'id'=>$model->id, 'w'=>$wwarehouse->id, 'type'=>'gear'], ['class'=>'label label-primary move-gear-no-items']);
                        }
                        
                    }
                        return $content;


                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'max-width:200px'],
                'visible'=>true
            ],
            [
                'label'=> Yii::t('app', 'Typ'),
                'attribute' =>'type',
                'value' => function ($model) use ($gearTypes)
                {
                    if ($model->type)
                        return $gearTypes[$model->type];
                    else
                        return "-";
                }
            ],
            [
                'label' => Yii::t('app', 'Na stanie'),
                'format'=>'raw',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'contentOptions'=>['style'=>'white-space:nowrap;'],
                'value'=>function($gear, $key, $index, $column) use ($wwarehouse)
                {

                    return $wwarehouse->getWQ($gear)->quantity;

                }
            ],

            [
                    'header' => Yii::t('app', 'Uwagi'),
                    'format' => 'html',
                    'contentOptions' => function ($model) {
                        if ($model->getItemsInfo()!="")
                            return ['style'=>'background-color:#ed5565; color:white; white-space:nowrap; cursor_pointer;', 'class' => 'info'];
                        else
                            return [];
                    },
                    'value' => function ($model) {
                                $info = $model->getItemsInfo();
                                if ($info!="")
                                {
                                     $info_div ="<div class='display_none'>".$info."</div><span>".Yii::t('app', 'Pokaż uwagi')."</span>";
                                    return $info_div;                                   
                                }else{
                                    return "";
                                }

                    },                   
            ],
            [
                'header' => Yii::t('app', 'Miejsce'),
                'format' => 'raw',
                'value'=> function ($model) use ($wwarehouse, $user)
                {
                    $content = "<div id='location-div".$model->id."' data-gearid=".$model->id.">".$wwarehouse->getWQ($model)->location."</div>";
                        if ($user->can('gearEdit'))
                        {
                            $content .= " ".Html::a("<i class='fa fa-edit'></i>", ['/warehouse/edit-location', 'id'=>$model->id, 'w'=>$wwarehouse->id], ['class'=>'edit-location']);
                        }
                    return $content;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => function ($model) {
                            return ['style'=>'white-space:nowrap;'];
                    },
                'visibleButtons' => [
                    'update'=>$user->can('gearEdit'),
                    'delete'=>$user->can('gearDelete'),
                    'view'=>$user->can('gearView'),
                ],

                'urlCreator'=>function ($action, $model, $key, $index) {
                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[0] = 'gear/' . $action;

                    return Url::toRoute($params);
                }
            ],
        ];
?>


            <?php
        echo DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'id'=>'warehouse-grid'.$w->id,
            //'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
            'dataProvider' => $provider,
            'filterModel' => null,
            //'layout'=>'{items}',
            'pager' => [
            'class'     => ScrollPager::className(),
            'container' => '.grid-view tbody',
            'item'      => 'tr',
            'paginationSelector' => '.grid-view .pagination',
            'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
                        'eventOnRendered' => 'function() {
                    changeQuantityForm();
            }',
            'enabledExtensions'  => [
                ScrollPager::EXTENSION_SPINNER,
                //ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
        ],
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
        

        
            'toolbar' => [
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
'afterRow' => function($model, $key, $index, $grid) use ($gearColumns, $warehouse)
            {
                        return Html::tag('tr',Html::tag('td', "", ['colspan'=>sizeof($gearColumns)]), ['class'=>'gear-details', 'style'=>"display:none"]);
                    
            },
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-warehouse'],
            
            'columns' => $gearColumns,
        ]);
        ?>

    </div>
            </div>
        </div>



</div>

<?php
$reloadUrl = Url::to(['warehouse/reload-quantity']);
$bookingsUrl = Url::to(['warehouse/reload-bookings']);
$eventGearList = Url::to(['gear/movement', 'w'=>$wwarehouse->id]);
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";

if (Yii::$app->request->get('c', false)=='favorite')
        $storeUrl = Url::to(['warehouse/store-order', 'favorite'=>1]);
else
    $storeUrl = Url::to(['warehouse/store-order']);
$this->registerJs('
    $(".open-gear-movement-list").on("click", function(){ 
    openGearMovementModal();
})

function openGearMovementModal(){
    var modal = $("#gear-movement");
    modal.find(".modalContent").empty();
    modal.find(".modalContent").append("'.$spinner.'");
    modal.find(".modalContent").load("'.$eventGearList.'");
    modal.modal("show");
}



    $(".edit-location").click(function(e){
            e.preventDefault();
            $("#gear-location").find(".modalContent").empty();
            $("#gear-location").modal("show").find(".modalContent").load($(this).attr("href"));
    });

    $(".show-items").click(function(e){
            e.preventDefault();
            if ($(this).hasClass("arrow-down"))
            {
                $(this).removeClass("arrow-down");
                if ($(this).closest("tr").next().find("td").html()=="")
                    $(this).closest("tr").next().slideDown().find("td").empty().load($(this).attr("href"));
                else
                    $(this).closest("tr").next().slideDown();
            }else{
                $(this).addClass("arrow-down");
                $(this).closest("tr").next().slideUp();
            }
            
            
            
    });

$(":checkbox.checkbox-model").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    
    var tr = $(this).closest("tr").next("tr");
    if (tr.hasClass("gear-details"))
    {
        tr.find(":checkbox").prop("checked", add);
    }
    
    return false;
});

$(".add-favorite").on("click", function(e){
        e.preventDefault();
                                if ($(this).hasClass("btn-primary"))
                        {
                               $(this).removeClass("btn-primary");
                               $(this).addClass("btn-default");
                        }else{
                               $(this).addClass("btn-primary");
                               $(this).removeClass("btn-default");
                        }
        $.ajax({
                        url:$(this).attr("href"), 
                        type:"POST",
                        data: [],
                    })
                    .done(function(data){

                    });

});
$(".gear-sort a").on("click", function(e) {
    e.preventDefault();
    var el = $(this);
    var row = el.closest("tr");

    if (el.hasClass("sort-up"))
    {
        var el2 = row.prev("tr");
        if (el2)
        {
            row.insertBefore( el2 );
        }
    }
    else if (el.hasClass("sort-down"))
    {
        var el2 = row.next("tr");
        if (el2)
        {
            row.insertAfter( el2 );
        }
        
    }
    
    var list = $(".gear-sort").map(function(){return $(this).data("id");}).get();
    $.post("'.$storeUrl.'", {data:list, _csrf: yii.getCsrfToken()});
    
    return false;
});
');
?>

<?php
$this->registerJs('
    changeQuantityForm();
function changeQuantityForm(){

    $(".calendar-button").unbind("click");
    $(".move-gear-no-items").unbind("click");
    $(".info").unbind("click");
    $(".calendar-button-all").unbind("click");
    $(".wizja-button-all").unbind("click");
    $(".gear-sort a").unbind("click");
    $(".add-favorite").unbind("click");
    $(".show-items").unbind("click");
    $(".manage-warehouse").unbind("click");
    $(".show-items").click(function(e){
            e.preventDefault();
            if ($(this).hasClass("arrow-down"))
            {
                $(this).removeClass("arrow-down");
                if ($(this).closest("tr").next().find("td").html()=="")
                    $(this).closest("tr").next().slideDown().find("td").empty().load($(this).attr("href"));
                else
                    $(this).closest("tr").next().slideDown();
            }else{
                $(this).addClass("arrow-down");
                $(this).closest("tr").next().slideUp();
            }
            
            
            
    });

        $(".manage-warehouse").click(function(e){
        $("#manage-warehouse").find(".modalContent").empty();
        e.preventDefault();
        $("#manage-warehouse").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".manage-warehouse").on("contextmenu",function(){
       return false;
    });

$(".move-gear-no-items").click(function(e){
e.preventDefault();
var modal = $("#gear-movement");
    modal.find(".modalContent").empty();
    modal.find(".modalContent").append("'.$spinner.'");
    modal.find(".modalContent").load($(this).attr("href"));
    modal.modal("show");
});

$(".add-favorite").on("click", function(e){
        e.preventDefault();
                                if ($(this).hasClass("btn-primary"))
                        {
                               $(this).removeClass("btn-primary");
                               $(this).addClass("btn-default");
                        }else{
                               $(this).addClass("btn-primary");
                               $(this).removeClass("btn-default");
                        }
        $.ajax({
                        url:$(this).attr("href"), 
                        type:"POST",
                        data: [],
                    })
                    .done(function(data){

                    });

});
$(".gear-sort a").on("click", function(e) {
    e.preventDefault();
    var el = $(this);
    var row = el.closest("tr");

    if (el.hasClass("sort-up"))
    {
        var el2 = row.prev("tr").prev("tr");
        if (el2)
        {
            row.insertBefore( el2 );
        }
    }
    else if (el.hasClass("sort-down"))
    {
        var el2 = row.next("tr").next("tr");
        if (el2)
        {
            row.insertAfter( el2 );
        }
        
    }
    
    var list = $(".gear-sort").map(function(){return $(this).data("id");}).get();
    $.post("'.$storeUrl.'", {data:list, _csrf: yii.getCsrfToken()});
    
    return false;
});

$(".info").click(function(){
    $(this).find("span").first().toggleClass("display_none");

    var ourDiv = $(this).find("div").first();
    if (ourDiv.hasClass("display_none")) {
        ourDiv.slideDown();
    }
    else {
        ourDiv.slideUp();
    }
    ourDiv.toggleClass("display_none");
});

$(".calendar-button").click(function(e){
    e.preventDefault();
    wname = "kalendarz"+ $(this).attr("data-id");
    window.open($(this).attr("href"), wname ,"height=500,width=850");
})

$(".calendar-button-all").click(function(e){
    e.preventDefault();
    wname = "kalendarz all";
    var keys = $("#warehouse-grid").yiiGridView("getSelectedRows");
    window.open($(this).attr("href")+"?keys="+JSON.stringify(keys), wname ,"height=500,width=850");
});

$(".wizja-button-all").click(function(e){
    e.preventDefault();
    var keys = $("#warehouse-grid").yiiGridView("getSelectedRows");
    var win = window.open($(this).attr("href")+"?keys="+JSON.stringify(keys), "_blank");
if (win) {
    win.focus();
} else {
    //Browser has blocked it
}
});




}



    /*
var currentEl;
$(document).on("pjax:beforeReplace", function(event, contents, options) {
var key = $(event.relatedTarget).closest("tr").data("key");
  currentEl = contents.find("tr[data-key="+key+"]") ;

  var el = currentEl.next("tr").find(".wrapper");
  
  if (el)
  {
    el.hide();
  }

});

$(".arrow-up").on("click", function(){
    $(this).closest("tr").next("tr").find(".wrapper").slideUp(200);
});
*/
$(document).on("pjax:complete", function() {
  var el = currentEl.next("tr").find(".wrapper");
  if (el) 
  {
   // el.slideDown();
  }
  
});

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');

$this->registerJs('


$(".info").click(function(){
    $(this).find("span").first().toggleClass("display_none");

    var ourDiv = $(this).find("div").first();
    if (ourDiv.hasClass("display_none")) {
        ourDiv.slideDown();
    }
    else {
        ourDiv.slideUp();
    }
    ourDiv.toggleClass("display_none");
});

$(".calendar-button").click(function(e){
    e.preventDefault();
    wname = "kalendarz"+ $(this).attr("data-id");
    window.open($(this).attr("href"), wname ,"height=500,width=850");
})

$(".calendar-button-all").click(function(e){
    e.preventDefault();
    wname = "kalendarz all";
    var keys = $("#warehouse-grid").yiiGridView("getSelectedRows");
    window.open($(this).attr("href")+"?keys="+JSON.stringify(keys), wname ,"height=500,width=850");
})

');
$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');

$this->registerJs('

'); 
?>

