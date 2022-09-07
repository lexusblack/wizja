<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\GearConnected */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gear Connected', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-connected-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Gear Connected'.' '. Html::encode($this->title) ?></h2>
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
        [
            'attribute' => 'gear.name',
            'label' => 'Gear',
        ],
        [
            'attribute' => 'connected.name',
            'label' => 'Connected',
        ],
        'quantity',
        'checked',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>Gear<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnGear = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'quantity',
        'available',
        'brightness',
        'power_consumption',
        'status',
        'type',
        'category_id',
        'width',
        'height',
        'volume',
        'depth',
        'weight',
        'weight_case',
        'info',
        'photo',
        'group_id',
        'create_time',
        'update_time',
        'price',
        'no_items',
        'sort_order',
        'active',
        'visible_in_offer',
        'visible_in_warehouse',
        'value',
    ];
    echo DetailView::widget([
        'model' => $model->connected,
        'attributes' => $gridColumnGear    ]);
    ?>
    <div class="row">
        <h4>Gear<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnGear = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'quantity',
        'available',
        'brightness',
        'power_consumption',
        'status',
        'type',
        'category_id',
        'width',
        'height',
        'volume',
        'depth',
        'weight',
        'weight_case',
        'info',
        'photo',
        'group_id',
        'create_time',
        'update_time',
        'price',
        'no_items',
        'sort_order',
        'active',
        'visible_in_offer',
        'visible_in_warehouse',
        'value',
    ];
    echo DetailView::widget([
        'model' => $model->gear,
        'attributes' => $gridColumnGear    ]);
    ?>
</div>
