<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\export\ExportMenu;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Statusy serwisu sprzętu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-service-statut-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj status'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
                'label'=>Yii::t('app', 'S'),
                'attribute'=>'order',

                'content'=>function($model, $key, $index, $grid)
                {
                    return Html::a(Html::icon('chevron-up'), '#', ['class'=>'sort-up']).Html::a(Html::icon('chevron-down'), '#', ['class'=>'sort-down']);;


                },
                'contentOptions'=>function ($model, $key, $index, $column) {
                        return [
                            'class'=>'text-center gear-sort',
                            'data-id'=>$model->id,
                            'style'=>'white-space:nowrap;'
                        ];
                }

        ],
        [
        'attribute'=>'name',
        'format'=>'html',
        'value'=>function ($model){
            return "<span class='label' style='background-color:".$model->color."; margin-right:20px;'> </span> ".$model->name;
        }],
        [
        'attribute'=>'type',
        'format'=>'html',
        'value'=>function ($model){
            return \common\models\GearServiceStatut::getTypes()[$model->type];
        }],  
        [
        'attribute'=>'in_menu',
        'format'=>'html',
        'value'=>function ($model){
            if ($model->in_menu)
            {
                return Yii::t('app', 'TAK');
            }else{
                return Yii::t('app', 'NIE');
            }
        }],       
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-service-statut']],
        'export' => false,
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

<?php
$this->registerJs('

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
    $.post("'.Url::to(['gear-service-statut/store-order']).'", {data:list, _csrf: yii.getCsrfToken()});
    
    return false;
});
');
?>