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
$warehouses = \common\models\Warehouse::find()->orderBy(['position'=>SORT_ASC])->all();
$this->title = Yii::t('app', 'Magazyn wewnętrzny');
$user = Yii::$app->user;
use yii\bootstrap\Modal;
use kop\y2sp\ScrollPager;
use kartik\grid\CheckboxColumn;
use backend\modules\permission\models\BasePermission;


$gearTypes = \common\models\Gear::getTypeList();

Modal::begin([
    'id' => 'on-events',
    'header' => Yii::t('app', 'Gdzie jest sprzęt'),
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
    'id' => 'manage-warehouse',
    'header' => Yii::t('app', 'Zarządzaj sprzętem w magazynach'),
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
?>

<div class="menu-pils">
<?= $this->render('_categoryMenu'); ?>
</div>
<br>
<?php
echo $this->render('_tools', ['warehouse'=>$warehouse, 'w'=>null]); ?>

    <div class="row">
        <div class="col-md-12">
        <div class="ibox float-e-margins">
        <?php

echo $this->render('_actionButtons');
if (!(Yii::$app->request->get('q', false)))
    if ($gearSets)
            echo $this->render('_sets', ['gearSet'=>$gearSets, 'event'=>null, 'type'=>0, 'category'=>$category]);
?>
</div>
</div>
</div>
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
                'label'=>Yii::t('app', ''),
                'class'=>\kartik\grid\EditableColumn::className(),
                'attribute'=>'sort_order',
                'editableOptions' => function ($model, $key, $index) {
                                return [
                                    'header' => Yii::t('app', 'Pozycja w kategorii'),
                                    'name'=>'sort_order',
                                    'formOptions' => [
                                            'action'=>['/warehouse/sort-order', 'id'=>$model->id, 'c'=>Yii::$app->request->get('c', false)],
                                        ],
                                    'pluginEvents' =>   [ 
                                        "editableSuccess"=>"function(event, val, form, data) { location.reload();}",
                                    ]
                                ];
                },
                'content'=>function($model, $key, $index, $grid)
                {
                    if (Yii::$app->request->get('c', false)=='favorite')
                    {
                            return $model->gearFavorite->position;
                    }else{
                        $ord = $model->sort_order;
                            return $ord;
                    }
                    


                },
                'visible' => (($visible_order && $user->can('gearOurWarehouseMoveGear'))&&(Yii::$app->request->get('c', false)!='favorite'))

            ],

            [
                'label'=>Yii::t('app', 'S'),
                'attribute'=>'sort_order',

                'content'=>function($model, $key, $index, $grid)
                {
                    return Html::a(Html::icon('chevron-up'), '#', ['class'=>'sort-up']).Html::a(Html::icon('chevron-down'), '#', ['class'=>'sort-down']);


                },
                'contentOptions'=>function ($model, $key, $index, $column) {
                        return [
                            'class'=>'text-center gear-sort',
                            'data-id'=>$model->id,
                            'style'=>'white-space:nowrap;'
                        ];
                },
                'visible' => ($visible_order && $user->can('gearOurWarehouseMoveGear'))

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
                'content'=>function($model, $key, $index, $grid) use ($warehouse)
                {
                    $activeModel = $warehouse->activeModel;
                    if ($model->no_items==1)
                    {
                        if ($model->type==3)
                        {
                            return Html::a(Html::icon('plus'), ['gear-purchase/create', 'gear_id' => $model->id], ['class' => 'gear-item-create']);
                        }else{
                            return Html::icon('ban-circle');
                        }
                        

                    }
                    else
                    {
                        $addItem = '';
                        if (Yii::$app->user->can('gearItemCreate')) {
                            $addItem = Html::a(Html::icon('plus'), ['gear-item/create', 'gearId' => $model->id], ['class' => 'gear-item-create']);
                        }

                        if ($model->getGearItems()->count() == 0)
                        {

                                return $addItem; //po co rozwijać, jak nie ma
                        }
                        $icon = $activeModel==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeModel==$model->id ?  null : $model->id;
                        $view = '';

                        if (Yii::$app->user->can('gearItemView')) {
                            $view = Html::a(Html::icon($icon), ['active-model','activeModel' => $id, 'c'=>2], ['class' => $icon." show-items"]);
                        }
                        return $addItem.' '.$view;
                    }


                },
                'contentOptions'=>['class'=>'text-center', 'style'=>'white-space:nowrap;'],
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
                'value' => function ($model, $key, $index, $column) use ($warehouse) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    if ($model->no_items){
                        if (isset($model->gearItems[0]))
                        {
                            $gear = $model->gearItems[0];
                            $content .= " ".Html::a(Yii::t('app', 'Wyślj na serwis'), ['/gear-service/create', 'id'=>$gear->id], ['class'=>'label label-primary']);
                        }
                        
                    }
                        return Html::a(Html::icon(' fa fa-calendar'), ['/gear/calendar', 'id'=>$model->id, 'start'=>$warehouse->from_date, 'end'=>$warehouse->to_date], ['class'=>'calendar-button btn btn-xs btn-success', 'data-id'=>$model->id])." ".Html::a(Html::icon(' fa fa-table'), ['/gear/wizja', 'id'=>$model->id], ['class'=>'btn btn-xs btn-primary', 'data-id'=>$model->id, 'target'=>'_blank'])." ".$content;


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
                'label'=> Yii::t('app', 'Kategoria'),
                'attribute' =>'category_id',
                'value' => function ($model) use ($gearTypes)
                {
                    return $model->category->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Na stanie'),
                'format'=>'raw',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'contentOptions'=>['style'=>'white-space:nowrap;'],
                'value'=>function($gear, $key, $index, $column) use ($warehouses, $user)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items)
                    {
                        $return = $gear->quantity;
                        $q = $gear->quantity;
                        
                    }
                    else
                    {
                        $return = $gear->getGearItems()->andWhere(['active'=>1])->count();
                        $q = $return;
                    }
                   
                    if ($warehouses)
                    {
                         $total = 0;
                    }
                            foreach ($warehouses as $w)
                            {
                                $return .= $w->getNumberLabel($gear);
                                $total += $w->getNumber($gear);
                            }
                            $e = $gear->getOnEvents();
                            $total+=$e;
                            if ($e)
                            {
                                $return .= "<br/>".Html::a("<span class='label label-primary' data-gearid='".$gear->id."' style='padding:1px; background-color:#555'>".$e."</span> ".Yii::t('app', 'Na eventach'), ['/gear/show-events', 'id'=>$gear->id], ['class'=>" on-events"]);
                            }
                            $left = $q-$total;
                            if ($left>0)
                            {
                                $return .= "<br/><span class='label label-primary' style='padding:1px; background-color:#000'>".$left."</span> ".Yii::t('app', 'Nieprzypisane');
                            }
                            if ($user->can("gearWarehouseQuantityEdit"))
                                    $return .= " ".Html::a("<i class='fa fa-pencil'></i>".Yii::t('app', 'Edytuj'), ['manage-warehouse', 'gear_id'=>$gear->id], ['class'=>'manage-warehouse', 'style'=>'padding:2px;']);
                            
                    
                    return $return;

                }
            ],
            
            [
                'label' => Yii::t('app', 'Dostępnych'),
                'format' => 'html',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'value'=>function($gear, $key, $index, $column) use ($warehouse)
                {
                    if ($gear->type!=1)
                    {
                        return $gear->quantity;
                    }                  
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $serwis = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        return $gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber . " " . $serwis;
                    }
                    else
                    {
                        $serwisNumber = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_SERVICE])->count();
                        $needSerwis = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_NEED_SERVICE])->count();

                        $serwis = null;
                        $need = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        if ($needSerwis > 0) {
                            $need = Html::tag('span', Yii::t('app', 'Wymaga serwisu').': ' . $needSerwis, ['class' => 'label label-warning']);
                        }
                        return ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber) . " " . $serwis." ".$need;
                    }
                }
            ],
            /*
            [
                'label' => Yii::t('app', 'Dostępnych'),
                'format' => 'raw',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'value'=>function($gear, $key, $index, $column) use ($warehouse)
                {
                    return "<div class='quantity-div' data-gearid=".$gear->id."></div>";
                }
            ],
            */
            
            [
                'label' => Yii::t('app', 'Rezerwacje'),
                'format' => 'html',
                'contentOptions' => function ($model) {
                            return ['style'=>'white-space:nowrap;'];
                    },
                'value' => function ($model, $key, $index, $column) use ($warehouse, $user) {
                    $return = "";
                    if ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL]))
                    {


                    $gears = $model->getEvents($warehouse->from_date, $warehouse->to_date);
                    
                    foreach ($gears['events'] as $g)
                    {
                        $return .=Html::a($g->packlist->event->name." - ".$g->quantity." ".Yii::t('app', 'szt. '), ['/event/view', 'id'=>$g->packlist->event->id]).substr($g->start_time, 0, 10)." - ".substr($g->end_time, 0, 10)."<br/>";
                    }
                    foreach ($gears['rents'] as $g)
                    {
                        $return .=Html::a($g->rent->name." - ".$g->quantity." ".Yii::t('app', 'szt.'), ['/rent/view', 'id'=>$g->rent->id]).substr($g->start_time, 0, 10)." - ".substr($g->end_time, 0, 10)."<br/>";
                    }
                    }else{
                        $return = "-";
                    }
                    return $return;

                },
                'visible'=> ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL])),
            ],

            [
                'label' =>Yii::t('app', 'Cena'),
                'visible'=> ($user->can('gearWarehousePrices')),
                'format'=>'raw',
                'value'=> function ($model){
                    $prices = $model->getOfferPrices();
                    $content = "";
                    foreach ($prices as $price)
                    {
                        $content .=$price->gearsPrice->name.":<strong>".$price->price.$price->gearsPrice->currency."</strong></br>";
                    }
                    return $content;
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
                    'header' => Yii::t('app', 'Konflikty'),
                    'format' => 'raw',
                    'value' => function ($model)  use ($warehouse){
                                $conflicts = $model->getConflicts($warehouse->from_date, $warehouse->to_date);
                                if ($conflicts)
                                    $content= Html::tag('span', $conflicts, ['class' => 'label label-danger']);
                                else
                                    $content= Html::tag('span', $conflicts, ['class' => 'label label-primary']);
                                return $content;

                    }, 
            ],
            [
                    'header' => Yii::t('app', 'Cross Rental Network'),
                    'format' => 'raw',
                    'value' => function ($model) use ($user){
                                $count = $model->getCrossRental();
                                if ($count)
                                {
                                    if ($user->can('gearCrossRentalDelete'))
                                        return Yii::t('app', "Udostępniasz")." ".$count.Yii::t('app', "szt.")."<br/><a href='/admin/cross-rental/delete?gear_id=".$model->id."'>".Yii::t('app', 'Przestań udostępniać')."</a>";
                                    else
                                        return "Udostępniasz ".$count."szt.";
                                }else{
                                    if ($user->can('gearCrossRentalCreate'))
                                        return "<a href='/admin/cross-rental/create?gear_id=".$model->id."'>".Yii::t('app', 'Udostępnij')."</a>";
                                    else
                                        return "-";
                                }

                    },                   
            ],
            'warehouse',
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
            'id'=>'warehouse-grid',
            //'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
            'dataProvider' => $warehouse->getGearDataProvider(),
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
                    if ($warehouse->activeModel)
                    {
                        return Html::tag('tr',Html::tag('td', $this->render('active_model', ['warehouse'=>$warehouse]), ['colspan'=>sizeof($gearColumns)]), ['class'=>'gear-details']);
                    }else{
                        return Html::tag('tr',Html::tag('td', "", ['colspan'=>sizeof($gearColumns)]), ['class'=>'gear-details', 'style'=>"display:none"]);
                    }
                    
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
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";

if (Yii::$app->request->get('c', false)=='favorite')
        $storeUrl = Url::to(['warehouse/store-order', 'favorite'=>1]);
else
    $storeUrl = Url::to(['warehouse/store-order']);
$this->registerJs('

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
    $(".on-events").click(function(e){
        e.preventDefault();
        $("#on-events").find(".modalContent").empty();
        e.preventDefault();
        $("#on-events").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');
?>

<?php
$this->registerJs('
function changeQuantityForm(){
    $(".on-events").unbind("click");
    $(".calendar-button").unbind("click");
    $(".info").unbind("click");
    $(".calendar-button-all").unbind("click");
    $(".wizja-button-all").unbind("click");
    $(".gear-sort a").unbind("click");
    $(".add-favorite").unbind("click");
    $(".show-items").unbind("click");
    $(".manage-warehouse").unbind("click");
    $(".on-events").click(function(e){
        e.preventDefault();
        $("#on-events").find(".modalContent").empty();
        e.preventDefault();
        $("#on-events").modal("show").find(".modalContent").load($(this).attr("href"));
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

     $(".manage-warehouse").click(function(e){
        $("#manage-warehouse").find(".modalContent").empty();
        e.preventDefault();
        $("#manage-warehouse").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".manage-warehouse").on("contextmenu",function(){
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
    $(".manage-warehouse").click(function(e){
        $("#manage-warehouse").find(".modalContent").empty();
        e.preventDefault();
        $("#manage-warehouse").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".manage-warehouse").on("contextmenu",function(){
       return false;
    });
    reloadAvability();
'); 
?>

<script type="text/javascript">

        function reloadAvability()
        {
            $(".quantity-div").each(function(){
                $(this).empty();
                gear_id = $(this).data("gearid");
                start = "<?=$warehouse->from_date?>";
                end = "<?=$warehouse->to_date?>";
                data = [];
                $(this).append("<?=$spinner?>");
                var qdiv = $(this);
                $.post("<?=$reloadUrl?>"+"?gear_id="+gear_id+"&start="+start+"&end="+end, data, function(response){
                qdiv.empty();
                qdiv.append(response); 
                }); 
            });
            $(".bookings").each(function(){
                $(this).empty();
                gear_id = $(this).data("gearid");
                start = "<?=$warehouse->from_date?>";
                end = "<?=$warehouse->to_date?>";
                data = [];
                $(this).append("<?=$spinner?>");
                var bdiv = $(this);
                $.post("<?=$bookingsUrl?>"+"?gear_id="+gear_id+"&start="+start+"&end="+end, data, function(response){
                bdiv.empty();
                bdiv.append(response); 
                }); 
            });
        }
</script>
