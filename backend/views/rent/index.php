<?php

use common\models\GearGroup;
use common\models\GearItem;
use common\models\Gear;
use common\models\OuterGear;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use kartik\editable\Editable;
use kartik\dynagrid\DynaGrid;
use kartik\helpers\Enum;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wypożyczenia');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="rent-index">

    <p>
        <?php if ($user->can('eventRentsAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' .  Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        }
        ?>
<?php 
        $sectionList = [Yii::t('app', 'Suma')=>Yii::t('app', 'Suma'), Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa')];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        }
        ?>
        <?= Html::dropDownList(null, Yii::t('app', 'Suma'), $sectionList, ['class' => 'changeSection form-control pull-right', 'style'=>' width:200px']) ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">

        <?php
        $columns = [
            ['class' => CheckboxColumn::className()],
            ['class' =>SerialColumn::className()],
            ];
        if ($user->can('eventEventEditPencil'))
        {
            $columns[] = [
                    'attribute'=>'status',
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Rent::getStatusList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'visible'=>$user->can('eventEventEditPencil'),
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2,
                            'name'=>'status',
                            'formOptions' => [
                                    'action'=>['/rent/status', 'id'=>$model->id],
                                ],
                                'options' => [
                                    'data'=>\common\models\Rent::getStatusList($model->status),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ]
                        ];
                    },
                    'value' => function($model, $key, $index, $column)
                    {
                        return $model->getStatusButton();
                    },
                ];
        }else{
            $columns[] = [
                    'attribute'=>'status',
                    'format' => 'html',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter'=>\common\models\Rent::getStatusList(),
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                        ],
                    ],
                    'visible'=>false,
                    'value' => function($model, $key, $index, $column)
                    {
                        return $model->getStatusButton();
                    },
                ];
        }
                
        $columns[] = 
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Wypożyczenie'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name.' ['.$model->code.']', ['view', 'id' => $model->id]);
                    return $content;
                },
            ];
        $columns[] = 
            [
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->customer)
                    {
                        $content = Html::a($model->customer->displayLabel, ['/customer/view', 'id' => $model->customer->id]);
                        return $content;
                    }else{
                        return "-";
                    }

                },
                'filter' => \common\models\Customer::getList(),
                'attribute' => 'customer_id',
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ];
        $columns[] = 
            [
                'value'=>'manager.displayLabel',
                'filter' => \common\models\User::getList(),
                'attribute' => 'manager_id',
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ];
        if ($user->can('eventsEventEditEyeFinance')){
        $columns[] = 
            [
                'label'=>Yii::t('app', 'Wartość'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventValueAll();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'visible'=>$user->can('eventsEventEditEyeFinance'),
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ]; }
        $columns[] = 
            [
                'attribute'=>'start_time',
                'value'=> function($model)
                {
                    return substr($model->start_time, 0, 16);
                },
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center']
            ];
        $columns[] = 
            [
                'attribute'=>'end_time',
                'value'=> function($model)
                {
                    return substr($model->end_time, 0, 16);
                },
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center']
            ];

            $columns[] = 'days';
            if ($user->can('eventsEventEditEyeFinance')){
            $columns[] = 
            [
                'label'=>Yii::t('app', 'FV'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $value= "";
                    foreach ($model->invoices as $invoice)
                    $value .= "<a href='/admin/finances/invoice/view?id=".$invoice->id."'>".$invoice->fullnumber."</a> ";
                    return $value;
                },
                'visible'=>$user->can('eventsEventEditEyeFinance')
            ];
            }
            $columns[] = 
            [
                'label' =>  Yii::t('app', 'Niezwrócony sprzęt'),
                'format'=>'html',
                'value' => function ($model) {
                   $not_returned_gear = '';
                    $gear_our_outt = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$model->id])->andWhere(['>', 'quantity', 0])->all();

                    foreach ($gear_our_outt as $gear)
                    {
                        $gear_model = Gear::findOne($gear->gear_id);
                        if ($gear_model->no_items)
                        {
                                $not_returned_gear .= "<div style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name  . "</div>";

                        }else{
                                
                                $numbers = GearItem::find()->where(['gear_id'=>$gear_model->id, 'rent_id'=>$model->id])->orderBy(['number'=>SORT_ASC])->all();
                                $num = "";
                                foreach ($numbers as $n)
                                {
                                    if ($num!="")
                                        $num.=", ";
                                    $num .=$n->number;
                                }
                                $not_returned_gear .= "<div style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name."[".$num."]</div";
                        }
                    }
                    $result = $not_returned_gear;
                    if ($result != '') {
                        $result =  "<div class='display_none'>".$result."</div><span>". Yii::t('app', 'Niezwrócony sprzęt')."</span>";
                    }
                    return $result;
                },
                'contentOptions' => function ($model) {
                    if ($model->countNotReturnedGears() > 0) {
                        return ['style'=>'background-color:red; color:white;', 'class' => 'outer_gear'];
                    }
                    return [];
                }
            ];
            $columns[] = 
            [
                'class' => \common\components\ActionColumn::className(),
                'visibleButtons' => [
                    'view' => $user->can('eventRentsView'),
                    'update' => $user->can('eventRentsEdit'),
                    'delete' => $user->can('eventRentsDelete'),
                ]
            ];
            $columns[] = 
            [
                'label' => Yii::t('app', 'Wydaj z magazynu'),
                'content' =>  function($model) use ($user) {
                    if ($user->can('eventRentsMagazin')) {
                        return Html::a(Html::icon('log-out'), ['outcomes-warehouse/create',
                                'rent' => $model->id]) . "<br>" . Html::a(Html::icon('log-in'), ['incomes-warehouse/create',
                                'rent' => $model->id, 'onlyEvent'=>1]);
                    }
                }
            ];
        ?>

    <?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => true,

            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
                'id'=>'rents-grid',

        
            'toolbar' => [
                [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')+1), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-rents'],
        'columns' => $columns,
    ]); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs('

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

$(".outer_gear").click(function(){
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


');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}
');

$this->registerJs('

$(".value-div").hide();
$("."+$(".changeSection").val()+"-div").show();
$(".changeSection").change(function(){
    $(".value-div").hide();
    $("."+$(this).val()+"-div").show();
    sumTable();
});

function sumTable(){
    
    var keys = $("#rents-grid").yiiGridView("getSelectedRows");
    var totals = [0];
    
    var $dataRows = $("tbody tr");
    $dataRows.each(function(){
    
        $(this).find(".sum-cell").each(function(i){
            var currentKey = $(this).closest("tr").data("key");
            var sumRow = false;
            
            // for all rows or selected 
            if (keys.length<1 || $.inArray(currentKey, keys)!=-1) {
                sumRow = true;
            }
            
            if (sumRow==true) {
                var val = $(this).html();
                var el2 = $(this).find("."+$(".changeSection").val()+"-div");
               
                if (el2.length) {
                    val = el2.html();
                }else{
                    val = "0";
                }
                
                if ("'.Yii::$app->formatter->decimalSeparator.'".length > 0) {
                    val = val.replace("'.Yii::$app->formatter->decimalSeparator.'", ".");
                }
                val = val.replace("'.Yii::$app->formatter->thousandSeparator.'", "");
                val = val.replace(/[^0-9.,]+/ig, "");
                val = val.replace(",", ".");
                totals[i] += parseFloat(val);
            }
            
        });
    });
    
    var x = 5;
    var y = 6;
    labels = [];
    labels[0] = "Wartość";

    for(var j=x;j<y; j++) {

        $(".kv-page-summary td").eq(j).html(labels[j-x]+": "+totals[j-x].toFixed(2));
        $(".kv-page-summary td").eq(j).css("white-space", "nowrap");
    }
    
}

sumTable();');