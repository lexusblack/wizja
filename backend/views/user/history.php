<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Historia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getDisplayLabel(), 'url' => ['view', 'id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'id',
                'label'=>Yii::t('app', 'Wydarzenie'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name.' ['.$model->code.']', ['/event/view', 'id' => $model->id]);
                    return $content;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Event::getList(),
                'filterWidgetOptions' => [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ],
                ],
            ],
            [
                'value'=>'location.displayLabel',
                'filter' => \common\models\Location::getList(),
                'attribute' => 'location_id',
            ],
            [
                'value'=>'customer.displayLabel',
                'filter' => \common\models\Customer::getList(),
                'attribute' => 'customer_id',
            ],
            [
                'value'=>'contact.displayLabel',
                'filter' => \common\models\Contact::getList(),
                'attribute' => 'contact_id',
            ],
            [
                'label'=> Yii::t('app', 'Od - do'),
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>