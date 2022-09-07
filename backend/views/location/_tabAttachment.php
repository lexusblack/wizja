<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
$user = Yii::$app->user;

/* @var $model \common\models\Event; */
?>
<div class="panel-body">
<div class="panel_mid_blocks">
    <div class="row">
        <div class="col-md-12">
        <?php if ($user->can('locationAttachmentsAdd')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['location-attachment/create', 'locationId'=>$model->id], ['class'=>'btn btn-success']); ?>
            <?php } ?>
        </div>
    </div>
    <div class="panel_block" style="margin-bottom: 0;">
        <div class="title_box">
            <h4><?php echo Yii::t('app', 'Załączniki'); ?></h4>
        </div>
    </div>

<div class="row">
    <div class="col-md-12">

        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedAttachements(),
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
                        if ($model->type == \common\models\LocationAttachment::TYPE_PANORAMA)
                        {
                            $options =  [];
                            $route = ['location-attachment/show', 'id'=>$model->id];

                            return Html::a($model->filename, $route, $options);

                        }
                        return Html::a($model->filename, ['location-attachment/download', 'id'=>$model->id]);
                    },
                    'format' => 'html',
                ],
                'typeLabel:text:'.Yii::t('app', 'Typ'),
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'location-attachment',
                    'template'=>'{show} {download} {update} {delete}',
                    'buttons'=>[
                        'show'=>function($url, $model, $key)
                        {
                            $options =  [];
                            $route = ['location-attachment/show', 'id'=>$model->id];
                            if ($model->type == \common\models\LocationAttachment::TYPE_FILE)
                            {
                                $options['target'] = '_blank';
                                $route = $model->getFileUrl();
                            }

                            return Html::a(Html::icon('eye-open'), $route, $options);
                        },
                        'download'=>function($url, $model, $key)
                        {
                            if ($model->type == \common\models\LocationAttachment::TYPE_PANORAMA)
                            {
                                return false;
                            }
                            return Html::a(Html::icon('save-file'), ['location-attachment/download', 'id'=>$model->id]);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            if (Yii::$app->user->can('locationDelete'))
                            {
                                return Html::a(Html::icon('trash'), ['location-attachment/delete', 'id'=>$model->id], [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                            return false;
                        }
                    ],

                ],
            ],

        ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>
</div>