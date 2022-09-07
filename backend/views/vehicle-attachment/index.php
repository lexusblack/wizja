<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\VehicleAttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Załączniki');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="vehicle-attachment-index">

    <p>
        <?php
        if ($user->can('fleetAttachmentsCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'vehicle_id',
                'value' =>'vehicle.name',
                'filter'=>\common\models\Vehicle::getModelList(),
            ],
            'filename',
            'extension',


            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('fleetAttachmentsEdit'),
                    'delete'=>$user->can('fleetAttachmentsDelete'),
                    'view'=>$user->can('fleetAttachmentsView'),
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>