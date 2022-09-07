<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Type', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-type-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Purchase Type'.' '. Html::encode($this->title) ?></h2>
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
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerPurchase->totalCount){
    $gridColumnPurchase = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'user.username',
                'label' => 'User'
            ],
            'section',
            'code',
            'description:ntext',
            'price',
                        'type',
            'group_id',
            'datetime',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerPurchase,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-purchase']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Purchase'),
        ],
        'export' => false,
        'columns' => $gridColumnPurchase
    ]);
}
?>

    </div>
</div>
