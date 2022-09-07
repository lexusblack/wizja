
<?php

use common\models\EventGear;
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\PacklistGear;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\RentGearItem;
use common\models\RentGear;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$gear_item_out = [];
$gear_group_out = [];
$gear_outer_out = [];

?>
<p><strong><?=Yii::t('app', 'Legenda') ?>: </strong>
<span class="label" style="background-color:#98fb98"><?php echo Yii::t('app', 'Sprzęt zeskanowany')?></span>
<span class="label" style="background-color:#f8ac59"><?php echo Yii::t('app', 'Sprzęt spoza rezerwacji')?></span>
<span class="label" style="background-color:#f6f581;"><?php echo Yii::t('app', 'Większa liczba sztuk, niż w rezerwacji')?></span>
</p>
        <?php 
        $sectionList[1] = Yii::t('app', 'Wszystkie');
        $sectionList += \common\models\GearCategory::getMainList();
        ?>
<p><label><?=Yii::t('app', 'Wybierz kategorię')?></label><?= Html::dropDownList(null, Yii::t('app', 'Suma'), $sectionList, ['class' => 'changeSection form-control', 'style'=>' width:200px']) ?></p>

    <?php


// ------- TABELKA z listą sprzętu ------- //
if ($event) {
    $type = 'event';
    $event_id = $event;
    $gear_event_items = EventGearItem::find()->where(['packlist_id' => $packlist_id])->all();
    $gear_event = PacklistGear::find()->where(['packlist_id'=>$packlist_id])->all();
    $gear_models = [];
    $outcomes = \common\models\EventGearOutcomed::find()->where(['packlist_id'=>$packlist_id])->all();
    foreach ($gear_event as $gear)
    {
        $gear_models[$gear->gear_id]['gear'] = $gear->gear;
        $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
        $gear_models[$gear->gear->id]['items'] = [];
        $gear_models[$gear->gear->id]['conflict'] = \common\models\EventConflict::findOne(['packlist_gear_id'=>$gear->id, 'resolved'=>0]);
    }
    foreach ($outcomes as $outcome) {
            $gear_models[$outcome->gear_id]['quantity'] -= $outcome->quantity;
        
        }

    foreach ($gear_models as $model_id => $gm)
    {
        if (($gm['quantity']<=0)&&(!$gm['conflict']))
        {
            unset($gear_models[$model_id]);
        }
    }
    
    ?>

    <div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
        <div class="ibox-content">
        <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">

            <tr class="newsystem-bg">
            <th style="width: 70px;"><?= Yii::t('app', '#') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Wydanie') ?></th>
            <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
            <th><?= Yii::t('app', 'L. zapotrzebowanie') ?></th>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Numery') ?></th>
            <th><?= Yii::t('app', 'Konflikt') ?></th>
            <th></th>

            </tr><?php
            $scanned = [];

            $nr=0;
            foreach ($gear_models as $model_id => $gearsM) {
                $nr++;
                $gears = $gearsM['items'];
                if ($gearsM['conflict'])
                {
                    $t = $gearsM['conflict']->quantity+$gearsM['quantity'];
                    $conflict = "<span class='label label-danger'>Łącznie ".$t."</span>";
                }else{
                    $conflict = "";
                }
                if (!$gears)
                    $gears = [];
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
                    $item_no = GearItem::find()->where(['gear_id'=>$gear_model->id])->andWhere(['active'=>1])->one();
                }
                if ((count($gears)==0)&&(!$noItemsClass))
                {
                    $noItemsClass = 'gear-no-pics-row';
                }
                $cat_class = 'cat-'.$gear_model->getMainCategory()->id;
                ?>
                <tr class="<?= $noItemsClass ?> gear-row <?=$cat_class?>" data-gearid="<?= $gear_model->id ?>" data-gearquantity="<?=$gearsM['quantity']?>">
                    <td><?php echo $nr; ?>
                        <?php if ($gear_model->no_items != 1) { echo Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); } ?>
                    </td>
                    <td><input type="checkbox" class="gear-our" <?php if ($gear_model->no_items != 1) { /*echo 'disabled="disabled"'; */}else{ echo "data-id=".$item_no->id;}?> data-gearid="<?= $gear_model->id ?>"/></td>
                    <td><?= $scanned[$model_id] ?></td>
                    <td><?= $gearsM['quantity']?></td>
                    <td><?= $gear_model->name; ?></td>
                    <td class="number-list-model-<?= $gear_model->id ?>"><?= $added_numbers_list ?></td>
                    <td><?=$conflict?></td>
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
                                $nr2 = 0;
                                foreach ($gears as $gear) {
                                    $nr2++;
                                        $greenRowClass = null;
                                        $checked = null;
                                        ?>
                                        <tr class="<?= $greenRowClass ?> gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                        <td><?= $nr2?></td>
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

// ------- TABELKA z listą sprzętu ------- //
if ($rent) {
    $type = 'rent';
    $event_id = $rent;
    $gear_rent_items = RentGearItem::find()->where(['rent_id' => $rent])->all();
    $outcomes = OutcomesForRent::find()->where(['rent_id' => $rent])->all();
    $gear_rent = RentGear::find()->where(['rent_id'=>$rent])->all();
    $gear_models = [];
    foreach ($gear_rent as $gear)
    {
        $gear_models[$gear->gear_id]['gear'] = $gear->gear;
        $gear_models[$gear->gear->id]['quantity'] = $gear->quantity;
        $gear_models[$gear->gear->id]['items'] = [];
    }

    foreach ($outcomes as $outcome) {
        foreach (OutcomesGearOur::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOur) {
            $gear_item_out[] = $gearOur->gear_id;
            $gi= GearItem::find()->where(['id' => $gearOur->gear_id])->one();
            if (isset($gear_models[$gi->gear_id]))
                $gear_models[$gi->gear_id]['quantity'] -= $gearOur->gear_quantity;
        }
        foreach (OutcomesGearOuter::find()->where(['outcome_id' => $outcome->outcome_id])->all() as $gearOuter) {
            $gear_outer_out[] = $gearOuter->outer_gear_id;
        }
    }

    foreach ($gear_rent_items as $gear_rent_item)
    {
        $gear_item = $gear_rent_item->gearItem;
        $gear_models[$gear_item->gear_id]['items'][] = $gear_item;
    }

    foreach ($gear_models as $gm)
    {
        if ($gm['quantity']<=0)
        {
            unset($gear_models[$gm['gear']->id]);
        }
    }

    ?>

    <div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
        <div class="ibox-content">
        <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">

            <thead>
            <th style="width: 70px;"><?= Yii::t('app', '#') ?></th>
            <th style="width: 100px;"><?= Yii::t('app', 'Wydanie') ?></th>
            <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
            <th><?= Yii::t('app', 'L. zapotrzebowanie') ?></th>
            <th><?= Yii::t('app', 'Nazwa') ?></th>
            <th><?= Yii::t('app', 'Numery') ?></th>
            <th><?= Yii::t('app', 'Magazyn') ?></th>
            <th></th>

            </thead><?php

            $scanned = [];
            $nr = 0;
            foreach ($gear_models as $model_id => $gearsM) {
                
                $nr++;
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
                    $item_no = GearItem::find()->where(['gear_id'=>$gear_model->id])->andWhere(['active'=>1])->one();
                }
                if ((count($gears)==0)&&(!$noItemsClass))
                {
                    $noItemsClass = 'gear-no-pics-row';
                }
                $cat_class = 'cat-'.$gear_model->getMainCategory()->id;
                ?>
                <tr class="<?= $greenRowClass. " " .$noItemsClass ?> gear-row <?=$cat_class?>" data-gearid="<?= $gear_model->id ?>" data-gearquantity="<?=$gearsM['quantity']?>">
                    <td><?php echo $nr; ?>
                        <?php if ($gear_model->no_items != 1) { echo Html::icon('arrow-down', ['class' => 'row-warehouse-out', 'style' => 'cursor: pointer;']); } ?>
                    </td>
                    <td><input type="checkbox" class="gear-our" <?php if ($gear_model->no_items != 1) { /*echo 'disabled="disabled"'; */}else{ echo "data-id=".$item_no->id;}?> data-gearid="<?= $gear_model->id ?>" <?= $checked ?>/></td>
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
                                <td><?= Yii::t('app', 'Nr') ?></td>
                                <td><?= Yii::t('app', 'Wydanie') ?></td>
                                <td><?= Yii::t('app', 'Nazwa') ?></td>
                                <td><?= Yii::t('app', 'Numery urządzeń') ?></td>
                                <td></td>
                                </thead><tbody><?php
                                $group_displayed = [];
                                $nr2 = 0;
                                foreach ($gears as $gear) {
                                    if ($gear->isAvailableForOutcome()){
                                    $nr2++;
                                        $greenRowClass = null;
                                        $checked = null;
                                        ?>
                                        <tr class="<?= $greenRowClass ?> gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                        <td><?= $nr2 ?></td>
                                        <td><input type="checkbox" class="gear-item-our" data-id="<?= $gear->id ?>" data-gearid="<?= $gear->gear_id ?>" data-number="<?= $gear->number ?>" <?= $checked ?>/> </td>
                                        <td><?= $gear->name ?></td>
                                        <td><span class="checkbox-item-gear" data-id="<?= $gear->id ?>"><?= $gear->number ?></span></td>
                                        <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_model', 'data' => ['id' => $gear->id]]) ?></td></tr>
                                        <?php
                                } }?>
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
    </div>
    <?php
    }

$this->registerJs('

    $(".changeSection").change(
        function(e){
            $(".gear-row").show();
            if ($(this).val()==1)
            {

            }else{
                $(".gear-row").hide();
                $(".cat-"+$(this).val()).show();
            }
        });
    var items = [];
    var groups = [];

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
            delete items[$(this).data("id")];
           // eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
            $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        });
        
        if (thisRow.next().hasClass("sub_models")) {
            thisRow.next().find(".checkbox-item-gear").each(function(){
                delete items[$(this).data("id")];
                //eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
                $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            });
            thisRow.next().find(".checkbox-group").each(function(){
                delete groups[$(this).data("id")];
                //eraseCookie("checkbox-group[" + $(this).data("id") + "]");
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
        delete items[$(this).data("id")];
        //eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");  
        $(".checkbox-item-id:input[value=\'"+$(this).data("id")+"\']").prop("checked", false);
    });
    
    $("body").on("click", ".remove_one_group", function(){
        var numberTd = $(this).parent().parent().parent().parent().parent().parent().prev().find("td:nth-child(4)");
        var number = parseInt(numberTd.html());
        console.log(number);
        number -= $(this).data("itemno");
        numberTd.html(number);
        $(this).parent().parent().remove();
        delete groups[$(this).data("id")];
        //eraseCookie("checkbox-group[" + $(this).data("id") + "]");
        $("input.checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        $(".number-list-group-"+$(this).data("id")).remove();
    });


   // ---- Ptaszki w tabelce na górze - lista sprzętu do wydania ---- //
    // ---- Ptaszki w tabelce na górze - lista sprzętu do wydania ---- //

    $("body").on("click", ".gear-our", function(){
        var checked = $(this).is(":checked");

        if ($(this).parent().parent().hasClass("gear-no-items-row"))
        {
                    row = $(this).parent().parent();
                    gear_id = $(this).data("id");
                    if (checked)
                    {
                        swal({
                              text: "Podaj liczbę sztuk "+row.find(":nth-child(5)").html(),
                              content: {
                                element: "input",
                                attributes: {
                                  placeholder: "Podaj wartość",
                                  type: "number",
                                  value:row.find(":nth-child(4)").html()
                                }
                            },
                              button: {
                                text: "OK",
                                closeModal: true,
                              },
                            })
                            .then(name => {
                              if (!name) number = row.find(":nth-child(4)").html();
                              else
                                number = name;
                            $.ajax({
                                type: "POST",
                                url: "' . Url::to(['outcomes-warehouse/check-quantity']) . '?w="+$("#outcomeswarehouse-warehouse_id").val()+"&quantity="+number+"&gear_id="+gear_id,
                                data: $("#dynamic-form").serialize(),
                                async: false,
                                success: function(data){
                                    //tutaj info i odpowiedni modal co zrobić - opcje usuń pozycje i ponów lub anuluj
                                    if (data.success==0)
                                    {
                                        error = true;
                                        var modal = $("#errors_modal");
                                        modal.find(".modalContent").empty().append("<p>W magazynie znajduje się tylko "+data.quantity+" szt. tego sprzętu</p>");
                                        modal.modal("show");
                                        row.find(":nth-child(3)").html(0);
                                         row.removeClass("item-in-basket");

                                    }else{
                                        if (!row.find("input").prop("checked")) {
                                            row.find("input").trigger("click");
                                        }
                                        if (number != null) {
                                            row.find(":nth-child(3)").html(number);
                                        }
                                        else {
                                            var numb = parseInt(row.find(":nth-child(3)").html());
                                            numb++;
                                            row.find(":nth-child(3)").html(numb);
                                        }
                                        var numb = parseInt(row.find(":nth-child(3)").html());
                                        var numb2 = parseInt(row.find(":nth-child(4)").html());
                                        if (numb<numb2)
                                            $(this).parent().parent().toggleClass("item-in-basket");
                                        items[gear_id] = numb;
                                        //createCookie("checkbox-item-gear[" + gear_id + "]", numb, 1);
                                    
                                    }
                                }    
                            });
                            });
                    }else{
                        delete items[gear_id];
                        //eraseCookie("checkbox-item-gear[" + gear_id + "]");
                        row.find(":nth-child(3)").html(0);
                        row.removeClass("item-in-basket");
                    }

        }else{
            if ($(this).parent().parent().hasClass("gear-no-pics-row"))
            {
                if (!checked)
                {
                    var submodels = $(this).parent().parent().next();
                    if (submodels.hasClass("sub_models")) {
                        submodels.find("input:checkbox").each(function(){
                            if ($(this).is(":checked") !== checked) {
                                $(this).trigger("click");
                            }
                        });
                    }
                    $(this).parent().parent().find(":nth-child(3)").html(0);
                    $(this).parent().parent().find(":nth-child(6)").html("");
                    submodels.find(".gear-item-case-row").remove();
                    submodels.find(".gear-item-row").remove();
                }else{
                    gear_id = $(this).data("gearid");
                    var numb2 = parseInt($(this).parent().parent().find(":nth-child(4)").html());
                    showChooseGearModal(gear_id, numb2);
                }
            }else{
                //$(this).parent().parent().toggleClass("item-in-basket");
                var submodels = $(this).parent().parent().next();
                if (submodels.hasClass("sub_models")) {
                    submodels.find("input:checkbox").each(function(){
                        if ($(this).is(":checked") !== checked) {
                            $(this).trigger("click");
                        }
                    });
                }
                row = $(this).parent().parent();
                var numb = parseInt(row.find(":nth-child(3)").html());
                var numb2 = parseInt(row.find(":nth-child(4)").html());
                if (numb>=numb2){
                    row.toggleClass("item-in-basket");
                }else{
                    gear_id = $(this).data("gearid");
                    showChooseGearModal(gear_id, numb2);
                }

            }
           
        }

    });
    
    $("body").on("click", ".gear-item-our", function(){
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ( $(this).is(":checked")) {
            items[$(this).data("id")] = 1;
            //createCookie("checkbox-item-gear[" + $(this).data("id") + "]", 1, 1);
            $(".checkbox-item-id[value=\'"+$(this).data("id")+"\']").prop("checked", true);
            if ($(".number-list-gear-"+$(this).data("id")).length === 0 ) {
                $(".number-list-model-" + $(this).data("gearid")).append("<span class=\'item-in-basket number-list-gear-"+$(this).data("id")+"\'>"+$(this).data("number")+", </span>");
            }
            else {
                $(".number-list-gear-"+$(this).data("id")).addClass("item-in-basket");
            }
        }
        else {
            delete items[$(this).data("id")];
            eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
            $(".checkbox-item-id[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            $(".number-list-gear-"+$(this).data("id")).removeClass("item-in-basket");
        }
        
        var prev_row = $(this).parent().parent().parent().parent().parent().parent().prev();
        if (!$(this).is(":checked")) {
            prev_row.removeClass("item-in-basket");
            prev_row.find("input").prop("checked", false);
            var numb = parseInt(prev_row.find(":nth-child(3)").html());
            numb--;
            prev_row.find(":nth-child(3)").html(numb);
        }
        else {
            var checked_all = true;
            var numb = parseInt(prev_row.find(":nth-child(3)").html());
            numb++;
            prev_row.find(":nth-child(3)").html(numb);
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
        var prev_row = $(this).parent().parent().parent().parent().parent().parent().prev();
        var numb = parseInt(prev_row.find(":nth-child(3)").html());
        if ( $(this).is(":checked")) {
            groups[$(this).data("id")] = 1;
            //createCookie("checkbox-group[" + $(this).data("id") + "]", 1, 1);
            $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", true);
            numb+=$(this).data("quantity");
            prev_row.find(":nth-child(3)").html(numb);
            if ($(".number-list-group-"+$(this).data("id")).length === 0) {
                $(".number-list-model-" + $(this).data("gearid")).append("<span class=\'item-in-basket number-list-group-"+$(this).data("id")+"\'>["+$(this).data("number")+"], </span>");
            }
            else {
                $(".number-list-group-"+$(this).data("id")).addClass("item-in-basket");
            }
        }
        else {
            delete groups[$(this).data("id")];
            //eraseCookie("checkbox-group[" + $(this).data("id") + "]");
            $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
            $(".number-list-group-"+$(this).data("id")).removeClass("item-in-basket");
            numb-=$(this).data("quantity");
            prev_row.find(":nth-child(3)").html(numb);
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
    /*
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
    });*/
');


$this->registerCss('
    .item-in-basket { background-color: #98fb98; }
');
// ---- KONIEC TABELKI Z LISTĄ SPRZĘTU ---- //
?>
<?php
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Wybierz egemplarze')."</h4>",
    'id' => 'outcome_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
$outcomeGearListtUrl = Url::to(['/outcomes-warehouse/get-gear-pics', 'id'=>$event_id, 'type'=>$type]);
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";

?>
<?php
$this->registerJs('
        function showChooseGearModal(gear_id, numb){
        var w = $("#outcomeswarehouse-warehouse_id").val();
        var modal = $("#outcome_modal");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("'.$spinner.'");
        modal.find(".modalContent").load("'.$outcomeGearListtUrl.'&total="+numb+"&gear_id="+gear_id+"&warehouse_id="+w, function( response, status, xhr ){
                modal.modal("show");
                $("#button-gear-outcome-modal").on("click", function(e){ e.preventDefault(); addToOutcomeFromModal();});
                modal.on("click", ".row-warehouse-out-modal", function(){
                    if ($(this).hasClass("glyphicon-arrow-down")) {
                        $(this).parent().parent().next().slideDown();
                    }
                    else {
                        $(this).parent().parent().next().slideUp();
                    }
                    $(this).toggleClass("glyphicon-arrow-up");
                    $(this).toggleClass("glyphicon-arrow-down");
                });
            });          
        
        
        }

        function addToOutcomeFromModal(){
            $groups = $("#outcome_modal").find(".gear-modal-group");
            $groups.each(function(){
                if ($(this).is(":checked"))
                {
                    addGearGroup($(this).data("id"));
                    groups[$(this).data("id")] = 1;
                    //createCookie("checkbox-group[" + $(this).data("id") + "]", 1, 1);
                    $("#outcome_modal").modal("hide");
                }
            });
            $items = $("#outcome_modal").find(".gear-modal-item");
            $items.each(function(){
                if ($(this).is(":checked"))
                {
                    addRowGearItem($(this).data("id"));
                    items[$(this).data("id")]=1;
                    //createCookie("checkbox-item-gear[" + $(this).data("id") + "]", 1, 1);
                    $("#outcome_modal").modal("hide");
                }
            });
        }');

?>