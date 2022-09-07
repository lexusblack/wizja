<?php

use common\models\Gear;
use common\models\EventGear;
use common\models\GearItem;
use common\models\IncomesForRent;
use common\models\IncomesForEvent;
use common\models\IncomesGearOur;
use common\models\IncomesGearOuter;
use common\models\OutcomesForEvent;
use common\models\OutcomesForRent;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OuterGear;
use yii\bootstrap\Html;


$outcomes = [];
$incomes = [];

// wydane i nie zwrócone sprzęty
$gear_our_out = [];
$gear_outer_out = [];
$gear_no_item_our = [];
$gear_out_number = [];
$items = [];

$items = [];
if ($event) {
        $gear_our_outt = \common\models\EventGearOutcomed::find()->where(['event_id'=>$event, 'packlist_id'=>$packlist_id])->andWhere(['>', 'quantity', 0])->all();
    }
if ($rent) {
        $gear_our_outt = \common\models\RentGearOutcomed::find()->where(['rent_id'=>$rent])->andWhere(['>', 'quantity', 0])->all();
    }

foreach ($gear_our_outt as $gear)
{
    $gear_model = Gear::findOne($gear->gear_id);
    if ($gear_model->no_items)
    {
            $gear_no_item_our[$gear->gear_id] = $gear->quantity;

    }else{

    }
}





?>
        <?php 
        $sectionList[1] = Yii::t('app', 'Wszystkie');
        $sectionList += \common\models\GearCategory::getMainList();
        ?>
<p><label><?=Yii::t('app', 'Wybierz kategorię')?></label><?= Html::dropDownList(null, Yii::t('app', 'Suma'), $sectionList, ['class' => 'changeSection form-control', 'style'=>' width:200px']) ?></p>
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
        <div class="ibox-content">
        <table class="kv-grid-table table kv-table-wrap" id="outcomes-table">
            <thead>
                <th style="width: 70px;"><?= Yii::t('app', '#') ?></th>
                <th><?= Yii::t('app', 'Przyjęcie') ?></th>
                <th><?= Yii::t('app', 'L. zeskanowanych') ?></th>
                <th><?= Yii::t('app', 'Liczba') ?></th>
                <th><?= Yii::t('app', 'Nazwa') ?></th>
                <th><?= Yii::t('app', 'Numery') ?></th>
                <th><?= Yii::t('app', 'Magazyn') ?></th>
                <th></th>
            </thead>
            <?php
                $nr = 0;
            foreach ($gear_our_outt as $gear_out) {
                $nr++;
                $gear_model = Gear::findOne($gear_out->gear_id);
                $all_green = true;
                $numbers_list = null;
                if ($event) {
                        $gear_list = GearItem::find()->where(['event_id'=>$event, 'packlist_id'=>$packlist_id, 'gear_id'=>$gear_out->gear_id])->all();
                    }
                if ($rent)
                {
                        $gear_list = GearItem::find()->where(['rent_id'=>$rent, 'gear_id'=>$gear_out->gear_id])->all();
                }
                foreach ($gear_list as $gearItem) {
                        $css_class = '';
                        $numbers_list .= Html::tag("span", $gearItem->number . ", ", ['class' => $css_class.' number-list-gear-'.$gearItem->id]);;
                }
                $case_numbers = [];
                $case_names = [];

                $greenRowClass = null;
                $checked = null;

                $noItemsClass = '';
                if ($gear_model->no_items == 1) {
                    $item_no = GearItem::find()->where(['gear_id'=>$gear_model->id])->andWhere(['active'=>1])->one();
                    $noItemsClass = "gear-no-items-row";
                    $item_quantity = $gear_no_item_our[$gear_model->id];
                    if ($item_quantity>0)
                        $greenRowClass = '';
                    $checked = null;
                }
                $cat_class = 'cat-'.$gear_model->getMainCategory()->id;
                ?>
                <?php if ($greenRowClass!='item-in-basket'){ ?>
                <tr class="<?= $greenRowClass. " " .$noItemsClass ?> gear-row  <?=$cat_class?>" data-gearid="<?= $gear_model->id ?>">
                    <td><?= $nr ?>
                        <?php if ($gear_model->no_items != 1) {echo Html::icon('arrow-down', ['class' => 'row-warehouse-in', 'style' => 'cursor: pointer;']);} ?>
                    </td>
                    <td><input type="checkbox" class="gear-our" <?php if ($gear_model->no_items == 1) { echo "data-id=".$item_no->id;}?> <?= $checked ?>/></td>
                    <td>0</td>
                    <td><?php if ($gear_model->no_items == 1) { echo $item_quantity; } else { echo count($gear_list); } ?></td>
                    <td><?= $gear_model->name; ?></td>
                    <td class="number-list-model-<?= $gear_model->id ?>"><?= $numbers_list ?></td>
                    <td><?= Yii::t('app', 'Wewnętrzny') ?></td>
                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_model', 'data' => ['gearid' => $gear_model->id]]) ?></td>
                </tr>

                <tr style="display: none;" class="sub_models">
                    <td colspan="8">
                        <table class="kv-grid-table table kv-table-wrap" style="width: 70%; margin: auto;">
                            <thead>
                                <th><?= Yii::t('app', '#') ?></th>
                                <th><?= Yii::t('app', 'Wydanie') ?></th>
                                <th></th>
                                <th><?= Yii::t('app', 'Nazwa') ?></th>
                                <th><?= Yii::t('app', 'Numery urządzeń') ?></th>
                                <th></th>
                            </thead><?php
                                $nr2 = 0;
                            foreach ($gear_list as $gear) {
                                $nr2++;
                                    $greenRowClass = null;
                                    ?>
                                    <tr class="<?= $greenRowClass ?> gear-item-row" data-gearitemid="<?= $gear->id ?>">
                                        <td><?= $nr2?></td>
                                        <td><input type="checkbox" class="gear-item-our" data-id="<?= $gear->id ?>" data-gearid="<?= $gear->gear_id ?>" data-number="<?= $gear->number ?>" <?= $checked ?>/> </td>
                                        <td><?php
                                            if ($gear != null) {
                                                echo Html::img($gear->getPhotoUrl(), ['width' => '100px']);
                                            } ?> </td>
                                        <td><?= $gear->name ?></td>
                                        <td><span class="checkbox-item-gear" data-id="<?= $gear->id ?>"><?= $gear->number ?></span></td>
                                        <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_one_model', 'data' => ['id' => $gear->id]]) ?></td>
                                    </tr><?php
                            }

                            ?>

                        </table>
                    </td>
                </tr>
                <?php } ?>
                            <?php



            } // koniec sprzętów z naszego magazynu

            foreach ($gear_outer_out as $gear_id => $gear_quantity) {
                $gear = OuterGear::findOne($gear_id);
                $greenRowClass = null;
                $checked = null;

                ?>


                <tr class="<?= $greenRowClass ?> gear-item-outer-row" data-itemouterid="<?= $gear->id ?>">
                    <td><?= $gear->id ?></td>
                    <td><input type="checkbox" class="gear-item-outer" data-id="<?= $gear->id ?>" <?= $checked ?> /> </td>
                    <td><?php
                        if ($gear->photo != null) {
                            echo Html::img($gear->getPhotoUrl(), ['width' => '100px']);
                        } ?></td>
                    <td><?= $gear_quantity ?></td>
                    <td><?= $gear->name . "<br>".Yii::t('app', 'Numer').": " . $gear->getBarCodeValue(); ?></td>
                    <td></td>
                    <td><?= Yii::t('app', 'Zewnętrzny') ?></td>
                    <td><?= Html::icon('remove', ['style' => 'cursor:pointer;', 'class' => 'remove_outer_model', 'data' => ['id' => $gear->id]]) ?></td>
                </tr>


                <?php
            }

            ?>

        </table>

    </div>
</div>
</div>
</div>


<?php


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

    $("body").on("click", ".row-warehouse-in", function(){
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
        var this_row = $(this).parent().parent();
        //eraseCookie("checkbox-group[" + $(this).data("id") + "]");
        delete groups[$(this).data("id")];
        $("input.checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", false);
        $(".number-list-group-"+$(this).data("id")).remove();
        
        var gearRow = this_row.parent().parent().parent().parent().prev();
        var itemNo = parseInt(gearRow.find("td:nth-child(4)").html());
        itemNo -= parseInt($(this).data("item-number"));
        this_row.remove();
        gearRow.find("td:nth-child(4)").html(itemNo);
        if (itemNo <= 0) {
            gearRow.next().remove();
            gearRow.remove();
        }
    });

    // ---- Ptaszki w tabelce na górze - lista sprzętu do przyjęcia ---- //
    // ---- Ptaszki w tabelce na górze - lista sprzętu do przyjęcia ---- //

    $("body").on("click", ".gear-our", function(){
        var checked = $(this).is(":checked");
        
        if ($(this).parent().parent().hasClass("gear-no-items-row"))
            {
                        row = $(this).parent().parent();
                        gear_id = $(this).data("id");
                        if ( $(this).is(":checked")) {
                            swal({
                              text: "Podaj liczbę sztuk",
                              content: {
                                element: "input",
                                attributes: {
                                  placeholder: "Podaj wartość",
                                  type: "number",
                                  value: row.find(":nth-child(4)").first().html()
                                }
                            },
                              button: {
                                text: "OK",
                                closeModal: true,
                              },
                            })
                            .then(name => {
                              if (!name) name =row.find(":nth-child(4)").first().html();
                                number = name;
                                if (parseInt(number)>parseInt(row.find(":nth-child(4)").first().html()))
                                    number = row.find(":nth-child(4)").first().html();
                                if (number != null) {
                                    row.find(":nth-child(3)").html(number);
                                }
                                else {
                                    var numb = parseInt(row.find(":nth-child(3)").first().html());
                                    numb++;
                                    row.find(":nth-child(3)").first().html(numb);
                                }
                                //createCookie("checkbox-item-gear[" + $(this).data("id") + "]", number, 1);
                                items[$(this).data("id")] = number;
                                var numb = parseInt(row.find(":nth-child(3)").first().html());
                                var numb2 = parseInt(row.find(":nth-child(4)").first().html());
                                if (numb>=numb2){
                                    $(this).parent().parent().toggleClass("item-in-basket");
                                }
                            });
                        }else{
                            $(this).parent().parent().removeClass("item-in-basket");
                            //eraseCookie("checkbox-item-gear[" + $(this).data("id") + "]");
                            delete items[$(this).data("id")];
                        }
                
            }else{
                $(this).parent().parent().toggleClass("item-in-basket");
                var number = 0;
                 var submodels = $(this).parent().parent().next();
                if (submodels.hasClass("sub_models")) {
                    submodels.find("input:checkbox").each(function(){
                        number++;
                        if ($(this).is(":checked") !== checked) {
                            $(this).trigger("click");
                        }
                    });
                }
                $(this).parent().parent().find(":nth-child(3)").first().html(number);             
            }

    });
    
    $("body").on("click", ".gear-item-our", function(){
        pr = $(this).parent().parent();
        $(this).parent().parent().toggleClass("item-in-basket");
        
        if ( $(this).is(":checked")) {

            items[$(this).data("id")] = 1;
            if ($(".number-list-gear-"+$(this).data("id")).length === 0 ) {
                $(".number-list-model-" + $(this).data("gearid")).append("<span class=\'item-in-basket number-list-gear-"+$(this).data("id")+"\'>"+$(this).data("number")+", </span>");
            }
            else {
                $(".number-list-gear-"+$(this).data("id")).addClass("item-in-basket");
            }
        }
        else {
            delete items[$(this).data("id")];
            $(".number-list-gear-"+$(this).data("id")).removeClass("item-in-basket");
        }
        
        var prev_row = pr.parent().parent().parent().parent().prev();
        if (!$(this).is(":checked")) {
            prev_row.removeClass("item-in-basket");
            prev_row.find("input").prop("checked", false);
            var numb = parseInt(prev_row.find(":nth-child(3)").first().html());
            numb--;
            prev_row.find(":nth-child(3)").html(numb);
        }
        else {
            var checked_all = true;
            var numb = parseInt(prev_row.find(":nth-child(3)").first().html());
            numb++;
            prev_row.find(":nth-child(3)").first().html(numb);
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
            numb+=$(this).data("quantity");
            prev_row.find(":nth-child(3)").html(numb);
            $(".checkbox-group[value=\'"+$(this).data("id")+"\']").prop("checked", true);
            
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


    $(".gear-our").each(function(){
        if (!$(this).parent().parent().hasClass("gear-no-items-row"))
        {
        var submodels = $(this).parent().parent().next(); 
        if (submodels.hasClass("sub_models")) {
            var checked_all = true;
            submodels.find("input").each(function(){
                if (!$(this).is(":checked")) {
                    checked_all = false;
                }
            });
            if (checked_all) {
                submodels.find("input").attr("checked", true);
                $(this).attr("checked", true);
                $(this).parent().parent().addClass("item-in-basket");
            }
        }
        }
    });

');


$this->registerCss('
    .item-in-basket { background-color: #98fb98; }
');