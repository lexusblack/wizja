<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Briefy'); ?></h3>
<div class="row">
    <div class="col-md-12">
            <div class="ibox">
        <?php

        if ($user->can('eventEventEditEyeAttachmentAdd')) {
            echo Html::a(Html::icon('plus') . ' ' . Yii::t('app', 'Dodaj'), ['brief/create', 'eventId' => $model->id], ['class' => 'btn btn-success']);
        } ?>
        <?php //echo Html::a(Html::icon('picture').' Galeria', ['attachment/gallery', 'eventId'=>$model->id], ['class'=>'btn btn-warning']); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedBriefs(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'value'=>function($model)
                    {
                        return Html::a($model->filename, $model->getFileUrl(), ['target'=>'_blank']);
                    },
                    'format' => 'html',
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'brief',

                    'template'=>'{download} {delete}',
                    'buttons'=>[
                        'download'=>function($url, $model, $key)
                        {
                            return Html::a(Html::icon('save-file'), $url);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            return Html::a(Html::icon('trash'), $url, [
                                'data' => [
                                    'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                    'visibleButtons' => [
                        'show' => $user->can('eventEventEditEyeAttachmentDownload'),
                        'download' => $user->can('eventEventEditEyeAttachmentDownload'),
                        'update' => $user->can('eventEventEditEyeAttachmentEdit'),
                        'delete' => $user->can('eventEventEditEyeAttachmentDelete'),
                    ]

                ],
            ],
        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>