<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Typ'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-type-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Yii::t('app', 'Typ').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'create_type',
        'update_type',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
    
    <div class="row">
<?php
if($providerLocation->totalCount){
    $gridColumnLocation = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'name',
            'address',
            'city',
            'zip',
            'country',
            'info:ntext',
            'latitude',
            'longitude',
            'type',
            'status',
            'travel_time',
            'manager_phone',
            'electrician_phone',
            'distance',
            'photo',
            'rent_price',
            'owner_id',
            'video',
            'description:ntext',
            'stars',
            [
                'attribute' => 'province.name',
                'label' => Yii::t('app', 'Województwo')
            ],
            'beds',
            'website',
            'biggest_room',
            'email:email',
                ];
    echo Gridview::widget([
        'dataProvider' => $providerLocation,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-location']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Location'),
        ],
        'export' => false,
        'columns' => $gridColumnLocation
    ]);
}
?>
    </div>
</div>
