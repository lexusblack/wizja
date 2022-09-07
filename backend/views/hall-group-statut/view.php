<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupStatut */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hall Group Statut', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hall-group-statut-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Hall Group Statut'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
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
        'color',
        'active',
        'position',
        'final',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerEventHallGroup->totalCount){
    $gridColumnEventHallGroup = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'event.name',
                'label' => 'Event'
            ],
            [
                'attribute' => 'hallGroup.name',
                'label' => 'Hall Group'
            ],
            'start_time',
            'end_time',
                ];
    echo Gridview::widget([
        'dataProvider' => $providerEventHallGroup,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-event-hall-group']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Event Hall Group'),
        ],
        'export' => false,
        'columns' => $gridColumnEventHallGroup
    ]);
}
?>

    </div>
</div>
