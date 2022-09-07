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
            'dataProvider'=>$model->getAssignedPlans(),
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
                        return Html::a($model->getName(), $model->getFileUrl());
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'owner',
                    'label'=>Yii::t('app', 'Przesłał'),
                    'value'=>function($model)
                    {
                            return $model->getOwner();
                    },
                    'format' => 'html',
                ],
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'location-plan',
                    'template'=>'{download} {show} {update} {delete}',
                    'buttons'=>[
                        'download'=>function($url, $model, $key)
                        {
                            return Html::a(Html::icon('save-file'), ['location-plan/download', 'id'=>$model->id]);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            if (Yii::$app->user->can('locationDelete'))
                            {
                                return Html::a(Html::icon('trash'), ['location-plan/delete', 'id'=>$model->id], [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                            return false;
                        }, 
                        'show'=>function($url, $model, $key)
                        {
                            $options =  [];
                            $options['target'] = '_blank';
                            $route = $model->getFileUrl();

                            return Html::a(Html::icon('eye-open'), $route, $options);
                        },
                    ],
                    'visibleButtons' => [
                        'update' => function ($model){ return $model->location->isEditable();},
                        'delete' => function ($model){ return $model->location->isEditable();},
                        'show' => true,
                        'download'=>true
                    ],

                ],
            ],

        ]);
        ?>