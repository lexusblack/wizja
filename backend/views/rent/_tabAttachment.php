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
            echo Html::a(Yii::t('app', 'Dodaj'), ['rent-attachment/create', 'rentId' => $model->id], ['class' => 'btn btn-success']);
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
                        if ($user->can('gearAttachmentsView')) {
                            return Html::a($model->filename, ['rent-attachment/download', 'id' => $model->id], ['target' => '_blank']);
                        }
                        return $model->filename;
                    },
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'template'=>'{show} {download} {delete}',
                    'controllerId'=>'rent-attachment',
                    'buttons'=>[
                        'show'=>function($url, $model, $key)
                        {
                            $options =  [];
                            $route = $url;
                            $options['target'] = '_blank';

                            return Html::a(Html::icon('eye-open'), $route, $options);
                        },
                        'download'=>function($url, $model, $key)
                        {
                            $options['target'] = '_blank';
                            return Html::a(Html::icon('save-file'), $url, $options);
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
                        'show' => true,
                        'download' => true,
                        'delete' => true,
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