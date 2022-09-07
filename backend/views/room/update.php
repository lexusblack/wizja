<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Room */

$this->title =  Yii::t('app', 'Edytuj salę').': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->location->name, 'url' => ['location/view', 'id' => $model->location_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo Html::a(Yii::t('app', 'Dodaj zdjęcie'), ['room-photo/create', 'roomId'=>$model->id], ['class'=>'btn btn-success']); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedPhotos(),
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'columns' => [
                [
                    'class'=>\yii\grid\SerialColumn::className(),
                ],
            [
                'attribute' => 'filename',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->filename == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getFileUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
                'filter'=>false,
            ],
            'filename',
                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'room-photo',
                    'template'=>'{delete}',
                    'buttons'=>[
                        'delete' => function ($url, $model, $key)
                        {
                            if (Yii::$app->user->can('locationDelete'))
                            {
                                return Html::a('<i class="fa fa-trash"></i>', ['room-photo/delete', 'id'=>$model->id], [
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