<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Segmenty');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="hall-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'name',
        'area',
        'width',
        'length',
        'height',
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
            ],

            [
                'label'=>Yii::t('app', 'Powiązane powierzchnie'),
                'format'=>'raw',
                'value'=>function($model){
                    $content = "";
                    $isOne = false;
                    foreach ($model->hallGroups as $hg)
                    {
                        $content .=$hg->name."<br/>";
                        if (count($hg->halls)==1)
                        {
                            $isOne = true;
                        }
                    }
                    if (!$isOne)
                    {
                        $content .=Html::a(Yii::t('app', 'Stwórz z segmentu'), ['hall-group/create-from', 'id'=>$model->id], ['class'=>'btn btn-success btn-xs']);
                    }
                    return $content;
                }
            ],
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-hall']],
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
