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
            <?php if (Yii::$app->params['companyID']=='newsystem'){ ?>

        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['gear-model-attachment/create', 'gearModelId'=>$model->id], ['class'=>'btn btn-success']); ?>
        <?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
<?php
    $gridColumnGearModelAttachment = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                [
                    'attribute' => 'filename',
                    'value'=>function($model)
                    {
                        $options =  [];
                        $options['target'] = '_blank';
                        return Html::a($model->filename, $model->getFileUrl(), $options);
                    },
                    'format' => 'html',
                ],
                /*
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-model-attachment',

                    'template'=>'{delete}',
                    'buttons'=>[
                        'delete' => function ($url, $model, $key)
                        {
                                return Html::a(Html::icon('trash'), $url, [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            return false;
                        }
                    ],

                ]*/
        ];
    echo Gridview::widget([
        'dataProvider' => $model->getAssignedAttachements(),
        'pjax' => false,
        'export' => false,
        'columns' => $gridColumnGearModelAttachment
    ]);
?>
            </div>
        </div>
    </div>
</div>
</div>