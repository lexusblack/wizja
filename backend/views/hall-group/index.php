<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Powierzchnie');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="hall-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
                    [
                'label'=>Yii::t('app', 'S'),
                'attribute'=>'position',

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

            ],
        [
            'attribute'=>'name',
            'format'=>'raw',
            'value'=>function($model){
                return Html::a($model->name, ['view', 'id'=>$model->id]);
            }
        ],
        'area',
        'height',
        ];
    foreach (\common\models\HallAudienceType::find()->orderBy(['position'=>SORT_ASC])->all() as $type)
    {
        $gridColumn[] = [
        'label'=>$type->name,
        'value'=>function ($model) use ($type)
        {
            $v = \common\models\HallAudience::find()->where(['hall_group_id'=>$model->id, 'hall_audience_type_id'=>$type->id])->one();
            if ($v)
            {
                return $v->audience;
            }else{
                return "-";
            }
        }
        ];
    }
         $gridColumn[] =           [
                'label'=>Yii::t('app', 'Segmenty'),
                'value'=>function($model)
                {
                    $content = "";
                    $first = true;
                    foreach ($model->halls as $hall)
                    {
                        if (!$first)
                            $content.=", ";
                        $first=false;
                        $content.=$hall->name;
                    }
                    return $content;
                }
            ];
        $gridColumn[] =
                    [
                'label' => Yii::t('app', 'Zdjęcie'),
                'value' => function ($model, $key, $index, $column) {
                    if ($model->main_photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'70px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ];
            $gridColumn[] =
        [
            'class' => 'yii\grid\ActionColumn',
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-hall-group']],
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
    $storeUrl = Url::to(['hall-group/store-order']);

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
    $.post("'.$storeUrl.'", {data:list, _csrf: yii.getCsrfToken()});
    
    return false;
});
');
