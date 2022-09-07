<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Załączniki'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a('Dodaj', ['gear-attachment/create', 'gearId' => $model->id], ['class' => 'btn btn-success']);
        }
        ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedAttachements(),
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'format' => 'raw',
                    'value'=>function($model)
                    {
                        return Html::a($model->filename, ['gear-attachment/show', 'id'=>$model->id], ['target' => '_blank']);
                    },
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'buttons' => [
                        'download' => function ($url, $model) {
                            if (strtolower($model->extension) == 'png'  || strtolower($model->extension) == 'jpg' || strtolower($model->extension) == 'pdf') {
                                return ' '.Html::a(Html::icon('download'), ['gear-attachment/download', 'id'=>$model->id], ['target' => '_blank']);
                            }
                        }
                    ],
                    'template'=>'{delete}{download}',
                    'controllerId'=>'gear-attachment',
                ]
            ],
        ]);
        ?>
            </div>
        </div>
    </div>
</div>
</div>