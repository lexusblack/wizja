<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Miejsca');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;

?>
<div class="location-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
    <?php if ($user->can('locationLocationsAdd')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'filterSelector'=>'.grid-filters',
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
                'filter'=>false,
            ],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    return $content;
                },
            ],
            [
                'value'=>'locationType.name',
                'attribute'=>'location_type_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\LocationType::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ],
            'city',
            [
                'value'=>'province.name',
                'attribute'=>'province_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Province::getList(),
                'filterWidgetOptions' => [
//                    'data'=>\common\models\Event::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ],
            [
                'value'=>'stars',
                'attribute'=>'stars',
                //'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Location::getStarList(),
                'filterWidgetOptions' => [
//                    'data'=>\common\models\Event::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'format'=>'html',
                'contentOptions'=>['class'=>'text-center'],
                'value' => function ($model, $key, $index, $column) {
                    $stars = "";
                    if ($model->stars>0)
                    {
                        for ($i=0; $i<$model->stars; $i++)
                        {
                            $stars .= "<i class='fa fa-star'></i>";
                        }
                    }
                    return $stars;
                },
            ],
            [
                'label'=>Yii::t('app', 'Łóżka'),
                'attribute'=>'beds',
                'value'=>'beds',
                'contentOptions'=>['class'=>'text-center'],
                'width'=>'70px'
            ],
            [
                'label'=>Yii::t('app', 'Sala'),
                'attribute'=>'biggest_room',
                'value'=>'biggest_room',
                'contentOptions'=>['class'=>'text-center'],
                'width'=>'70px'
            ],
            
            [
                'label'=>Yii::t('app', 'Publiczne'),
                'attribute'=>'public',
                'filter'=>\common\models\Location::getPublicList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
                'format'=>'html',
                'value'=>function ($model, $key, $index, $column) {
                    if ($model->public==0)
                    {
                        return Yii::t('app', "Prywatne");
                    }
                    if ($model->public==1)
                    {
                        return Yii::t('app', "Publiczne (niezaakceptowane)");
                    }
                    if ($model->public==2)
                    {
                        return Yii::t('app', "Publiczne");
                    }
                },
                'contentOptions'=>['class'=>'text-center'],
                'width'=>'70px'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'update' => function ($model){ return $model->isEditable();},
                        'delete' => function ($model){ return $model->isEditable();},
                        'view' => true,
            ],
            ],
        ],
    ]); ?>
</div>
    </div>
</div>