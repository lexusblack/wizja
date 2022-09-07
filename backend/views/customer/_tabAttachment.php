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
            echo Html::a(Yii::t('app', 'Dodaj'), ['customer-attachment/create', 'customerId' => $model->id], ['class' => 'btn btn-success']);
        
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
                    'value'=>function($model) use ($user)
                    {
                            return Html::a($model->filename, ['customer-attachment/show', 'id' => $model->id], ['target' => '_blank']);
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
                    'controllerId'=>'customer-attachment',
                    'visibleButtons' => [
                        'delete' => $user->can('gearAttachmentsDelete'),
                        'download' => $user->can('gearAttachmentsView')
                    ]
                ]
            ],
        ]);
        ?>
            </div>
        </div>
    </div>
</div>
</div>