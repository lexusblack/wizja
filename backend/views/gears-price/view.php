<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearsPrice */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gears Price', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gears-price-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Gears Price'.' '. Html::encode($this->title) ?></h2>
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
        'gear_id',
        'gear_category_id',
        'currency',
        'type',
        'vat',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerGearPrice->totalCount){
    $gridColumnGearPrice = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'gear.name',
                'label' => 'Gear'
            ],
                        'price',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerGearPrice,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-price']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Gear Price'),
        ],
        'export' => false,
        'columns' => $gridColumnGearPrice
    ]);
}
?>

    </div>
</div>
