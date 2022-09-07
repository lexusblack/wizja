<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\MonthCostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
use kartik\grid\CheckboxColumn;

$this->title = Yii::t('app', 'Koszty miesięczne');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="month-cost-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<div class="row">
<div class="col-lg12">
<div class="ibox">
<div class="ibox-title"><h4><?=Yii::t('app', 'Podsumowanie')?></h4></div>
<div class="ibox-content" id="month-cost-summary">
</div>
</div>
    
</div>
</div>
<div class="row">
    <?php 
    $gridColumn = [
        ['class' => CheckboxColumn::className()],
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
            'attribute' =>'section',
            'contentOptions'=>[
                    'class'=>'section-cell',
                ],
        ],
        [
            'attribute' =>'amount',
            'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => false,
        'export' => false,
        'id'=>'month-cost-grid',
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]) ,
        ],
    ]); ?>
</div>
</div>
<?php
$this->registerJs('
function sumTable(){
    
    var keys = $("#month-cost-grid").yiiGridView("getSelectedRows");
    var totals = new Object();
    
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
                var section = $(this).closest("tr").find(".section-cell").html();
                if (val=="-" || val=="<em>(brak)</em>") {
                    val = 0;
                }
                if (section!="-")
                {
                    sections = section.split(";");
                    for (i=0; i<sections.length; i++)
                    {
                        if (!(typeof totals[sections[i]] === "undefined"))
                        {
                            totals[sections[i]]["val"] += parseFloat(val)/sections.length;
                        }else{
                            totals[sections[i]]= new Object();
                            totals[sections[i]]["val"] = parseFloat(val)/sections.length;
                            totals[sections[i]]["name"] = sections[i];
                        }                        
                    }
                }else{
                    if (!(typeof totals[section] === "undefined"))
                    {
                        totals[section]["val"] += parseFloat(val);
                    }else{
                        totals[section]= new Object();
                        totals[section]["val"] = parseFloat(val);
                        totals[section]["name"] = section;
                    }                   
                }
            }
            
        });
    });
    $("#month-cost-summary").empty();
    var content = "<table class=\'table\'>"
    $.each(totals, function(index, value) {
        content+="<tr><td>"+value.name+"</td><td>"+numberWithCommas(Math.round(value.val * 100) / 100)+"</td></tr>";
    });
    content += "</table>"
    $("#month-cost-summary").append(content);
    
}

sumTable();

$(".kv-row-checkbox").on("change", function(){
    sumTable();
});
');
?>