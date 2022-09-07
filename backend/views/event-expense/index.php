<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\helpers\Enum;
use kartik\grid\CheckboxColumn;
use kartik\grid\SerialColumn;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventExpenseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Koszty');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-expense-index">

    <h1><?= Html::encode($this->title) ?></h1>
        <div class="title_box row">
            <div class="col-lg-6">
            <form class="form-inline">
            <?php 
            $months = Enum::monthList();
            $months = array_merge([Yii::t('app', 'Wszystkie')], $months); ?>
                <?php echo Html::a(Html::icon('arrow-left'), ['index', 'm'=>$prev['m'], 'y'=>$prev['y']], ['class'=>'btn btn-md btn-primary date-drop']); ?>
                <?php echo Html::dropDownList('y', $y, Enum::yearList(2016, date('Y'), true), ['class'=>'form-control date-drop', 'id'=>"year"]); ?>
                <?php echo Html::dropDownList('m',$m, $months, ['class'=>'form-control date-drop', 'id'=>'month']); ?>
                <?php echo Html::a(Html::icon('arrow-right'), ['index', 'm'=>$next['m'], 'y'=>$next['y']], ['class'=>'btn btn-md  btn-primary date-drop']); ?>
                </form>
                                <?php
                    $this->registerJs('
                        $(".date-drop").on("change", function(e){
                            location.href="/admin/event-expense/index?m="+$("#month").val()+"&y="+$("#year").val();
                        });
                    ');
                ?>
            </div>
</div>
    <?= DynaGrid::widget([
        'gridOptions'=>[
        'dataProvider' => $dataProvider,
         'id'=>'invoices-grid',
        'filterModel' => $searchModel,
        'showPageSummary' => true,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
        'toolbar' => [
                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-eventexpens'],
        'columns' => [
            ['class' => CheckboxColumn::className()],
            ['class' =>SerialColumn::className()],
            'name',
            [
                'attribute'=>'event_id',
                'label'=>Yii::t('app', 'Wydarzenie'),
                'format'=>'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Event::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->event_id)
                    {
                        $list = \common\models\Event::getList();
                        $content = Html::a($list[$model->event_id], ['/event/view', 'id' => $model->event_id]);
                        return $content;
                    }else{
                        return "-";
                    }

                },
            ],
            [
                'attribute'=>'manager_id',
                'label'=>Yii::t('app', 'Project Manager'),
                'format'=>'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\User::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->event_id)
                    {
                        if ($model->event->manager_id)
                        {
                            $content = $model->event->manager->displayLabel;
                            return $content;
                        }else{
                            return "-";
                        }
                        
                        
                    }else{
                        return "-";
                    }

                },
            ],
            'section',
            [
                'attribute' => 'customer_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Customer::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'label'=>Yii::t('app', 'Firma'),
                'format'=>'html',
                'value'=>function($model){
                    if (isset($model->customer))
                        return Html::a($model->customer->name, ['/customer/view', 'id' => $model->customer_id]);
                    else
                        return "-";
                }
            ],
            [
                'attribute' => 'amount',
                'format' => 'currency',
                'value'=>function($model)
                {
                    if ($model->amount)
                    {
                        return $model->amount;
                    }else{
                        return 0;
                    }
                },
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            [
                'label'=>Yii::t('app', 'Zapłacono'),
                'value'=>function($model)
                {
                    if ($model->expense_id)
                    {
                        if ($model->expense->total>0)
                            return $model->expense->alreadypaid/$model->expense->total*$model->amount;
                        else
                            return 0;
                    }else{
                        return 0;
                    }
                },
                'format' => 'currency',
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            [
                'label'=>Yii::t('app', 'Pozostało'),
                'value'=>function($model)
                {
                    if ($model->expense_id)
                    {
                        if ($model->expense->total>0)
                            return $model->expense->remaining/$model->expense->total*$model->amount;
                        else
                            return 0;
                    }else{
                        return $model->amount;
                    }
                },
                'format' => 'currency',
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            'invoice_nr',
            [
                'attribute'=>'expense_id',
                'label'=>Yii::t('app', 'Faktura'),
                'format'=>'html',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>[1=>"Bez podpiętej faktury", 2=>"Bez numeru fv", 3=>"Bez numeru i podpiętej fv"],
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'value' => function($model, $key, $index, $column)
                {
                    if ($model->expense_id)
                    {
                        $content = Html::a($model->expense->number, ['/finances/expense/view', 'id' => $model->expense_id]);
                        return $content;
                    }else{
                        $content = Html::a(Yii::t('app', 'Dodaj fakturę'), ['/finances/expense/create', 'id' => $model->event_id]);
                        return $content;
                    }

                },
            ],
            [
                'attribute' => 'create_time',
                'label'=>Yii::t('app', 'Data dodania'),
                'format'=>'html',
                'value'=>function($model){
                    return mb_substr($model->create_time, 0, 10);
                }
            ],
        ],
    ]); ?>
</div>

<?php
$this->registerJs('
function sumTable(){
    
    var keys = $("#invoices-grid").yiiGridView("getSelectedRows");
    var totals = [0,0,0];
    
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
                var el2 = $(this).find(".kv-editable-value");
               
                if (el2.length) {
                    val = el2.html();
                }
                
                if (val=="-" || val=="<em>(brak)</em>") {
                    val = 0;
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
    
    var x = 7;
    var y = 9;
    
    for(var j=x;j<=y; j++) {
        $(".kv-page-summary td").eq(j).html(numberWithCommas(totals[j-x].toFixed(2)));
        $(".kv-page-summary td").eq(j).css("white-space", "nowrap");
    }
    
}

sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});
');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');
?>