<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\EventExtraItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Extra Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-extra-item-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Event Extra Item'.' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'offerExtraItem.name',
            'label' => 'Offer Extra Item',
        ],
        'name',
        'quantity',
        [
            'attribute' => 'gearCategory.name',
            'label' => 'Gear Category',
        ],
        'weight',
        'volume',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>GearCategory<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnGearCategory = [
        ['attribute' => 'id', 'visible' => false],
        'root',
        'lft',
        'rgt',
        'lvl',
        'name',
        'icon',
        'icon_type',
        'active',
        'selected',
        'disabled',
        'readonly',
        'visible',
        'collapsed',
        'movable_u',
        'movable_d',
        'movable_l',
        'movable_r',
        'removable',
        'removable_all',
        'color',
    ];
    echo DetailView::widget([
        'model' => $model->gearCategory,
        'attributes' => $gridColumnGearCategory    ]);
    ?>
    <div class="row">
        <h4>OfferExtraItem<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnOfferExtraItem = [
        ['attribute' => 'id', 'visible' => false],
        'offer_id',
        'name',
        'quantity',
        'price',
        'duration',
        'category_id',
        'type',
        'discount',
        'name_in_offer',
        'description',
        'first_day_percent',
        'visible',
        'import',
    ];
    echo DetailView::widget([
        'model' => $model->offerExtraItem,
        'attributes' => $gridColumnOfferExtraItem    ]);
    ?>
</div>
