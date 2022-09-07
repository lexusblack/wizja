<?php

use common\models\GearService;
use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sprzęt');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-item-index">

    <p>
        <?php if ($user->can('gearItemCreate')) {
            echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
        } 
        echo " ".Html::a(Yii::t('app', 'Lista usuniętych'), ['deleted'], ['class' => 'btn btn-danger']);
        ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app', 'Nazwa'),
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($model) {
                    $service = GearService::getCurrentModel($model->id);
                    if ($service != null) {
                        return Html::a($model->name, ['view', 'id'=>$model->id]) . "<br>" . Html::a($model->getStatusLabel(), ['/gear-service/view', 'id'=>$service->id], ['class'=>'label label-danger']);
                    }
                    return Html::a($model->name, ['view', 'id'=>$model->id]);
                }
            ],
            [
                'attribute' => 'gear.name',
                'label' => Yii::t('app', 'Nazwa modelu'),
            ],
            [
                'label' => Yii::t('app', 'Kategoria'),
                'attribute' => 'gear.category.name',
            ],
            'number',
            'rfid_code',
            [
                'label' => Yii::t('app', 'Kod Qr'),
                'content' => function ($model) {
                    return $model->generateQrCodeAsLink();
                }
            ],
            [
                'label' => Yii::t('app', 'BarCode'),
                'content' => function ($model) {
                    return $model->generateBarCode();
                },
                'contentOptions' => ['style'=>'vertical-align: middle;'],
            ],
            [
                'label' => Yii::t('app', 'Wydaj z magazynu'),
                'content' => function ($model) {
                    if ($model->numberOfAvailable() > 0) {
                        return Html::a(Yii::t('app', 'Wydaj sprzęt'), ['outcomes-warehouse/create-start']) . "<br>" . Html::a(Yii::t('app', 'Przyjmij sprzęt'), ['incomes-warehouse/create-start']);
                    }
                    else {
                        return Yii::t('app', "Brak egzemplarzy w magazynie") . "<br>" . Html::a(Yii::t('app', 'Przyjmij sprzęt'), ['incomes-warehouse/create-start']);
                    }
                },
                'visible' => $user->can('eventRentsMagazin')
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update'=>$user->can('gearItemEdit'),
                    'delete'=>$user->can('gearItemDelete'),
                    'view'=>$user->can('gearItemView'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>

<?php
$this->registerJs('

$(".bar-code-img").click(function(){
    var url = $(this).find("object").attr("data") ;
    var link = document.createElement("a");
    link.href = url;
    link.download = "barcode.bmp";
    document.body.appendChild(link);
    link.click();
    link.remove();
});


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


');


$this->registerCss('
.bar-code-img { cursor: pointer; }
');