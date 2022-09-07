<?php
/* @var $model \common\models\Customer; */
/* @var $this \yii\web\View; */

use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\widgets\ActiveForm;

if (!Yii::$app->user->can('clientClientsSeeProjects')) {
    return;
}

?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12"> 

<?= GridView::widget([
        'dataProvider' => $model->getAssignedEvents(),
        'showPageSummary' => true,
        'id'=>'events-c-grid',
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['/event/view', 'id' => $model->id]);
                    return $content;
                },
            ],
            [
                'value'=>'manager.displayLabel',
                'filter' => \common\models\User::getList(),
                'attribute' => 'manager_id',
                'filterType' => GridView::FILTER_SELECT2,
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
                'label'=>Yii::t('app', 'Od - do'),
                'attribute'=>'event_start',
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' - '.$end;
                },
                'contentOptions'=>['style'=>'width: 210px;'],
            ],
        ],
    ]); ?>

    </div>
</div>

</div>
<?php

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');
