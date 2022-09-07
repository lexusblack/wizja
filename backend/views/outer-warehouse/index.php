<?php
/* @var $this \yii\web\View */

use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\dynagrid\DynaGrid;

$this->title = (Yii::$app->session->get('company')==1)? Yii::t('app', 'Magazyn zewnętrzny') : Yii::t('app', 'Usługi zewnętrzne');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>

<div class="menu-pils">
<?= $this->render('_categoryMenu'); ?>
</div><br/>

<?= $this->render('_tools');

Pjax::begin(); ?>
<div class="row">
    <div class="tools warehouse-tools col-md-12">
        <?php
        if ($user->can('outerGearCreate')) {
            echo Html::a( Yii::t('app', 'Dodaj model'), ['outer-gear-model/create'], ['class'=>'btn btn-success btn-xs gear-create']);
        }
        if ($user->can('gearOurWarehouseAddFromGearBase')) {
            echo Html::a(Yii::t('app', 'Dodaj z bazy sprzętu'), ['gear-model/index?t=outer'], ['class' => 'btn btn-success btn-xs gear-create']);
        } ?>

    </div>
</div>

<div class="warehouse-container">

    <div class="gear gears">
        <div class="panel_mid_blocks">
            <div class="panel_block" style="margin-bottom: 0;">

            </div>
        <?php
        $types = \common\models\Gear::getTypeList();

        $visible_order = false;
        if ($s == null) {
            $visible_order = true;
        }

        $gearColumns = [
            [
                'content'=>function($model, $key, $index, $grid) use ($activeModel)
                {
                        $addItem = '';
                        if (Yii::$app->user->can('gearItemCreate')) {
                            $addItem = Html::a(Html::icon('plus'), ['outer-gear/create', 'outerGearModelId' => $model->id], ['class' => 'gear-item-create']);
                        }
                        $icon = $activeModel==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeModel==$model->id ?  null : $model->id;
                        $view = '';

                        if (Yii::$app->user->can('gearItemView')) {
                            $view = Html::a(Html::icon($icon), ['active-model', 'activeModel' => $id], ['class' => $icon." show-items"]);
                        }
                        return $addItem.'<br />'.$view;


                },
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'label'=> Yii::t('app', 'S'),
                'attribute'=>'sort_order',
                'content'=>function($model, $key, $index, $grid) use ($activeModel)
                {
                    return Html::a(Html::icon('chevron-up'), '#', ['class'=>'sort-up']).Html::tag('br').Html::a(Html::icon('chevron-down'), '#', ['class'=>'sort-down']);;


                },
                'contentOptions'=>function ($model, $key, $index, $column){
                        return [
                            'class'=>'text-center gear-sort',
                            'data-id'=>$model->id
                        ];
                },
                'visible' => ($visible_order && $user->can('gearOuterWarehouseMove')),
            ],
            [
                    'label'=>'U',
                    'content'=>function($model)
                    {
                            if (isset($model->outerGearFavorite))
                            {
                                return Html::a("<i class='fa fa-heart'></i>", ['/outer-gear-model/favorite', 'id'=>$model->id], ['class'=>'add-favorite btn btn-primary btn-xs']);
                            }
                            else
                            {
                                return Html::a("<i class='fa fa-heart-o'></i>", ['/outer-gear-model/favorite', 'id'=>$model->id], ['class'=>'add-favorite btn btn-default btn-xs']);
                            }
                    }
            ],
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = $model->name;
                    if (Yii::$app->user->can('outerWarehouseUpdate'))
                        $content = Html::a($model->name, ['outer-gear-model/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label'=> Yii::t('app', 'Typ'),
                'attribute' =>'type',
                'value' => function ($model) use ($types)
                {
                    return $types[$model->type];
                }
            ],
            [
                'format' => 'html',
                'header' => Yii::t('app', 'Firma'),
                'value' => function ($model) {
                        return $model->getItemsCompany();
                                

                    },
            ],
            [
                'format' => 'html',
                'header' => Yii::t('app', 'Sztuk'),
                'value' => function ($model) {
                        return $model->getQuantity();
                                

                    },
            ],
            [
                'class' => 'yii\grid\ActionColumn',

                'urlCreator'=>function ($action, $model, $key, $index) {
                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[0] = 'outer-gear-model/' . $action;

                    return Url::toRoute($params);
                },
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'view'=>$user->can('outerGearView'),
                    'update'=>$user->can('outerGearUpdate'),
                    'delete'=>$user->can('outerGearDelete'),
                ],
            ],
            ]; ?>
    <div class="panel_mid_blocks">
        <div class="panel_block">
<?php
        echo DynaGrid::widget([
        'gridOptions'=>[

            'dataProvider' => $gearDataProvider,
            'filterModel' => null,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
        

        
            'toolbar' => [
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
'afterRow' => function($model) use ($gearColumns, $activeModel, $itemProvider, $user) {
                $content = '';


                return Html::tag('tr',Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]), ['class'=>'gear-details', 'style'=>"display:none"]);
            },
        ],
        
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-outer-warehouse'],
            
            'columns' => $gearColumns,
        ]);
        ?>

    </div>
    </div>
    </div>



</div>

<?php
if (Yii::$app->request->get('c', false)=='favorite')
        $storeUrl = Url::to(['outer-gear-model/store-order', 'favorite'=>1]);
else
    $storeUrl = Url::to(['outer-gear-model/store-order']);
$this->registerJs('

    $(".show-items").click(function(e){
            e.preventDefault();
            if ($(this).hasClass("arrow-down"))
            {
                $(this).removeClass("arrow-down");
                $(this).closest("tr").next().slideDown().find("td").empty().load($(this).attr("href"));
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

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');

?>
<?php Pjax::end();

$this->registerJs('

    $("object").each(function(){
        var data = $(this).attr("data");
        var name = $(this).parent().data("name");
           
        $(this).wrap("<a href=\'" + data + "\' download=\'" + name + ".bmp\'></a>");
    });

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');