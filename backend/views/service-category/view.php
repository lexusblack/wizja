<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Service Category', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-category-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Service Category'.' '. Html::encode($this->title) ?></h2>
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
        'in_offer',
        'position',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerService->totalCount){
    $gridColumnService = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'name',
                        'in_offer',
            'position',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerService,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-service']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Service'),
        ],
        'export' => false,
        'columns' => $gridColumnService
    ]);
}
?>

    </div>
</div>
