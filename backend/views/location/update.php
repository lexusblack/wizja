<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = Yii::t('app', 'Edycja').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Miejsca'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="location-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
    <div class="row">
<?php
if($providerPhoto->totalCount){
    $gridColumnPhoto = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'filename',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerPhoto,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-location-photo']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Zdjęcia')),
        ],
        'export' => false,
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
                    'controllerId'=>'location-photo',
                    'template'=>'{delete}',
                    'buttons'=>[
                        'delete' => function ($url, $model, $key)
                        {
                            if (Yii::$app->user->can('locationDelete'))
                            {
                                return Html::a('<i class="fa fa-trash"></i>', ['location-photo/delete', 'id'=>$model->id], [
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
}
?>
    </div>
