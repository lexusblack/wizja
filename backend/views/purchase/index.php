<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\PurchaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
use kartik\helpers\Enum;

$this->title = Yii::t('app', 'Zakupy');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<div class="row">
<div class="col-lg12">
<div class="ibox">
<div class="ibox-title newsystem-bg"><h4><?=Yii::t('app', 'Podsumowanie')?></h4></div>
<div class="ibox-content" id="purchase-summary">
</div>
</div>
    
</div>
</div>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
         ['class' =>kartik\grid\CheckboxColumn::className()],
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute' => 'user_id',
                'value' => function($model){
                    if ($model->user)
                    {return $model->user->displayLabel;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\models\User::getList(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz'), 'id' => 'grid-purchase-search-user_id']
            ],
        'code',
        'description:ntext',
        [
        'attribute'=>'price',
        'contentOptions'=>[
                    'class'=>'sum-cell',
                ],
        ],
        [
        'attribute'=>'datetime',
        'value'=>function($model){
            if ($model->datetime)
            {
                return substr($model->datetime, 0, 10);
            }else{
                return "-";
            }
        }
        ],
        [
            'attribute' =>'section',
            'contentOptions'=>[
                    'class'=>'section-cell',
                ],
        ],
        [
            'label' => Yii::t('app', 'Powiązane eventy'),
            'attribute'=>'event',
            'format'=>'raw',
            'value' => function($model){
                    if ($model->events)
                    {
                        $return = "";
                        foreach ($model->events as $event)
                        {
                            $return .=Html::a($event->name.' ['.$event->code.']', ['event/view', 'id' => $event->id])."</br>";
                        }
                        return $return;
                    }else{
                        return "-";
                    }
                },
        ],
        [
                'attribute' => 'purchase_type_id',
                'value' => function($model){
                    if ($model->purchaseType)
                    {return $model->purchaseType->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\PurchaseType::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Wybierz'), 'id' => 'grid-purchase-search-purchase_type_id']
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
        'id'=>'purchase-grid',
        'filterSelector'=>'.grid-filters',
        // your toolbar can include the additional full export menu
        'toolbar' => [
        [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')+1), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
        ],
    ]); ?>

</div>
<?php
$this->registerJs('
function sumTable(){
    
    var keys = $("#purchase-grid").yiiGridView("getSelectedRows");
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
    $("#purchase-summary").empty();
    var content = "<table class=\'table\'>"
    var sum_total = 0;
    $.each(totals, function(index, value) {
        content+="<tr><td>"+value.name+"</td><td>"+numberWithCommas(Math.round(value.val * 100) / 100)+"</td></tr>";
        sum_total+=Math.round(value.val * 100) / 100;
    });
    content+="<tr style=\'background-color:#fafafa;\'><td>Suma</td><td>"+numberWithCommas(Math.round(sum_total * 100) / 100)+"</td></tr>";
    content += "</table>"
    $("#purchase-summary").append(content);
    
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