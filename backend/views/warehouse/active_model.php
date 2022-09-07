         <?php

use common\components\grid\GridView;
use common\models\GearItem;
use common\models\GearService;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\editable\Editable;
use kartik\dynagrid\DynaGrid;
$user = Yii::$app->user;

                $content = '';
               $content .= GridView::widget([
                   'dataProvider' => $warehouse->gearItemDataProvider,
                   'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
                   'layout' => '{items}',
                   'options'=>[
                        'class'=>'grid-view grid-view-items',
                   ],
                   'rowOptions' => function ($model, $key, $index, $grid)
                   {
                       $options = [];
                        if ($model->group_id != null)
                        {
                            $options['class'] = 'warning';
                        }
                        return $options;

                   },
                    'filterModel' => null,
                    'columns' => [
                        ['class' => 'yii\grid\CheckboxColumn'],
                        //'id',
                        [
                            'header' => Yii::t('app', 'Nazwa'),
                            'format' => 'html',
                            'value' => function ($gear)  use ($user){

                                $content = Html::a($gear->name, ['gear-item/view', 'id'=>$gear->id]) . " ";
                                
                                if ($gear->status == GearItem::STATUS_NEED_SERVICE) {
                                    $service = GearService::getCurrentModel($gear->id);
                                    if ($user->can('gearServiceView')){
                                        $content .= Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color.';']);
                                    }else{
                                        echo "<span class='label' style='color:white; background-color:".$service->serviceStatus->color."'>".$service->serviceStatus->name."</span>";
                                    }
                                }else{
                                if ($gear->status == GearItem::STATUS_SERVICE) {
                                    $service = GearService::getCurrentModel($gear->id);
                                    if ($user->can('gearServiceView')){
                                        $content .= Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                    }else{
                                        echo "<span class='label' style='color:white; background-color:".$service->serviceStatus->color."'>".$service->serviceStatus->name."</span>";
                                    }
                                }else{
                                    if ($user->can('gearServiceUpdate'))
                                        $content .= Html::a(Yii::t('app', 'Wyślj na serwis'), ['/gear-service/create', 'id'=>$gear->id], ['class'=>'label label-primary']);

                                }}
                                return $content;
                            }
                        ],
                        'number:text:'.Yii::t('app', 'Nr'),
                        //'code:text:'.Yii::t('app', 'Kod'),
                        //'serial:text:'.Yii::t('app', 'Nr seryjny'),
                         ['attribute'=>'warehouse_id',
                    'label' => Yii::t('app', 'Magazyn'),
                    'format' => 'html',
                    'value' => function($gear) {
                        if ($gear->warehouse_id)
                        {
                            return $gear->warehouseModel->name;
                        }else{
                            if ( $gear->event_id)
                            {
                                return Html::a($gear->event->name, ['event/view', 'id'=>$gear->event_id], ['target' => '_blank']);
                            }
                            if ( $gear->rent_id)
                            {
                                return Html::a($gear->rent->name, ['rent/view', 'id'=>$gear->rent_id], ['target' => '_blank']);
                            }
                        }
                    }
                ],
                        [
                            'attribute' => 'location',
                            'label' => Yii::t('app', 'Miejsce w<br/>magazynie'),
                            'encodeLabel'=>false,
                        ],
                        [
                            'header' => Yii::t('app', 'Sprawdzony'),
                            'format' => 'raw',
                           'value' => function ($gear) {
                                $date = "";
                                if ($gear->test_date)
                                    $date = " (".date("d.m.Y", strtotime($gear->test_date)).")";
                                if ($gear->tester)
                                    return Html::a($gear->tester.$date, ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'tester'.$gear->id]);
                                else
                                    return Html::a(Yii::t('app', 'b.d.'), ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'tester'.$gear->id]);
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Godziny lamp'),
                            'format' => 'raw',
                            'value' => function ($gear) {
                                if ($gear->lamp_hours)
                                    return Html::a($gear->lamp_hours, ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'lamp'.$gear->id]);
                                else
                                    return Html::a(Yii::t('app', 'b.d.'), ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'lamp'.$gear->id]);
                            },
                        ],
                        [
                            'header' => Yii::t('app', 'Uwagi'),
                            'format' => 'raw',
                            'value' => function ($gear) {
                                if ($gear->info)
                                    return Html::a($gear->info, ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'info'.$gear->id]);
                                else
                                    return Html::a(Yii::t('app', 'b.d.'), ['/gear-item/edit', 'id'=>$gear->id], ['class'=>'change-item', 'id'=>'info'.$gear->id]);
                            },
                        ],
                        /*[
                            'attribute' => 'purchase_price',
                            'label' => Yii::t('app', 'Cena<br/>zakupu'),
                            'encodeLabel'=>false,
                            'visible'=>Yii::$app->user->can('warehouseGearItemPriceView')
                        ],
                        [
                            'attribute' => 'refund_amount',
                            'label' => Yii::t('app', 'Kwota<br/>zwrotu'),
                            'encodeLabel'=>false,
                            'visible'=>Yii::$app->user->can('warehouseGearItemPriceView')
                        ],*/
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visibleButtons' => [
                                'update'=>Yii::$app->user->can('gearItemEdit'),
                                'delete'=>Yii::$app->user->can('gearItemDelete'),
                                'view'=>Yii::$app->user->can('gearItemView'),
                            ],
                            'urlCreator' =>  function($action, $model, $key, $index)
                            {
                                $params = is_array($key) ? $key : ['id' => (string) $key];
                                $params[0] = 'gear-item/' . $action;

                                return Url::toRoute($params);
                            },
                            'template' => '{history} {view} {update} {delete} {service}',
                        ],
                    ],
                ]);
                $content = $this->render('_group', ['checkbox'=>false, 'warehouse'=>$warehouse, 'gearColumns'=>3]).$content;
                $content.= Html::a(Yii::t('app', 'Utwórz case'), ['group-create'], ['class'=>'btn btn-success btn-xs group-create', 'data-pjax'=>0]);
                $content = Html::tag('div', $content, ['class'=>'wrapper']);
echo $content;

$this->registerJs('

$(".change-item").on("click", function(e){
e.preventDefault();
$("#gear-item-modal").modal("show").find(".modalContent").load($(this).attr("href"));
    });
$(".change-item").on("contextmenu",function(){
       return false;
    })
$("a.group-create").on("click", function(e){
    e.preventDefault();
    var n = $(".grid-view-items").length;
    if (n==0)
    {
         alert("'.Yii::t('app', 'Brak zaznaczonych egzemplarzy').'");
        return;       
    }
    var ids = $(".grid-view-items").yiiGridView("getSelectedRows");
    if(ids.length==0) 
    {
        alert("'.Yii::t('app', 'Brak zaznaczonych egzemplarzy').'");
        return;
    }
    var params = $.param({"id[]":ids}, true);
    var url = this.href + "?" + params;
    //window.location = url;
    
    var container = $(this).closest("[data-pjax-container]");
    $.get(url, {}, function(response){
        window.location.href = "/admin/gear-group/update?id="+response;
    });

    
    return false;
});

',\yii\web\View::POS_END, 'group-create-click');

$this->registerJs('
    $(".show-group-items").click(function(e){
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

');