<?php

use common\models\Invoice;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
use common\components\grid\LabelColumn;
use kartik\helpers\Enum;
use common\helpers\Inflector;
use kartik\dynagrid\DynaGrid;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $model \common\models\Event; */
Modal::begin([
    'id' => 'new-payment',
    'header' => Yii::t('app', 'Dodaj płatność'),
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
/* @var $this yii\web\View */
/* @var $searchModel common\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\common\assets\CustomNumberFormatAsset::register($this);

$this->title = Yii::t('app', 'Faktury');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

$inlineSettings = [
    'templateBefore'=>'<div class="kv-editable-form-inline">
    <div class=" text-right">
        {close}
    </div>',
    'templateAfter'=>'<div class="form-group">
        {buttons}
    </div>
</div>',
];
?>
<div class="invoice-index">
    <?php echo $this->render('_nav', ['model'=>$searchModel]);
    $months = Enum::monthList();
    $months = array_merge([Yii::t('app', 'Wszystkie')], $months);  ?>
    <div class="row">
    <div class="panel-body">
        <div class="title_box row">
            <div class="col-lg-4">
            <form class="form-inline">
                <?php echo Html::a(Html::icon('arrow-left'), ['index', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['index', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                </form>
                <?php echo Html::activeHiddenInput($searchModel, 'useRange', ['class'=>'grid-filter', 'id'=>'date-use-range']); ?>
            </div>
            <div class="col-lg-3">
                    <?php
                    $this->registerJs('
                        var url = "'.Url::current(['m'=>null, 'y'=>null, 'date-use-range'=>null]).'";
                        $(".date-drop").on("change", function(e){
                            if(url.indexOf("?")>0)
                            {location.href=url+"&m="+$("#month").val()+"&y="+$("#year").val();}
                                else
                            {location.href=url+"?m="+$("#month").val()+"&y="+$("#year").val();}
                        });
                    ');
                    ?>
                <?php echo \kartik\daterange\DateRangePicker::widget([
                    'options' => ['class'=>' form-control'],
                    'model' => $searchModel,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'startAttribute' => 'dateStart',
                    'endAttribute' => 'dateEnd',
                    'startInputOptions' => [
                        'class'=>'grid-filter',
                    ],
                    'endInputOptions' => [
                        'class'=>'grid-filter',
                    ],
                    'pluginOptions' => [
                        'locale'=>[
                            'format'=>'Y-m-d'
                        ]
                    ],
                    'pluginEvents' => [
                        'apply.daterangepicker'=>'function(ev,picker){
                            $("#date-use-range").val(1).trigger("change");
                        }',
                    ]
                ]);
                ?>
            </div>
<div class="col-lg-3"></div>
</div>


    <?= DynaGrid::widget([
            'gridOptions'=>[
            'dataProvider' => $dataProvider,
                        'floatHeader'=>true,
            'floatHeaderOptions' => [
            'position' => 'absolute'
            ],
            'filterSelector' => 'select[name="per-page"], .grid-filter',
            'showPageSummary' => true,
            'dataProvider' => $dataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                if (in_array($model->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS])) {
                    return ['data' => ['correction' => 1]];
                }
                return ['data' => ['correction' => 0]];
            },
        

        
            'toolbar' => [
                '{export}',
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
            'id'=>'invoices-grid',
            ],

        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-invoices'],
        'columns' => [
            ['class' => CheckboxColumn::className()],
            ['class' =>SerialColumn::className()],
            [
                'attribute'=>'fullnumber',
                'value' => function($model)
                {
                    $content = Html::a($model->fullnumber, ['view', 'id'=>$model->id]);
                    if (in_array($model->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS]) && $model->parent_id) {
                        $invoice = Invoice::findOne($model->parent_id);
                        $content .= " " . Yii::t('app', ' do faktury'). ": " . Html::a($invoice->fullnumber, ['invoice/view', 'id'=>$invoice->id], ['target' => '_blank']);
                    }
                    return $content;
                },
                'format'=>'raw'
            ],

            [
                'attribute'=>'customer_id',
                'value' => function ($model)
                {
                    $label = '';
                    if ($model->customer)
                    {
                        $label = $model->customer->displayLabel;
                    }
                    return $label;
                },
                'filter'=>\common\models\Customer::getList(),
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...')
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ],
            ],
            [
                'filter' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]),
                'filterType' => GridView::FILTER_SELECT2,
                 'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'attribute'=>'pm',
                'label'=>'PM',
                'value' => function ($model)
                {
                    $label = '';
                    if ($model->owner)
                    {
                        if ($model->owner->manager)
                            $label = $model->owner->manager->displayLabel;

                    }
                    return $label;
                },
            ],
            'date',
            [
                'attribute'=>'paymentdate',
                'format' =>'html',
                'filterType'=>GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => [
                    'pluginOptions'=> [
                        'format' => 'yyyy-mm-dd'
                    ]
                ],
                'value' =>function($model){
                    $content = $model->paymentdate;
                    $date = date('Y-m-d');
                    if (($model->paymentdate<$date)&&($model->paid==0))
                    {
                        $now = time(); // or your date as well
                        $your_date = strtotime($model->paymentdate);
                        $datediff = $now - $your_date;

                        $days = floor($datediff / (60 * 60 * 24));
                        if ($days>0)
                            $content.=" ".Html::tag('span', $days, ['class' => 'label label-danger']);
                        else
                            $content.=" ".Html::tag('span', $days, ['class' => 'label label-warning']);
                    }
                    if (($model->paymentdate>$date)&&($model->paid==0))
                    {
                        $now = time(); // or your date as well
                        $your_date = strtotime($model->paymentdate);
                        $datediff = $your_date-$now;

                        $days = floor($datediff / (60 * 60 * 24));
                        if ($days>0)
                            $content.=" ".Html::tag('span', $days, ['class' => 'label label-primary']);
                        else
                            $content.=" ".Html::tag('span', $days, ['class' => 'label label-warning']);
                    }
                    return $content;
                }
            ],
            [
                'attribute'=>'alreadypaid',
                'format'=>'raw',
                'value' => function($model) {
                    //return Yii::$app->formatter->asCurrency($model->alreadypaid, $model->currency);
                    return Html::a(Yii::$app->formatter->asCurrency($model->alreadypaid, $model->currency), ['/finances/invoice/add-payment', 'id'=>$model->id], ['class'=>'add-payment add-payment-'.$model->id]);
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell paid-cell',
                ],
            ],
            [
                'attribute'=>'remaining',
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell not-paid-cell',
                ],
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->remaining, $model->currency);
                },
            ],
            [
                'attribute'=>'netto',
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell netto-cell',
                ],
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->netto, $model->currency);
                },
            ],
            [
                'attribute'=>'tax',
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell vat-cell',
                ],
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->tax, $model->currency);
                },
            ],
            [
                'attribute'=>'total',
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell brutto-cell',
                ],
                'value' => function($model) {
                    return Yii::$app->formatter->asCurrency($model->total, $model->currency);
                },
            ],
            [
                'filter'=>[
                        1=>Yii::t('app', 'Tak'),
                    2=>Yii::t('app', 'Nie')
                ],
                'attribute'=>'paid',
                'pageSummary'=>false,
                'value'=>function ($model){
                    if ($model->paid){
                        return Yii::t('app', 'Tak');
                    }else{
                        return Yii::t('app', 'Nie');
                    }
                }
            ],
            [
                'attribute'=>'ownerCode',
                'format'=>'html',
                'value' => function ($model)
                {
                    $label = '';
                    if ($model->owner)
                    {
                        $label = $model->owner->name . " [" . $model->owner->code . "]";
                        $controller = Inflector::camel2id(Inflector::getClassName($model->owner_class));
                        $label = Html::a($label, ['/'.$controller.'/view', 'id'=>$model->owner_id, '#'=>'tab-finances']);

                    }
                    return $label;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>\common\models\Invoice::getOwnerCodeList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...')
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ],
            ],
            [
                'attribute'=>'types',
                'value' => function ($model)
                {
                    return \common\models\Invoice::getTypeList()[$model->type];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>\common\models\Invoice::getTypeList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...')
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'multiple'=>true
                    ],
                ],
            ],
            [
                'class'=>\common\components\ActionColumn::className(),
                'visibleButtons' => [
                    'update' => $user->can('menuInvoicesInvoiceEdit'),
                    'delete' => $user->can('menuInvoicesInvoiceDelete'),
                    'view' => $user->can('menuInvoicesInvoiceView'),
                ]
            ]
        ],
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


');

$this->registerJs('
function sumTable(){
    
    var keys = $("#invoices-grid").yiiGridView("getSelectedRows");
    var totals = [0,0,0,0,0];
    var positions = [0,0,0,0,0];
    
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
                var el2 = $(this).find(".add-payment");
                if (el2.length)
                {
                    val = el2.html();
                }
                
                if (val=="-" || val=="<em>(brak)</em>")
                {
                    val = 0;
                }
                
                if ("'.Yii::$app->formatter->decimalSeparator.'".length > 0) {
                    val = val.replace("'.Yii::$app->formatter->decimalSeparator.'", ".");
                }
                val = val.replace("'.Yii::$app->formatter->thousandSeparator.'", "");
                val = val.replace(/[^0-9.,]+/ig, "");
                val = val.replace(",", ".");
                if ($(this).hasClass("paid-cell"))
                {
                    totals[0] += parseFloat(val);
                    positions[0] = $(this).index();
                }
                if ($(this).hasClass("not-paid-cell"))
                {
                    totals[1] += parseFloat(val);
                    positions[1] = $(this).index();
                }
                if ($(this).hasClass("netto-cell"))
                {
                    totals[2] += parseFloat(val);
                    positions[2] = $(this).index();
                }
                if ($(this).hasClass("vat-cell"))
                {
                    totals[3] += parseFloat(val);
                    positions[3] = $(this).index();
                }
                if ($(this).hasClass("brutto-cell"))
                {
                    totals[4] += parseFloat(val);
                    positions[4] = $(this).index();
                }
            }
            
        });
    });
    labels = [];
    labels[0] = "Zapłacono";
    labels[1] = "Do zapłaty";
    labels[2] = "Netto";
    labels[3] = "Vat";
    labels[4] = "Brutto";

    for(var j=0;j<5; j++) {
        if (positions[j]!=0)
        {
            $(".kv-page-summary td").eq(positions[j]).html(labels[j]+": "+numberWithCommas(totals[j].toFixed(2)));
            $(".kv-page-summary td").eq(positions[j]).css("white-space", "nowrap");
        }
    }
    
}


sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});
');

$this->registerCss('
.sum-cell { white-space: nowrap; }
.select2-container--krajee .select2-selection--multiple .select2-selection__choice
{
    max-width: 150px;
    overflow-x: hidden;
}
.table > tbody > tr > td{
    border-left:1px solid #e7eaec;
}
');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');

$this->registerJs('
    $(".add-payment").click(function(e){
        e.preventDefault();
        $("#new-payment").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');
?>