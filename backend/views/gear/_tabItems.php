<?php

use common\models\GearItem;
use common\models\GearService;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Egzemplarze'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="ibox">
        <?php
        $user = Yii::$app->user;
        if ($user->can('gearCreate') || $user->can('gearEdit')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['gear-item/create', 'gearId' => $model->id], ['class' => 'btn btn-success'])." ";
        }
        if ($user->can('gearWarehouseOutcomes')){
            echo Html::a(Yii::t('app', 'Przesuń zaznaczone'), ['/gear/edit-items', 'id' => $model->id, 'type'=>3], ['class' => 'btn btn-success move-items-button']);
        }
        ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedItems(),
            'id'=>'items-grid',
            'columns' => [
                                [
                        'class' => 'yii\grid\CheckboxColumn',
                        'header' => null
                    ],
                [
                            'attribute' => 'number',
                            'label' => Yii::t('app', 'Numer'),
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value'=>function($model)
                    {
                        return Html::a($model->name, ['gear-item/view', 'id'=>$model->id], ['target' => '_blank']);
                    },
                ],
                [
                    'label' => Yii::t('app', 'Kod kreskowy'),
                    'encodeLabel'=>false,
                    'value'=>function($model)
                    {
                        return $model->getBarCodeValue();
                    }
                ],
                'serial',
                                [
                    'attribute' => 'warehouse_id',
                    'label' => Yii::t('app', 'Magazyn'),
                    'format' => 'html',
                    'value' => function($gear) {
                        if ($gear->warehouse_id)
                        {
                            return $gear->warehouseModel->name;
                        }else{
                            if ( $gear->event_id)
                            {
                                return Html::a($gear->event->name, ['event/view', 'id'=>$gear->event_id], ['target' => '_blank']);
                            }
                            if ( $gear->rent_id)
                            {
                                return Html::a($gear->rent->name, ['rent/view', 'id'=>$gear->rent_id], ['target' => '_blank']);
                            }
                        }
                    }
                ],
                [
                    'attribute' => 'location',
                    'label' => Yii::t('app', 'Miejsce w<br/>magazynie'),
                    'encodeLabel'=>false,
                ],
                'rfid_code',
                [
                        'attribute' => 'description',
                        'format' => 'html',
                ],
                [
                    'attribute' => 'info',
                    'label' => Yii::t('app', 'Uwagi'),
                    'format' => 'html',
                    'value' => function($gear) {
                        $service = GearService::getCurrentModel($gear->id);
                                if ($service != null) {
                                    return $gear->info. " " . Html::a($service->serviceStatus->name, ['/gear-service/view', 'id'=>$service->id], ['class'=>'label', 'style'=>'color:white; background-color:'.$service->serviceStatus->color]);
                                }
                                if ($gear->status == GearItem::STATUS_SERVICE) {
                                    return $gear->info . " " . Html::tag('span', $gear->getStatusLabel(), ['class'=>'label label-danger']);
                                }
                        return $gear->info;
                    }
                ],

                [
                    'class'=>\common\components\ActionColumn::className(),
                    'controllerId'=>'gear-item',
                    'visibleButtons' => [
                        'update'=>$user->can('gearItemEdit'),
                        'delete'=>$user->can('gearItemDelete'),
                        'view'=>$user->can('gearItemView'),
                    ]
                ],
            ],
        ]);
        ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php $this->registerJs('
$(".move-items-button").click(function(e){ 
e.preventDefault();
var keys = $("#items-grid").yiiGridView("getSelectedRows");
var href = $(this).attr("href")+"&items="+keys;
location.href = href;
});
    ');
$this->registerCss('
.kv-grid  input[type="checkbox"]
{
    transform: scale(1); 
    -ms-transform: scale(1);
    -webkit-transform: scale(1); 
    -o-transform: scale(1);
    -moz-transform: scale(1);
}
    ');
