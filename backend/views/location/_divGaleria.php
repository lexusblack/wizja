<?php
use yii\bootstrap\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Event; */
?>

        <?php
        echo GridView::widget([
                            'striped'=>false,
                'condensed'=>true,
                'bordered'=>false,
                'layout'=>'{items}',
            'dataProvider'=>$model->getAssignedGalleries(),
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