<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\AgencyOfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use common\components\grid\GridView;

$this->title = Yii::t('app', 'Oferty');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="agency-offer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        [
            'label' => Yii::t('app', 'Duplikuj'),
            'format' => 'html',
            'visible' => $user->can('menuOffersViewDuplicate'),
            'value' => function ($model) {
                return Html::a('<i class="fa fa-copy"></i>', ['/agency-offer/duplicate', 'id' => $model->id], ['class'=>'btn btn-warning btn-circle']) ;                  
            }
        ],
        'id',
        [
                 'attribute' => 'name',
                 'format'=>'html',
                'value' => function($model){
                    return Html::a($model->name, ['view', 'id'=>$model->id]);
                },      
        ],
        [
                'attribute' => 'customer_id',
                'label' => 'Customer',
                'value' => function($model){
                    if ($model->customer)
                    {return $model->customer->name;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Customer::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Customer', 'id' => 'grid-agency-offer-search-customer_id']
            ],
        [
                'attribute' => 'manager_id',
                'label' => 'Manager',
                'value' => function($model){
                    if ($model->manager)
                    {return $model->manager->username;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->asArray()->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'User', 'id' => 'grid-agency-offer-search-manager_id']
            ],
        [
                'attribute' => 'event_id',
                'label' => 'Event',
                'format'=>'html',
                'value' => function($model){
                    if ($model->event)
                    {return Html::a($model->event->name, ['/event/view', 'id'=>$model->event->id]);}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\Event::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Event', 'id' => 'grid-agency-offer-search-event_id']
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
