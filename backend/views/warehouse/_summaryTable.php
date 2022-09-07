<?php
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearGroup;
use common\models\RentGearItem;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

    <div class="row">
        <div class="col-md-12">
        <div class="ibox-title black-bg">
            <h5><?= Yii::t('app', 'Dodany sprzęt') ?></h5>
        </div>
        <div class="ibox-content">
    <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">

        <thead>
            <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
            <th><?= Yii::t('app', 'Zdjęcie') ?></th>
            <th><?= Yii::t('app', 'Liczba') ?></th>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Numery') ?></th>
            <th><?= Yii::t('app', 'Magazyn') ?></th>
            <th></th>
        </thead><?php

        $gear_models = [];
        $gear_ilosc_sztuk_number = [];

        if ($type == 'rent') {
            $gear_event_items = RentGearItem::find()->where(['rent_id' => $event])->all();
        }
        else {
            $gear_event_items = EventGearItem::find()->where(['event_id' => $event])->all();
        }

        foreach ($gear_event_items as $gear_event_item) {
            $gear_item = $gear_event_item->gearItem;
            $gear_models[$gear_item->gear_id][] = $gear_item;
            if ($gear_item->name == '_ILOSC_SZTUK_') {
                if ($gear_event_item->quantity == null) {
                    $gear_event_item->quantity = 1;
                }
                $gear_ilosc_sztuk_number[$gear_item->gear_id] = $gear_event_item->quantity;
            }
        }

        $numbers = [];
        foreach ($gear_models as $model_id => $gears) {
            $numbers[$model_id] = '';
            $case = [];
            foreach ($gears as $gear_item) {
                if ($gear_item->group_id == null) {
                    $numbers[$model_id] .= "<span class='numbers-item-gear' data-id='".$gear_item->id."'>" . $gear_item->number . ", </span>";
                }
                else {
                    $case[$gear_item->group_id][] = $gear_item->number;
                }
            }
            foreach ($case as $group_id => $numbers_in_case) {
                $numbers[$model_id] .= "<span class='numbers-item-group' data-id='".$group_id."'>[";

                $numer_list = null;
                $ids = [];
                foreach ($numbers_in_case as $number) {
                    $numer_list .= $number . ', ';
                    $ids[] = $number;
                }
                $in_order = true;
                for ($i = min($ids); $i < max($ids); $i++) {
                    if (!in_array($i, $ids)) {
                        $in_order = false;
                    }
                }
                if ($in_order) {
                    $numer_list = min($ids) . "-" . max($ids);
                }

                $numbers[$model_id] .= $numer_list. '], </span>';
            }
        }

        foreach ($gear_models as $model_id => $gears) {
            $gear_model = Gear::find()->where(['id' => $model_id])->one(); ?>
        <tr class="gear-row" data-gearid="<?= $model_id ?>">
            <td><?= $gear_model->id ?>
                <?= Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); ?>
            </td>
            <td><?php
                if ($gear_model->photo != null) {
                    echo Html::img($gear_model->getPhotoUrl(), ['width' => '100px']);
                }  ?></td>
            <td><?php if ($gears[0]->name == '_ILOSC_SZTUK_') { echo $gear_ilosc_sztuk_number[$gear_model->id]; } else { echo count($gears); } ?></td>
            <td><?= $gear_model->name; ?></td>
            <td><?= $numbers[$model_id] ?></td>
            <td><?= Yii::t('app', 'Wewnętrzny') ?></td>
            <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_model', 'data' => ['gearid' => $gear_model->id]]) ?></td>
            </tr><?php

            if (count($gears) > 0) { ?>
                <tr style="display: none;" class="sub_models">
                    <td colspan="5">
                        <table class="kv-grid-table table kv-table-wrap" style="width: 70%; margin: auto;">
                            <thead>
                                <td><?=  Yii::t('app', 'Id') ?></td>
                                <td></td>
                                <td><?= Yii::t('app', 'Nazwa') ?></td>
                                <td><?= Yii::t('app', 'Numery urządzeń') ?></td>
                                <td></td>
                            </thead><?php
                            $group_displayed = [];
                            foreach ($gears as $gear) {
                                if ($gear->group_id == null) { ?>
                                    <tr class="gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                    <td><?= $gear->id ?></td>
                                    <td><?php
                                        if ($gear != null) {
                                            echo Html::img($gear->getPhotoUrl(), ['width' => '100px']);
                                        } ?> </td>
                                    <td><?= $gear->name ?></td>
                                    <td><span class="checkbox-item-gear" data-id="<?= $gear->id ?>"><?= $gear->number ?></span></td>
                                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_model', 'data' => ['id' => $gear->id, 'gearid' => $gear->gear_id]]) ?></td>
                                    <?php
                                }
                                else if (!in_array($gear->group_id, $group_displayed)) {
                                    $group_displayed[] = $gear->group_id;
                                    $group = GearGroup::find()->where(['id' => $gear->group_id])->one(); ?>
                                <tr class="checkbox-group gear-item-case-row" data-id="<?= $group->id ?>" data-groupid="<?= $group->id ?>">
                                    <td><?= $group->id ?></td>
                                    <td><?= Html::img('@web/../img/case.jpg', ['style'=>'width:100px;']) ?></td>
                                    <td><?php foreach ($group->gearItems as $gear_model) { echo $gear_model->name."<br>"; } ?></td>
                                    <td><?php
                                        foreach ($group->gearItems as $gear_model) {
                                            echo Yii::t('app', "numer").": " . $gear_model->number . "<br>";
                                        }
                                        ?></td>
                                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_group', 'data' => ['id' => $group->id, 'gearid' => $gear->gear_id, 'number' => count($group->gearItems)]]) ?></td>
                                    </tr><?php
                                }
                            } ?>
                        </table>
                    </td>
                </tr>

                <?php
            }
        }

        $gear_outer_items = [];
        if ($type == 'event') {
            $gear_outer_items = EventOuterGear::find()->where(['event_id' => $event])->all();
        }
        foreach ($gear_outer_items as $gear) {
            if ($gear->quantity == null) { ?>
            <tr class="gear-item-outer-row" data-itemouterid="<?= $gear->outerGear->id ?>">
                <td><?= $gear->outerGear->id ?></td>
                <td><?php
                    if ($gear->outerGear->photo != null) {
                        echo Html::img($gear->outerGear->getPhotoUrl(), ['width' => '100px']);
                    } ?></td>
                <td>1</td>
                <td><?= $gear->outerGear->name ?></td>
                <td></td>
                <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->outerGear->id]]) ?></td>
                </tr><?php
            }
            else { ?>
            <tr class="gear-item-outer-row" data-itemouterid="<?= $gear->outerGear->id ?>">
                <td><?= $gear->outerGear->id ?></td>
                <td><?php
                    if ($gear->outerGear->photo != null) {
                        echo Html::img($gear->outerGear->getPhotoUrl(), ['width' => '100px']);
                    } ?></td>
                <td><?= $gear->quantity ?></td>
                <td><?= $gear->outerGear->name;
                    echo "<br>".Yii::t('app', 'Numer').": " . $gear->outerGear->getBarCodeValue(); ?></td>
                <td></td>
                <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->outerGear->id]]) ?></td>
                </tr><?php

            }
        }
        ?>

    </table>
</div>
</div>
</div><?php

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
    
    $("body").on("click", ".remove_one_model", function(){
        var post_data = {
            add: 0,
            itemId: $(this).data("id"),
        };
        $.ajax({
            type: "POST",
            url: "'.Url::to(['warehouse/assign-gear']).'?id='.$event.'&type='.$_GET['type'].'",
            data: post_data, 
        });
    
        var number = $(this).parent().parent().parent().parent().parent().parent().prev().find("td:nth-child(3)");
        var new_value = parseInt(number.html())-1;
        if (new_value == 0) {
            number.parent().next().remove();
            number.parent().remove();
            $(".checkbox-group[data-gearid=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        }
        else {
            number.html(new_value);
            $(this).parent().parent().remove();
        }
        $(".checkbox-model[value=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        $(".checkbox-item[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        $("span.numbers-item-gear[data-id=\'"+$(this).data("id")+"\']").remove();
    });
    
    $("body").on("click", ".remove_model", function(){
        var post_data = {
            add: 0,
            itemId: $(this).data("gearid"),
        };
        $.ajax({
            type: "POST",
            url: "'.Url::to(['warehouse/assign-gear']).'?id='.$event.'&type='.$_GET['type'].'&model=1",
            data: post_data, 
        });
        $(this).parent().parent().next().remove();
        $(this).parent().parent().remove();
        $(".checkbox-model[value=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        $(".checkbox-group[data-gearid=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        $(".checkbox-item[data-gearid=\'"+$(this).data("gearid")+"\']").prop("checked", false);
    });
    
    $("body").on("click", ".remove_one_group", function(){
        var post_data = {
            add: 0,
            itemId: $(this).data("id"),
        };
        $.ajax({
            type: "POST",
            url: "'.Url::to(['warehouse/assign-gear']).'?id='.$event.'&type='.$_GET['type'].'&group=1",
            data: post_data, 
        });
    
        var number = $(this).parent().parent().parent().parent().parent().parent().prev().find("td:nth-child(3)");
        var new_value = parseInt(number.html()) - parseInt($(this).data("number"));
        if (new_value <= 0) {
            number.parent().next().remove();
            number.parent().remove();
            $(".checkbox-item[data-gearid=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        }
        else {
            number.html(new_value);
            $(this).parent().parent().remove();
        }
        $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        $(".checkbox-model[value=\'"+$(this).data("gearid")+"\']").prop("checked", false);
        $("span.numbers-item-group[data-id=\'"+$(this).data("id")+"\']").remove();
    });
    
    $("body").on("click", ".remove_outer_model", function(){
        var post_data = {
            add: 0,
            itemId: $(this).data("id"),
            quantity: 0,
        };
        $.ajax({
            type: "POST",
            url: "'.Url::to(['outer-warehouse/assign-outer-gear']).'?id='.$event.'&type='.$_GET['type'].'",
            data: post_data, 
        });
        $(this).parent().parent().remove();
        $("input.quantity-input.item-input-id-"+$(this).data("id")).val("");
        $("input.checkbox-model.item-checkbox-id-"+$(this).data("id")).prop("checked", false);
    });
    
    
');