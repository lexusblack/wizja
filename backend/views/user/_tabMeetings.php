<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Spotkania'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $model->getAssignedMeetings(),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'id',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['/meeting/view', 'id' => $model->id]);
                    return $content;
                },
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
                'contentOptions' => ['style' => 'width:120px;'],
                'content' => function ($model, $index, $row, $grid)
                {
                    $start = Yii::$app->formatter->asDateTime($model->start_time,'short');
                    $end = Yii::$app->formatter->asDateTime($model->end_time, 'short');
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