<?php

use common\models\EventGear;
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\RentGearItem;
use common\models\RentGear;
use yii\bootstrap\Html;

$gear_item_out = [];
$gear_group_out = [];
$gear_outer_out = [];


if (!$event && !$rent) { ?>

    <div class="panel_mid_blocks">
        <div class="panel_block">
            <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">
                <tr>
                    <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
                    <th style="width: 100px;"><?= Yii::t('app', 'Wydanie') ?></th>
                    <th><?= Yii::t('app', 'Zdjęcie') ?></th>
                    <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
                    <th><?= Yii::t('app', 'L. zapotrzebowanie') ?></th>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th>><?= Yii::t('app', 'Numery') ?></th>
                    <th><?= Yii::t('app', 'Magazyn') ?></th>
                    <th></th>
                </tr>
            </table>
        </div>
    </div>
    <?php

}

// ------- TABELKA z listą sprzętu ------- //
if ($event) {

    $gear_event_items = EventGearItem::find()->where(['event_id' => $event])->all();
    $gear_event = EventGear::find()->where(['event_id'=>$event])->all();
    $outcomes = OutcomesForEvent::find()->where(['event_id' => $event])->all();

    foreach ($outcomes as $outcome) {
        foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
            $gear_item_out[] = $gearOur->gear_id;
        }
        foreach (OutcomesGearOuter::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOuter) {
            $gear_outer_out[] = $gearOuter->outer_gear_id;
        }
    }
    $gear_models = [];
    foreach ($gear_event as $gear)
    {
        $gear_models[$gear->gear_id]['gear'] = $gear->gear;
        $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
        $gear_models[$gear->gear->id]['items'] = [];
    }
    foreach ($gear_event_items as $gear_event_item)
    {
        $gear_item = $gear_event_item->gearItem;
        $gear_models[$gear_item->gear_id]['items'][] = $gear_item;
    }

    ?>

    <div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
        <div class="ibox-content">
        <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">

            <tr class="newsystem-bg">
            <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Wydanie') ?></th>
            <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
            <th><?= Yii::t('app', 'L. zapotrzebowanie') ?></th>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Numery') ?></th>
            <th><?= Yii::t('app', 'Magazyn') ?></th>
            <th></th>

            </tr><?php
            $scanned = [];
            if (isset($_COOKIE['checkbox-item-gear'])) {
                foreach ($_COOKIE['checkbox-item-gear'] as $id => $val) {
                    $gear_item = GearItem::find()->where(['id'=>$id])->one();
                    $gear_event_item = EventGearItem::find()->where(['event_id' => $event, 'gear_item_id'=>$id])->one();
                    if (!$gear_event_item)
                    {
                        $gear_models[$gear_item->gear_id]['items'][] = $gear_item;
                    }
                    if (!isset($gear_models[$gear_item->gear_id]['gear']))
                    {
                        $gear_models[$gear_item->gear_id]['gear'] = $gear_item->gear;
                        $gear_models[$gear_item->gear_id]['quantity'] = 0;
                    }
                    if (!$gear_item->gear->no_items)                   
                        $added_numbers[$gear_item->gear_id]['items'][$gear_item->id] = $gear_item->number;
                    if (!isset($scanned[$gear_item->gear_id])) {
                            if ($gear_item->gear->no_items)
                            {
                                $scanned[$gear_item->gear_id] = $val;
                            }else{
                                $scanned[$gear_item->gear_id] = 1;
                            }
                            
                    }
                    else {
                            $scanned[$gear_item->gear_id]++;
                    }
                }
            }

            foreach ($gear_models as $model_id => $gearsM) {
                $gears = $gearsM['items'];
                if (!isset($scanned[$model_id])) {
                    $scanned[$model_id] = 0;
                }
                $gear_model = $gearsM['gear'];
                $greenRowClass = null;
                $checked = null;
                $added_numbers_list = null;
                if (isset($added_numbers[$model_id])) {
                    foreach ($added_numbers[$model_id] as $name => $list) {
                        if ($name == 'items') {
                            foreach ($list as $id => $number) {
                                $added_numbers_list .= Html::tag("span", $number . ", ", ['class' => 'item-in-basket number-list-gear-'.$id]);
                            }
                        }
                    }
                }
                $noItemsClass = null;
                if ($gear_model->no_items == 1) {
                    $noItemsClass = "gear-no-items-row";
                    $item_no = GearItem::findOne(['gear_id'=>$gear_model->id]);
                }

                ?>
                <tr class="<?= $greenRowClass. " " .$noItemsClass ?> gear-row" data-gearid="<?= $gear_model->id ?>" data-gearquantity="<?=$gearsM['quantity']?>">
                    <td><?php echo $gear_model->id; ?>
                        <?php if ($gear_model->no_items != 1) { echo Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); } ?>
                    </td>
                    <td><input type="checkbox" class="gear-our" <?php if ($gear_model->no_items != 1) { echo 'disabled="disabled"'; }else{ echo "data-id=".$item_no->id;}?> data-gearid="<?= $gear_model->id ?>" <?= $checked ?>/></td>
                    <td><?= $scanned[$model_id] ?></td>
                    <td><?= $gearsM['quantity']?></td>
                    <td><?= $gear_model->name; ?><?=$greenRowClass?></td>
                    <td class="number-list-model-<?= $gear_model->id ?>"><?= $added_numbers_list ?></td>
                    <td><?= Yii::t('app', 'Wewnętrzny') ?></td>
                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_model', 'data' => ['gearid' => $gear_model->id]]) ?></td>
                </tr><?php

                if (1 == 1) { ?>
                    <tr style="display: none;" class="sub_models">
                        <td colspan="9">
                            <table class="kv-grid-table table kv-table-wrap" style="width: 70%; margin: auto;">
                                <thead>
                                <td><?= Yii::t('app', 'Id') ?></td>
                                <td><?= Yii::t('app', 'Wydanie') ?></td>
                                <td><?= Yii::t('app', 'Nazwa') ?></td>
                                <td><?= Yii::t('app', 'Numery urządzeń') ?></td>
                                <td></td>
                                </thead><tbody><?php
                                $group_displayed = [];
                                foreach ($gears as $gear) {
                                        $greenRowClass = null;
                                        $checked = null;
                                        if (isset($_COOKIE['checkbox-item-gear'][$gear->id])) {
                                            $checked = 'checked';
                                            $greenRowClass = 'item-in-basket';
                                        }
                                        ?>
                                        <tr class="<?= $greenRowClass ?> gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                        <td><?= $gear->id ?></td>
                                        <td><input type="checkbox" class="gear-item-our" data-id="<?= $gear->id ?>" data-gearid="<?= $gear->gear_id ?>" data-number="<?= $gear->number ?>" <?= $checked ?>/> </td>
                                        <td><?= $gear->name ?></td>
                                        <td><span class="checkbox-item-gear" data-id="<?= $gear->id ?>"><?= $gear->number ?></span></td>
                                        <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_model', 'data' => ['id' => $gear->id]]) ?></td>
                                        <?php
                                } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <?php
                }
            }

            $gear_outer_items = EventOuterGear::find()->where(['event_id' => $event])->all();
            $cookies = [];
            $scanned_outer = [];
            if (isset($_COOKIE['checkbox-item-outer-id'])) {
                foreach ($_COOKIE['checkbox-item-outer-id'] as $id => $value) {
                    $cookies[] = $id;
                    if (isseT($scanned_outer[$id])) {
                        $scanned_outer[$id] += $value;
                    }
                    else {
                        $scanned_outer[$id] = $value;
                    }
                }
                foreach ($gear_outer_items as $gear) {
                    if (in_array($gear->outer_gear_id, $cookies)) {
                        unset($cookies[array_search($gear->outer_gear_id, $cookies)]);
                    }
                }
            }
            foreach ($gear_outer_items as $gear) {
                if (!in_array($gear->outer_gear_id, $gear_outer_out)) {
                    if (!isset($scanned_outer[$gear->outerGear->id])) {
                        $scanned_outer[$gear->outerGear->id] = 0;
                    }
                    $greenRowClass = null;
                    $checked = null;
                    if (isset($_COOKIE['checkbox-item-outer-id'][$gear->outerGear->id])) {
                        $greenRowClass = 'item-in-basket';
                        $checked = 'checked';
                    }
                    if ($gear->quantity == null) { ?>
                        <tr class="<?= $greenRowClass ?> gear-item-outer-row" data-itemouterid="<?= $gear->outerGear->id ?>">
                            <td><?= $gear->outerGear->id ?></td>
                            <td><input type="checkbox" class="gear-item-outer" data-id="<?= $gear->outerGear->id ?>" <?= $checked ?>/> </td>
                            <td><?php
                                if ($gear->outerGear->photo != null) {
                                    echo Html::img($gear->outerGear->getPhotoUrl(), ['width' => '100px']);
                                } ?></td>
                            <td><?= $scanned_outer[$gear->outerGear->id] ?></td>
                            <td>1</td>
                            <td><?= $gear->outerGear->name ?></td>
                            <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                            <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->outerGear->id]]) ?></td>
                        </tr><?php
                    }
                    else { ?>
                        <tr class="<?= $greenRowClass ?> gear-item-outer-row" data-itemouterid="<?= $gear->outerGear->id ?>">
                            <td><?= $gear->outerGear->id ?></td>
                            <td><input type="checkbox" class="gear-item-outer" data-id="<?= $gear->outerGear->id ?>" <?= $checked ?> /> </td>
                            <td><?php
                                if ($gear->outerGear->photo != null) {
                                    echo Html::img($gear->outerGear->getPhotoUrl(), ['width' => '100px']);
                                } ?></td>
                            <td><?= $scanned_outer[$gear->outerGear->id] ?></td>
                            <td><?= $gear->quantity ?></td>
                            <td><?= $gear->outerGear->name;
                                echo "<br>".Yii::t('app', "Numer").": " . $gear->outerGear->getBarCodeValue(); ?></td>
                            <td></td>
                            <td><?= Yii::t('app', 'Zewnętrzny')?></td>
                            <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->outerGear->id]]) ?></td>
                        </tr><?php

                    }
                }
            }
            foreach ($cookies as $id) {
                $gear = \common\models\OuterGear::find()->where(['id'=>$id])->one();
                ?>
                <tr class="item-in-basket gear-item-outer-row" data-itemouterid="<?= $id ?>">
                    <td><?= $id ?></td>
                    <td><input type="checkbox" class="gear-item-outer" data-id="<?= $id ?>" checked/> </td>
                    <td><?php
                        if ($gear->photo != null) {
                            echo Html::img($gear->getPhotoUrl(), ['width' => '100px']);
                        } ?></td>
                    <td><?= $_COOKIE['checkbox-item-outer-id'][$id] ?></td>
                    <td><?= Yii::t('app', 'zapotrzebowanie tu bedzie') ?></td>
                    <td><?= $gear->name ?></td>
                    <td></td>
                    <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->id]]) ?></td>
                </tr><?php
            }
            ?>


        </table>
        </div>
        </div>
    </div>
    </div><?php

}

// ------- TABELKA z listą sprzętu ------- //
if ($rent) {

    $gear_rent_items = RentGearItem::find()->where(['rent_id' => $rent])->all();
    $outcomes = OutcomesForRent::find()->where(['rent_id' => $rent])->all();
    $gear_rent = RentGear::find()->where(['rent_id'=>$rent])->all();

    foreach ($outcomes as $outcome) {
        foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
            $gear_item_out[] = $gearOur->gear_id;
        }
        foreach (OutcomesGearOuter::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOuter) {
            $gear_outer_out[] = $gearOuter->outer_gear_id;
        }
    }
    $gear_models = [];
    foreach ($gear_rent as $gear)
    {
        $gear_models[$gear->gear_id]['gear'] = $gear->gear;
        $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
        $gear_models[$gear->gear->id]['items'] = [];
    }
    foreach ($gear_rent_items as $gear_rent_item)
    {
        $gear_item = $gear_rent_item->gearItem;
        $gear_models[$gear_item->gear_id]['items'][] = $gear_item;
    }

    ?>

    <div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
        <div class="ibox-content">
        <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">

            <thead>
            <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Wydanie') ?></th>
            <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
            <th><?= Yii::t('app', 'L. zapotrzebowanie') ?></th>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Numery') ?></th>
            <th><?= Yii::t('app', 'Magazyn') ?></th>
            <th></th>

            </thead><?php

            $scanned = [];
            if (isset($_COOKIE['checkbox-item-gear'])) {
                foreach ($_COOKIE['checkbox-item-gear'] as $id => $val) {
                    $gear_item = GearItem::find()->where(['id'=>$id])->one();
                    $gear_event_item = RentGearItem::find()->where(['rent_id' => $event, 'gear_item_id'=>$id])->one();
                    if (!$gear_event_item)
                    {
                        $gear_models[$gear_item->gear_id]['items'][] = $gear_item;
                    }
                    if (!isset($gear_models[$gear_item->gear_id]['gear']))
                    {
                        $gear_models[$gear_item->gear_id]['gear'] = $gear_item->gear;
                        $gear_models[$gear_item->gear_id]['quantity'] = 0;
                    }                    
                    $added_numbers[$gear_item->gear_id]['items'][$gear_item->id] = $gear_item->number;
                    if (!isset($scanned[$gear_item->gear_id])) {
                            $scanned[$gear_item->gear_id] = $val;
                    }
                    else {
                            $scanned[$gear_item->gear_id]++;
                    }
                }
            }

            foreach ($gear_models as $model_id => $gearsM) {
                $gears = $gearsM['items'];
                if (!isset($scanned[$model_id])) {
                    $scanned[$model_id] = 0;
                }
                $gear_model = $gearsM['gear'];
                $greenRowClass = null;
                $checked = null;
                $added_numbers_list = null;
                if (isset($added_numbers[$model_id])) {
                    foreach ($added_numbers[$model_id] as $name => $list) {
                        if ($name == 'items') {
                            foreach ($list as $id => $number) {
                                $added_numbers_list .= Html::tag("span", $number . ", ", ['class' => 'item-in-basket number-list-gear-'.$id]);
                            }
                        }
                    }
                }
                $noItemsClass = null;
                if ($gear_model->no_items == 1) {
                    $noItemsClass = "gear-no-items-row";
                }

                ?>
                <tr class="<?= $greenRowClass. " " .$noItemsClass ?> gear-row" data-gearid="<?= $gear_model->id ?>" data-gearquantity="<?=$gearsM['quantity']?>">
                    <td><?php echo $gear_model->id; ?>
                        <?php if ($gear_model->no_items != 1) { echo Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); } ?>
                    </td>
                    <td><input type="checkbox" class="gear-our" <?php if (count($gears) == 1) { echo "data-id=".$gear_model->id; } ?> data-gearid="<?= $gear_model->id ?>" <?= $checked ?>/></td>
                    <td><?= $scanned[$model_id] ?></td>
                    <td><?= $gearsM['quantity']?></td>
                    <td><?= $gear_model->name; ?><?=$greenRowClass?></td>
                    <td class="number-list-model-<?= $gear_model->id ?>"><?= $added_numbers_list ?></td>
                    <td><?= Yii::t('app', 'Wewnętrzny') ?></td>
                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_model', 'data' => ['gearid' => $gear_model->id]]) ?></td>
                </tr><?php

                if (1 == 1) { ?>
                    <tr style="display: none;" class="sub_models">
                        <td colspan="9">
                            <table class="kv-grid-table table kv-table-wrap" style="width: 70%; margin: auto;">
                                <thead>
                                <td><?= Yii::t('app', 'Id') ?></td>
                                <td><?= Yii::t('app', 'Wydanie') ?></td>
                                <td><?= Yii::t('app', 'Nazwa') ?></td>
                                <td><?= Yii::t('app', 'Numery urządzeń') ?></td>
                                <td></td>
                                </thead><tbody><?php
                                $group_displayed = [];
                                foreach ($gears as $gear) {
                                        $greenRowClass = null;
                                        $checked = null;
                                        if (isset($_COOKIE['checkbox-item-gear'][$gear->id])) {
                                            $checked = 'checked';
                                            $greenRowClass = 'item-in-basket';
                                        }
                                        ?>
                                        <tr class="<?= $greenRowClass ?> gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                        <td><?= $gear->id ?></td>
                                        <td><input type="checkbox" class="gear-item-our" data-id="<?= $gear->id ?>" data-gearid="<?= $gear->gear_id ?>" data-number="<?= $gear->number ?>" <?= $checked ?>/> </td>
                                        <td><?= $gear->name ?></td>
                                        <td><span class="checkbox-item-gear" data-id="<?= $gear->id ?>"><?= $gear->number ?></span></td>
                                        <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_model', 'data' => ['id' => $gear->id]]) ?></td>
                                        <?php
                                } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <?php
                }
            }
            ?>


        </table>
    </div>
    </div>
    </div>
    </div><?php
}

$this->registerJs('

    $("body").on("click", ".row-warehouse-out", function(){
        if ($(this).hasClass("glyphicon-arrow-down")) {
            $(this).parent().parent().next().slideDown();
        }
        else {
            $(this).parent().parent().next().slideUp();
        }
        $(this).toggleClass("glyphicon-arrow-up");
        $(this).toggleClass("glyphicon-arrow-down");
    });
    
    
    $("body").on("click", ".remove_model", function(){
        var thisRow = $(this).parent().parent();
        
        $(".checkbox-model:input[value=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        

        // jeżeli jest tylko jeden egzemplarz tego sprzętu
        thisRow.find(".checkbox-item-gear").each(function(){
            eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
            $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        });
        
        if (thisRow.next().hasClass("sub_models")) {
            thisRow.next().find(".checkbox-item-gear").each(function(){
                eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
                $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            });
            thisRow.next().find(".checkbox-group").each(function(){
                eraseCookie("checkbox-group[" + $(this).data("id") + "]");
                $(".checkbox-group:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            });
            thisRow.next().remove();
        }
        thisRow.remove();
    });
    
    $("body").on("click", ".remove_outer_model", function() {
        $(this).parent().parent().remove();
        eraseCookie("checkbox-item-outer-id[" + $(this).data("id") + "]");
        $(".checkbox-model.checkbox-item-outer-id.item-id-"+$(this).data("id")).prop("checked", false);
    });
    
    $("body").on("click", ".remove_one_model", function(){
        removeRowGearItem($(this).data("id"));
        eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");  
        $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
    });
    
    $("body").on("click", ".remove_one_group", function(){
        var numberTd = $(this).parent().parent().parent().parent().parent().parent().prev().find("td:nth-child(4)");
        var number = parseInt(numberTd.html());
        console.log(number);
        number -= $(this).data("itemno");
        numberTd.html(number);
        $(this).parent().parent().remove();
        eraseCookie("checkbox-group[" + $(this).data("id") + "]");
        $("input.checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        $(".number-list-group-"+$(this).data("id")).remove();
    });


   // ---- Ptaszki w tabelce na górze - lista sprzętu do wydania ---- //
    // ---- Ptaszki w tabelce na górze - lista sprzętu do wydania ---- //

    $("body").on("click", ".gear-our", function(){
        var checked = $(this).is(":checked");
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ($(this).data("id")) {
            if (checked) {
                createCookie("checkbox-item-gear[" + $(this).data("id") + "]", 1, 1);
            }
            else { 
                eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
                $(".number-list-gear-" + $(this).data("id")).remove();
            }
        }

        var submodels = $(this).parent().parent().next();
        if (submodels.hasClass("sub_models")) {
            submodels.find("input:checkbox").each(function(){
                if ($(this).is(":checked") !== checked) {
                    $(this).trigger("click");
                }
            });
        }
    });
    
    $("body").on("click", ".gear-item-our", function(){
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ( $(this).is(":checked")) {
            createCookie("checkbox-item-gear[" + $(this).data("id") + "]", 1, 1);
            $(".checkbox-item-id[value=\'"+$(this).data("id")+"\']").prop("checked", true);
            if ($(".number-list-gear-"+$(this).data("id")).length === 0 ) {
                $(".number-list-model-" + $(this).data("gearid")).append("<span class=\'item-in-basket number-list-gear-"+$(this).data("id")+"\'>"+$(this).data("number")+", </span>");
            }
            else {
                $(".number-list-gear-"+$(this).data("id")).addClass("item-in-basket");
            }
        }
        else {
            eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
            $(".checkbox-item-id[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            $(".number-list-gear-"+$(this).data("id")).removeClass("item-in-basket");
        }
        
        var prev_row = $(this).parent().parent().parent().parent().parent().parent().prev();
        if (!$(this).is(":checked")) {
            prev_row.removeClass("item-in-basket");
            prev_row.find("input").prop("checked", false);
        }
        else {
            var checked_all = true;
            $(this).parent().parent().parent().find("input").each(function(){
                if (!$(this).is(":checked")) {
                    checked_all = false;
                }
            });
            if (checked_all) {
                prev_row.addClass("item-in-basket");
                prev_row.find("input").prop("checked", true);
            }
        }
    });
    
    $("body").on("click", ".gear-group", function(){
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ( $(this).is(":checked")) {
            createCookie("checkbox-group[" + $(this).data("id") + "]", 1, 1);
            $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", true);
            
            if ($(".number-list-group-"+$(this).data("id")).length === 0) {
                $(".number-list-model-" + $(this).data("gearid")).append("<span class=\'item-in-basket number-list-group-"+$(this).data("id")+"\'>["+$(this).data("number")+"], </span>");
            }
            else {
                $(".number-list-group-"+$(this).data("id")).addClass("item-in-basket");
            }
        }
        else {
            eraseCookie("checkbox-group[" + $(this).data("id") + "]");
            $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            $(".number-list-group-"+$(this).data("id")).removeClass("item-in-basket");
        }
        
        var prev_row = $(this).parent().parent().parent().parent().parent().parent().prev();
        if (!$(this).is(":checked")) {
            prev_row.removeClass("item-in-basket");
            prev_row.find("input").prop("checked", false);
        }
        else {
            var checked_all = true;
            $(this).parent().parent().parent().find("input").each(function(){
                if (!$(this).is(":checked")) {
                    checked_all = false;
                }
            });
            if (checked_all) {
                prev_row.addClass("item-in-basket");
                prev_row.find("input").prop("checked", true);
            }
        }
    });
    
    $("body").on("click", ".gear-item-outer", function(){
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ( $(this).is(":checked")) {
            createCookie("checkbox-item-outer-id[" + $(this).data("id") + "]", $(this).parent().next().next().html(), 1);
            $(".checkbox-model.checkbox-item-outer-id.item-id-"+$(this).data("id")).prop("checked", true);
        }
        else {
            eraseCookie("checkbox-item-outer-id[" + $(this).data("id") + "]");
            $(".checkbox-model.checkbox-item-outer-id.item-id-"+$(this).data("id")).prop("checked", false);
        }
    });
    
    $(".gear-our").each(function(){
        var submodels = $(this).parent().parent().next(); 
        var all = $(this).parent().parent().data("gearquantity");
        if (submodels.hasClass("sub_models")) {
            var checked_all = true;
            var selected = 0;
            submodels.find("input").each(function(){
                if (!$(this).is(":checked")) {
                    checked_all = false;
                }else{
                    selected++;
                }
            });
            if (all<=selected) {
                submodels.find("input").attr("checked", true);
                $(this).attr("checked", true);
                $(this).parent().parent().addClass("item-in-basket");
            }
        }
    });
    
');


$this->registerCss('
    .item-in-basket { background-color: #98fb98; }
');

// ---- KONIEC TABELKI Z LISTĄ SPRZĘTU ---- //