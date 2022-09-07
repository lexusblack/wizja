<?php
/* @var $model \common\models\Customer; */
/* @var $this \yii\web\View; */

use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\widgets\ActiveForm;

if (!Yii::$app->user->can('clientClientsSeeProjects')) {
    return;
}

?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
<?php 
        $sectionList = [Yii::t('app', 'Suma')=>Yii::t('app', 'Suma'), Yii::t('app', 'Transport')=>Yii::t('app', 'Transport'), Yii::t('app', 'Obsługa')=>Yii::t('app', 'Obsługa')];
        foreach (\common\models\EventExpense::getSectionList() as $s)
        {
            $sectionList[$s] = $s;
        }
        ?>
<?= GridView::widget([
        'dataProvider' => $model->getAssignedEvents2(),
        'showPageSummary' => true,
        'pjax'=>true,
        'id'=>'events-c-grid',
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'kartik\grid\CheckboxColumn'],
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['/event/view', 'id' => $model->id]);
                    return $content;
                },
            ],
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
            ],
            [
                'label'=>Yii::t('app', 'Od - do'),
                'attribute'=>'event_start',
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ],
            
            [
                'label'=>Yii::t('app', 'Wartość'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventValueAll();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        if ($k==Yii::t('app', 'Suma'))
                                $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            [
                'label'=>Yii::t('app', 'Koszt'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventCosts();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        if ($k==Yii::t('app', 'Suma'))
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
            [
                'label'=>Yii::t('app', 'Zysk'),
                'format'=>'raw',
                'value'=>function($model)
                {
                    $value = $model->getEventProfits();
                    $content = "";
                    foreach ($value as $k=>$v)
                    {
                        if ($k==Yii::t('app', 'Suma'))
                        $content .= "<div class='value-div ".$k."-div'>".Yii::$app->formatter->asCurrency($v)."</div>";
                    }
                    return $content;
                },
                'pageSummary'=>true,
                'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
            ],
        ],
    ]); ?>

    </div>
</div>

</div>
<?php
$this->registerCss('
td.Suma {
    width: 100px;
}
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
    
    var keys = $("#events-c-grid").yiiGridView("getSelectedRows");
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
    var y = 8;
    totals[2] = totals[0]-totals[1];
    labels = [];
    labels[0] = "Wartość";
    labels[1] = "Koszt";
    labels[2] = "Zysk";

    for(var j=x;j<y; j++) {

        $(".kv-page-summary td").eq(j).html(labels[j-x]+": "+totals[j-x].toFixed(2));
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
