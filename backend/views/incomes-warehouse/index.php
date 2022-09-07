<?php

use common\models\User;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\helpers\Enum;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IncomesWarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Przyjęcie do magazynu');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<div class="incomes-warehouse-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p> <?php
        if ($user->can('eventRentsMagazin')) {
            echo Html::a(Yii::t('app', 'Przyjmij sprzęt'), ['create-start'], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector'=>'.grid-filters',
        'toolbar' => [
                [
                    'content' =>
                        Html::beginForm('', 'get', ['class'=>'form-inline']) .
                        Html::activeDropDownList($searchModel, 'year', Enum::yearList(2016, (date('Y')), true), ['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'rok')])
                        . Html::activeDropDownList($searchModel, 'month', Enum::monthList(),['class'=>'form-control grid-filters', 'prompt'=>Yii::t('app', 'miesiąc')])
                            .Html::endForm()
                ],
                '{export}',

                ],
        'columns' => [
            [
                        'label' => Yii::t('app', 'Numer'),
                        'format'=>'html',
                        'attribute'=>'id',
                        'value' => function($model) {
                            return Html::a($model->id, ['/incomes-warehouse/view', 'id' => $model->id]);
                        }
                    ],
            [
                'label' => Yii::t('app', 'Nazwa imprezy'),
                 'format'=>'html',
                'attribute'=>'event',
                'value' => function($model) {
                    $customer = $model->getIncomesForCustomers();
                    $rent = $model->getIncomesForRents();
                    $event = $model->getIncomesForEvents();

                    if ($customer->count() == 1) {
                                $result = $customer->one()->customer->name;
                            }
                            if ($rent->count() == 1) {
                                $res = $rent->one();
                                $result = Html::a($res->rent->name.' ['.$res->rent->code.']', ['/rent/view', 'id' => $res->rent->id]);
                            }
                            if ($event->count() == 1) {
                                $res = $event->one();
                                $result = Html::a($res->event->name.' ['.$res->event->code.']', ['/event/view', 'id' => $res->event->id]);
                            }
                            return $result;
                }
            ],
            [
                'label' => Yii::t('app', 'Typ imprezy'),
                'value' => function($model) {
                    $customer = $model->getIncomesForCustomers();
                    $rent = $model->getIncomesForRents();
                    $event = $model->getIncomesForEvents();

                    $result = null;
                    if ($customer->count() == 1) {
                        $result = Yii::t('app', "Bez imprezy, klient");
                    }
                    if ($rent->count() == 1) {
                        $result = Yii::t('app', "Wypożyczenie");
                    }
                    if ($event->count() == 1) {
                        $result = Yii::t('app', "Event");
                    }
                    return $result;
                }
            ],
            [
                'label' => Yii::t('app', 'Przyjęty sprzęt'),
                'format' => 'raw',
                'value' => function ($model) {
                    $our_gears = $model->getIncomesGearOurs()->all();
                    $outer_gears = $model->getIncomesGearOuters()->all();

                    $result = '';
                    foreach ($outer_gears as $gear) {
                        $result .= "<div class='one_row'>" . $gear->gear_quantity . 'x ' . $gear->outerGear->name . ", ".Yii::t('app', 'firma').": " . $gear->outerGear->company_name . ". ".Yii::t('app', 'Magazyn: zew.')."</div>";
                    }
                    $gear_list = [];
                    $group_list = [];
                    foreach ($our_gears as $gear) {
                        if ($gear->gear->group_id != null) {
                            $group_list[$gear->gear->gear_id][$gear->gear->group_id][] = $gear->gear;
                            continue;
                        }
                        $gear_list[$gear->gear->gear_id][] = $gear;
                    }
                    foreach ($gear_list as $id => $gears) {
                        $numbers = null;
                        $count = 0;
                        foreach ($gears as $gear) {
                            $count++;
                            $numbers .= $gear->gear->number . ", ";
                        }
                        if ($gears[0]->gear->name == '_ILOSC_SZTUK_') {
                            $result .= "<div class='one_row'>" . $gear->quantity . "x " . $gears[0]->gear->gear->name . " ".Yii::t('app', 'Magazyn: wew.')."</div>";

                        }
                        else {
                            $result .= "<div class='one_row'>" . $count . "x " . $gears[0]->gear->gear->name . " " . $gears[0]->gear->name . ", ".Yii::t('app', 'numery').": " . $numbers . " ".Yii::t('app', 'Magazyn: wew.')."</div>";
                        }
                    }
                    foreach ($group_list as $gear_id => $group_arr) {
                        $numbers = null;
                        $name = null;
                        foreach ($group_arr as $group_id => $group_items) {
                            $numer_list = null;
                            $ids = [];
                            foreach ($group_items as $item) {
                                $name = $item->gear->name;
                                $numer_list .= $item->number .", ";
                                $ids[] = $item->number;
                            }
                            $in_order = true;
                            for ($i = min($ids); $i < max($ids); $i++) {
                                if (!in_array($i, $ids)) {
                                    $in_order = false;
                                }
                            }
                            if ($in_order) {
                                $numer_list = min($ids) . "-" . max($ids).", ";
                            }
                            $numbers .= $numer_list;
                        }
                        $result .= "<div class='one_row'>" . count($group_arr) . "x Case " . $name . ", ".Yii::t('app', 'numery').": " . $numbers . " ".Yii::t('app', 'Magazyn: wew.')."</div>";
                    }

                    return "<div class='rolled-gear' data-id='".$model->id."'>".Yii::t('app', 'Rozwiń')."</div><div class='rolled-gear' style='display: none;' data-id='".$model->id."'>".$result."</div>";
                }
            ],
            [
                'label' => Yii::t('app', 'Data przyjęcia'),
                'attribute' => 'datetime',
                'value' => function($model) {
                    return $model->datetime;
                }
            ],
            [
                'label' => Yii::t('app', 'Przyjął'),
                'filter'=> \common\models\User::getList(),
                'attribute'=>'user',
                'value' => function($model) {
                    return User::find()->where(['id' => $model->user])->one()->displayLabel;
                }
            ],
            [
                'label' => Yii::t('app', 'Komentarz'),
                'value' => function($model) {
                    return $model->comments;
                }
            ],

            [
                'label' => Yii::t('app', 'Dokument'),
                'content' => function ($model) {
                    if (count($model->incomesGearOurs) == 0 && count($model->incomesGearOuters) == 0) {
                        return null;
                    }
                    return Html::a(Html::icon('file'), ['incomes-warehouse/pdf', 'id' => $model->id], ['target' => '_blank']);
                },
                'visible' => $user->can('gearWarehouseIncomesViewPdf')
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{delete}',
                'visibleButtons' => [
                    'delete'=>$user->can('gearWarehouseIncomesDelete'),
                    'view'=>$user->can('gearWarehouseIncomesView'),
                ],
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
    $(".rolled").click(function(){
        $(".rolled[data-id=\'"+$(this).data("id")+"\']").toggle();
    });
    $(".rolled-gear").click(function(){
        $(".rolled-gear[data-id=\'"+$(this).data("id")+"\']").toggle();
    });


');

$this->registerCss('
    .rolled { cursor: pointer; }
    .rolled-gear { cursor: pointer; }
    .one_row { white-space: nowrap; }
');