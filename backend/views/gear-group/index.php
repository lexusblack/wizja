<?php

use common\models\GearService;
use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GearGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Zestawy sprzętu');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="gear-group-index">

    <p>
        <?php if ($user->can('gearOurWarehouseCreateCase')) {
            //echo Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']);
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
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    $content = Html::a($model->name, ['view', 'id'=>$model->id]);
                    return $content;
                },
                'format' => 'html',
            ],
            [
                'label' => Yii::t('app', 'Urządzenia w case'),
                'format' => 'html',
                'value' => function($group) {
                    $result = null;
                    foreach ($group->gearItems as $gear) {
                        $service = GearService::getCurrentModel($gear->id);

                        $service_text = null;
                        if ($service != null) {
                            $service_text = Html::a($gear->getStatusLabel(), ['/gear-service/view', 'id'=>$service->id], ['class'=>'label label-danger']);
                        }
                        $result .= $gear->name . " ".Yii::t('app', 'numer').": " . $gear->number . ", ".Yii::t('app', 'kod').": " . $gear->getBarCodeValue() . " " . $service_text ."<br>";
                    }
                    return $result;
                }
            ],
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
                    'update'=>$user->can('gearCaseEdit'),
                    'delete'=>$user->can('gearCaseDelete'),
                    'view'=>$user->can('gearCaseView'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>
<?php

$this->registerJs('

    $("object").each(function(){
        var data = $(this).attr("data");
        var name = $(this).parent().data("name");

        $(this).wrap("<a href=\'" + data + "\' download=\'" + name + ".bmp\'></a>");
    });

');