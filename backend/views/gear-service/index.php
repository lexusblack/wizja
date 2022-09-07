<?php

use kartik\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Serwis');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-service-index">

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
                'attribute'=>'gear_item_id',
                'format'=>'html',
                'value'=>
                function ($gear) {
                            if ($gear->gearItem->gear->no_items)
                                return Html::a($gear->gearItem->gear->name. " [" . $gear->quantity."]",['/gear-service/view', 'id'=>$gear->id]);
                            else    
                                return Html::a($gear->gearItem->name. " [" . $gear->gearItem->number."]",['/gear-service/view', 'id'=>$gear->id]);
                        },
                'filterType'=>GridView::FILTER_SELECT2,
                'filterWidgetOptions'=> [
                    'data'=>\common\models\GearService::getGearItemsList(),
                    'pluginOptions'=>[
                        'placeholder'=>'',
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'attribute'=>'status',
                'value'=>'statusLabel',
                'filter'=>\common\models\GearService::getStatusList(),
            ],
            'status_time',
            'create_time',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('gearServiceUpdate'),
                    'delete'=>false,
                    'view'=>$user->can('gearServiceView'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>

<?php
$this->registerJs('



$(".table-bordered").each(function(){
$(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
$(this).removeClass("table-striped");
});


');