<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Room */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Pokój'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?=  Yii::t('app', 'Pokój').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a( Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a( Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' =>  Yii::t('app', 'Czy na pewno chcesz usunąć?'),
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
        'podkowa',
        'bankiet',
        'teatr',
        [
            'attribute' => 'location.name',
            'label' =>  Yii::t('app', 'Lokacja'),
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
    
    <div class="row">
<?php
if($providerRoomPhoto->totalCount){
    $gridColumnRoomPhoto = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'filename',
            'extension',
            'status',
                        'mime_type',
            'base_name',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerRoomPhoto,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-room-photo']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode( Yii::t('app', 'Zdjęcie')),
        ],
        'export' => false,
        'columns' => $gridColumnRoomPhoto
    ]);
}
?>
    </div>
</div>
