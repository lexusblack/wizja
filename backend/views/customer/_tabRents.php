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
        'dataProvider' => $model->getAssignedRents2(),
        'showPageSummary' => true,
                'pjax'=>true,

        'id'=>'events-c-grid',
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'kartik\grid\CheckboxColumn'],
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['/rent/view', 'id' => $model->id]);
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
                    $start = Yii::$app->formatter->asDateTime($model->start_time,'short');
                    $end = Yii::$app->formatter->asDateTime($model->end_time, 'short');
                    return $start.' <br /> '.$end;
                },
                'contentOptions'=>['style'=>'width: 110px;'],
            ],
        ],
    ]); ?>

    </div>
</div>

</div>
<?php
$this->registerCss('
td.Suma {
    width: 100px;
}
');


$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');

