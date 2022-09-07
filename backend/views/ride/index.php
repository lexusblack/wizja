<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\RideSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = (Yii::$app->params['companyID']=="imagination")?Yii::t('app','Kilometrówka') : Yii::t('app','Przejazdy');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="ride-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute' => 'vehicle_id',
                'value' => function($model){
                    if ($model->vehicle)
                    {return $model->vehicle->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Vehicle::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'wybierz...'), 'id' => 'grid-ride-search-vehicle_id']
            ],
        [
                'attribute' => 'user_id',
                'label'=>Yii::t('app', 'Kierowca'),
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
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'wybierz...'), 'id' => 'grid-ride-search-user_id']
            ],
        [
                'attribute' => 'event_id',
                'label' => 'Event',
                'value' => function($model){
                    if ($model->event)
                    {return $model->event->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'wybierz...'), 'id' => 'grid-ride-search-event_id']
        ],
        [
            'label'=>Yii::t('app', 'Licznik przed'),
            'format'=>'raw',
            'value'=>function($model){
                    return $model->km_start;
            }
        ],
        [
            'label'=>Yii::t('app', 'Licznik po'),
            'format'=>'raw',
            'value'=>function($model){
                    return $model->km_end;
            }
        ],
        [   
            'label'=>Yii::t('app', 'Dystans'),
            'format'=>'raw',
            'value'=>function($model){
                if ($model->km_end)
                {
                    $distance = $model->km_end-$model->km_start;
                    return $distance." km";
                }else{
                    return "";
                }
            }
        ],
        [   
            'label'=>Yii::t('app', 'Data'),
            'format'=>'raw',
            'attribute'=>'start',
            'value'=>function($model){
                return substr($model->start, 0, 16)."<br/>".substr($model->end, 0, 16);
            }
        ],
        'start_place',
        'end_place',
        'description',
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
