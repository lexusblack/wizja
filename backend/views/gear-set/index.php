<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;
$user = Yii::$app->user;

$this->title = Yii::t('app', 'Zestawy urządzeń');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-set-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if ($user->can('gearSetCreate')) { ?>
    <p>
        <?= Html::a('Dodaj', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
            [
                'label' => Yii::t('app', 'Zdjęcie'),
                'value' => function ($model) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['gear-set/view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
        [
                'attribute' => 'category_id',
                'label' => Yii::t('app', 'Kategoria'),
                'value' => function($model){
                    if ($model->category)
                    {return $model->category->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\GearCategory::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Kategoria'), 'id' => 'grid-gear-model-search-category_id']
            ],
        'create_time',
        [
            'class' => 'yii\grid\ActionColumn',
            'visibleButtons' => [
                                'update'=>Yii::$app->user->can('gearSetEdit'),
                                'delete'=>Yii::$app->user->can('gearSetDelete'),
                                'view'=>Yii::$app->user->can('gearSetView'),
                            ],
        ],
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-set']],
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
