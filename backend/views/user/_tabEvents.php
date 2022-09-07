<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Wydarzenia'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                'format'=>'html',
                'value'=>function($model)
                {
                    if ($model->location)
                    {
                        $content = Html::a($model->location->name, ['/location/view', 'id' => $model->location->id]);
                        return $content;
                    }else{
                        return "-";
                    }

                },
                'attribute'=>'location_id',
                'filterType' => GridView::FILTER_SELECT2,
                'filter'=>\common\models\Location::getModelList(false, 'displayLabel'),
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
                'value'=>'customer.displayLabel',
                'filter' => \common\models\Customer::getList(),
                'attribute' => 'customer_id',
            ],
            [
                'label'=> Yii::t('app', 'Od - do'),
                'contentOptions' => ['style' => 'width:120px;'],
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->getTimeStart(),'short');
                    $end = Yii::$app->formatter->asDateTime($model->getTimeEnd(), 'short');
                    return $start.' <br /> '.$end;
                }
            ]
        ],
    ]); ?>
            </div>
        </div>
    </div>
</div>
</div>