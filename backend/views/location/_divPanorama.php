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
            'dataProvider'=>$model->getAssignedPanoramas(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
                [
                    'attribute' => 'filename',
                    'label'=>Yii::t('app', 'Nazwa'),
                    'value'=>function($model)
                    {
                            $options =  [];
                            $route = ['location-panorama/show', 'id'=>$model->id];
                            return Html::a($model->getName(), $route, $options);
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
                    'controllerId'=>'location-panorama',
                    'template'=>'{show} {update} {delete}',
                    'buttons'=>[
                        'show'=>function($url, $model, $key)
                        {
                            $options =  [];
                            $route = ['location-panorama/show', 'id'=>$model->id];

                            return Html::a(Html::icon('eye-open'), $route, $options);
                        },
                        'delete' => function ($url, $model, $key)
                        {
                            if (Yii::$app->user->can('locationDelete'))
                            {
                                return Html::a(Html::icon('trash'), ['location-panorama/delete', 'id'=>$model->id], [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                            return false;
                        }
                    ],
                    'visibleButtons' => [
                        'update' => function ($model){ return $model->location->isEditable();},
                        'delete' => function ($model){ return $model->location->isEditable();},
                        'show' => true,
                    ],

                ],
            ],

        ]);
        ?>