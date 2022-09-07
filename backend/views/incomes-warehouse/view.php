<?php

use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\GearItem;
use common\models\RentGearItem;
use common\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesWarehouse */

$event = \common\models\IncomesForEvent::find()->where(['income_id' => $model->id])->one();
$rent = \common\models\IncomesForRent::find()->where(['income_id' => $model->id])->one();
$customer = \common\models\IncomesForCustomer::find()->where(['income_id' => $model->id])->one();

$btnText = '';
$url = '';
if ($event) {
    $btnText = Yii::t('app', 'Event');
    $url = Url::toRoute(['event/view', 'id' => $event->event_id, '#' => 'tab-gear']);
}
if ($rent) {
    $btnText = Yii::t('app', 'Wypożyczenie');
    $url = Url::toRoute(['rent/view', 'id' => $rent->rent_id]);;
}

$this->title =Yii::t('app', 'Przyjęcie nr ').$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przyjęcia'), 'url' => ['index']];
if ($customer == null) {
    $this->params['breadcrumbs'][] = ['label' => $btnText, 'url' => $url];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="incomes-warehouse-view">
    <p>
        <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
    <div class="col-md-4">
    <div class="ibox float-e-margins">
        <div class="ibox-title newsystem-bg">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="ibox-content">
    <?= DetailView::widget([
        'model' => $model,
        'options' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap\'',
        ],
        'attributes' => [
            [
                'label' => Yii::t('app', 'Przyjął'),
                'value' => function($model) {
                    return User::find()->where(['id' => $model->user])->one()->username;
                }
            ],
                        [
                'label' => Yii::t('app', 'Magazyn'),
                'value' => function($model) {
                    return $model->warehouse->name;
                }
            ],
            'datetime',
            'comments:ntext',
            [
                'label' => Yii::t('app', 'Dla kogo'),
                'value' => function ($model) {
                    $customer = $model->getIncomesForCustomers();
                    $rent = $model->getIncomesForRents();
                    $event = $model->getIncomesForEvents();

                    $result = null;
                    if ($customer->count() == 1) {
                        $result = Yii::t('app', "Klienta").": " . $customer->one()->customer->name;
                    }
                    if ($rent->count() == 1) {
                        $result = Yii::t('app', "Wypożyczenia").": " . $rent->one()->rent->name;
                    }
                    if ($event->count() == 1) {
                        $result = Yii::t('app', "Eventu").": " . $event->one()->event->name;
                    }

                    return $result;

                },
            ],

            [
                'label' => Yii::t('app', 'Dokument'),
                'format' => 'raw',
                'value' => function ($model) {
                    if (count($model->incomesGearOurs) == 0 && count($model->incomesGearOuters) == 0) {
                        return null;
                    }
                    return Html::a(Html::icon('file'), ['incomes-warehouse/pdf', 'id' => $model->id], ['target' => '_blank']);
                }
            ],
        ],
    ]) ?>
        </div>
    </div>
    </div>
    <div class="col-md-8">
    <div class="ibox float-e-margins">
        <div class="ibox-title newsystem-bg">
            <h5><?=Yii::t('app', 'Przyjęty sprzęt') ?></h5>
        </div>
        <div class="ibox-content">
<?php
                    $our_gears = $model->getIncomesGearOurs()->all();
                    $outer_gears = $model->getIncomesGearOuters()->all();

                    $result = '';
                    foreach ($outer_gears as $gear) {
                        $not_planed = null;
                        if ($event) {
                            if (EventOuterGear::find()->where(['planned'=>1])->andWhere(['event_id'=>$event->event_id])->andWhere(['outer_gear_id'=>$gear->outer_gear_id])->andWhere(['quantity'=>$gear->gear_quantity])->count() == 0)  {
                                $not_planed = 'not_planned';
                            }
                        }

                        $result .= "<div class='gear_box'>".Html::tag("span", $gear->gear_quantity . "x " .$gear->outerGear->name . ". Firma: " . $gear->outerGear->company_name, ['class' => 'planned '] )."</div>";
                    }
                    $gear_list_planned = [];
                    $gear_list_unplanned = [];
                    $gear_group_planned = [];
                    $gear_group_unplanned = [];
                    foreach ($our_gears as $gear) {
                        $gear_item = GearItem::find()->where(['id' => $gear->gear_id])->one();
                        if ($event) {
                            if ( EventGearItem::find()->where(['planned'=>1])->andWhere(['event_id'=>$event->event_id])->andWhere(['gear_item_id'=>$gear->gear_id])->count() == 0) {
                                if ($gear_item->group_id == null) {
                                    $gear_list_unplanned[$gear_item->gear_id][] = $gear;
                                }
                                else {
                                    $gear_group_unplanned[$gear_item->gear_id][$gear_item->group_id][] = $gear_item;
                                }
                            }
                            else {
                                if ($gear_item->group_id == null) {
                                    $gear_list_planned[$gear_item->gear_id][] = $gear;
                                }
                                else {
                                    $gear_group_planned[$gear_item->gear_id][$gear_item->group_id][] = $gear_item;
                                }
                            }
                        }
                        if ($rent) {
                            if (RentGearItem::find()->where(['planned'=>1])->andWhere(['rent_id'=>$rent->rent_id])->andWhere(['gear_item_id'=>$gear->gear_id])->count() == 0) {
                                if ($gear_item->group_id == null) {
                                    $gear_list_unplanned[$gear_item->gear_id][] = $gear;
                                }
                                else {
                                    $gear_group_unplanned[$gear_item->gear_id][$gear_item->group_id][] = $gear_item;
                                }
                            }
                            else {
                                if ($gear_item->group_id == null) {
                                    $gear_list_planned[$gear_item->gear_id][] = $gear;
                                }
                                else {
                                    $gear_group_planned[$gear_item->gear_id][$gear_item->group_id][] = $gear_item;
                                }
                            }
                        }
                    }
                    foreach ($gear_list_planned as $gear_id => $gear_list) {
                        $numbers = null;
                        foreach ($gear_list as $gear_model) {
                            $numbers .= $gear_model->gear->number.", ";
                        }
                        if ($gear_list[0]->gear->name == '_ILOSC_SZTUK_') {
                            $result .= "<div class='gear_box'>" . Html::tag("span", $gear_list[0]->quantity. "x " . $gear_list[0]->gear->gear->name, ['class' => 'planned ']) . " </div>";
                        }
                        else {
                            $result .= "<div class='gear_box'>" . Html::tag("span", count($gear_list). "x " . $gear_list[0]->gear->name . ", ".Yii::t('app', 'numery').": " . $numbers, ['class' => 'planned ']) . " </div>";
                        }
                    }
                    foreach ($gear_list_unplanned as $gear_id => $gear_list) {
                        $numbers = null;
                        foreach ($gear_list as $gear_model) {
                            $numbers .= $gear_model->gear->number.", ";
                        }
                        if ($gear_list[0]->gear->name == '_ILOSC_SZTUK_') {
                            $result .= "<div class='gear_box'>" . Html::tag("span", $gear_list[0]->quantity. "x " . $gear_list[0]->gear->gear->name, ['class' => 'planned ']) . " </div>";
                        }
                        else {
                            $result .= "<div class='gear_box'>" . Html::tag("span", count($gear_list). "x " . $gear_list[0]->gear->name . ", ".Yii::t('app', 'numery').": " . $numbers, ['class' => 'planned ']) . " </div>";
                        }
                    }
                    foreach ($gear_group_planned as $gear_id => $groups) {
                        $number_list = null;
                        $name = null;
                        foreach ($groups as $items) {
                            $numbers = null;
                            $ids = [];
                            foreach ($items as $item) {
                                $name = $item->gear->name;
                                $numbers .= $item->number . ", ";
                                $ids[] = $item->number;
                            }
                            $in_order = true;
                            for ($i = min($ids); $i < max($ids); $i++) {
                                if (!in_array($i, $ids)) {
                                    $in_order = false;
                                }
                            }
                            if ($in_order) {
                                $numbers = min($ids) . "-" . max($ids) . ", ";
                            }

                            $number_list .= $numbers;
                        }

                        $result .= "<div class='gear_box'>" . Html::tag("span", count($groups). "x Case " . $name . ", ".Yii::t('app', 'numery').": " . $number_list, ['class' => 'planned ']) . " </div>";
                    }
                    foreach ($gear_group_unplanned as $gear_id => $groups) {
                        $number_list = null;
                        $name = null;
                        foreach ($groups as $items) {
                            $numbers = null;
                            $ids = [];
                            foreach ($items as $item) {
                                $name = $item->gear->name;
                                $numbers .= $item->number . ", ";
                                $ids[] = $item->number;
                            }
                            $in_order = true;
                            for ($i = min($ids); $i < max($ids); $i++) {
                                if (!in_array($i, $ids)) {
                                    $in_order = false;
                                }
                            }
                            if ($in_order) {
                                $numbers = min($ids) . "-" . max($ids) . ", ";
                            }

                            $number_list .= $numbers;
                        }

                        $result .= "<div class='gear_box'>" . Html::tag("span", count($groups). "x Case " . $name . ", ".Yii::t('app', 'numery').": " . $number_list, ['class' => 'planned ']) . " </div>";
                    }

                    echo $result; ?>
    </div>
    </div>
</div>
</div>

<?php

$this->registerCss('
    .gear_box { margin-bottom: 1px; }
    .planned {  padding-left: 5px; padding-right: 5px;  }
    .not_planned { background-color: orangered; color: white;} 

');