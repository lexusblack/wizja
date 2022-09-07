<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Eventy'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssgignedEvents(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                        [
                            'format' => 'html',
                            'header' => Yii::t('app', 'Event'),
                            'value' => function ($model) {
                                    if($model->event)
                                        return Html::a($model->event->name,['/event/view', 'id' => $model->event->id]);
                                    else
                                        return "-";
                            },
                        ],
                'quantity',
                'start_time',
                'end_time',
                'price:currency',
                         [
                            'format' => 'html',
                            'header' => Yii::t('app', 'Zamówienie'),
                            'value' => function ($model) {
                                    if($model->order)
                                        return Html::a(Yii::t('app', "Nr")." ".$model->order->id,['/order/view', 'id' => $model->order->id]);
                                    else
                                        return "-";
                            },
                        ],               
            ],
        ]);
        ?>
    </div>
</div>
</div>
</div>
</div>
