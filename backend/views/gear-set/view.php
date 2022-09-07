<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\grid\GridView;
$user = Yii::$app->user;

/* @var $this yii\web\View */
/* @var $model common\models\GearSet */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestawy urządzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gear-set-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            <?php if ($user->can('gearSetUpdate')) { ?>
            <?= Html::a(Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } ?>
            <?php if ($user->can('gearSetDelete')) { ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
            <?php } ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        [
            'attribute'=>'category_id',
            'value'=>function($model){
                return $model->category->name;
            }
        ],
        'create_time',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
    <h3><?=Yii::t('app', 'Sprzęt wewnętrzny')?></h3>
<?php
if($providerGearSetItem->totalCount){
    $gridColumnGearSetItem = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'gear.name',
                'label' => 'Urządzenie'
            ],
                        'quantity',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerGearSetItem,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-set-item']],
        'export' => false,
        'columns' => $gridColumnGearSetItem
    ]);
}
?>

    </div>
    <div class="row">
    <h3><?=Yii::t('app', 'Sprzęt zewnętrzny')?></h3>
<?php
if($providerGearSetOuterItem->totalCount){
    $gridColumnGearSetItem = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'outerGearModel.name',
                'label' => 'Urządzenie'
            ],
                        'quantity',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerGearSetOuterItem,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-gear-set-item']],
        'export' => false,
        'columns' => $gridColumnGearSetItem
    ]);
}
?>
    </div>
</div>
