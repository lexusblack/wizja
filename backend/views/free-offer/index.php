<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\FreeOfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Freelancers Network');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="free-offer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj ogłoszenie'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute'=>'name',
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    return $content;
                }
            ],
        'company_name',
        [
                'label'=>Yii::t('app', 'Od - do'),
                'attribute'=>'start_time',
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->start_time,'short');
                    $end = Yii::$app->formatter->asDateTime($model->end_time, 'short');
                    return $start.' <br /> '.$end;
                },
                'contentOptions'=>['style'=>'width: 130px;'],
            ],
        'address',
        [
            'attribute'=>'city_id',
            'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\City::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            'value'=>function($model)
            {
                if ($model->city_id)
                    return $model->city->name;
            }
        ],
        'deal_type',
        [
            'attribute'=>'skills',
            'format'=>'html',
            'value'=>function($model)
            {
                $skills = explode(";", $model->skills);
                $content = "";
                foreach ($skills as $skill)
                {
                    if ($skill!="")
                        $content.=$skill."<br/>";
                }
                return $content;
            }
        ],
        [
            'attribute'=>'devices',
            'format'=>'html',
            'value'=>function($model)
            {
                $skills = explode(";", $model->devices);
                $content = "";
                foreach ($skills as $skill)
                {
                    if ($skill!="")
                        $content.=$skill."<br/>";
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
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-free-offer']],

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
