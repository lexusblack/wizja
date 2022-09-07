s<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php

if (isset($_GET['activeModel']) && is_numeric($_GET['activeModel'])) {
    $this->registerJs('
        $("html, body").animate({
            scrollTop: $("#row-'.$_GET['activeModel'].'").offset().top - 54
        }, 2000);
    ');
}

$this->registerJs('
var currentEl;

$(".checkbox-item-id").change(function(){
    if ($(this).is(":checked")) {
        addRowGearItem($(this).val());
    }
    else {
        removeRowGearItem($(this).val());
    }
});

$(".checkbox-group").change(function(){
    if ($(this).is(":checked")) {
        addGearGroup($(this).val());
    }
    else {
        removeGearGroup($(this).val());
    }
});
$(".header-checkbox").find("input").change(function(){

    $(".checkbox-item-id").each(function(){
        if ($(this).is(":checked")) {
            addRowGearItem($(this).val());
        }
        else {
            removeRowGearItem($(this).val());
        }
    });

});









// cały gear, nie pojedynczy model
$(":checkbox.checkbox-model").on("change", function(e){
    e.preventDefault();
    var add = $(this).prop("checked");
    
    var tr = $(this).closest("tr").next("tr");
    if (tr.hasClass("gear-details")) {
        tr.find(":checkbox").prop("checked", add);
    }
    
    $.ajax({
      type: "POST",
      url: "'.Url::to(['incomes-warehouse/get-gear-list']).'?model_id=" + $(this).val(),
      success: function(resp) {
        var gear_items = resp[0];
        var groups = resp[1];
        
        for (var i = 0; i < gear_items.length; i++) {
            if (add) {
                addRowGearItem(gear_items[i]);
            }
            else {
                removeRowGearItem(gear_items[i]);
            }
        }
        for (var i = 0; i < groups.length; i++) {
             if (add) {
                addGearGroup(groups[i]);
            }
            else {
                removeGearGroup(groups[i]);
            }
        }
        
      }
    });
    
    return false;
});











// -------


function addGearRow(gear, item_id) {
    if ($(".gear-row[data-gearid=\'"+gear.id+"\']").length === 1) {
        return;
    }
    var img;
    if (gear.photo) {
        img = "<img src=\'/uploads/gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
    }

    scanned = 0;  

    var new_row =   "<tr class=\'gear-row item-in-basket\' data-gearid=\'"+gear.id+"\' style=\'cursor:pointer;\' >" +
                        "<td>"+gear.id+"<span class=\'row-warehouse-in glyphicon glyphicon-arrow-down\'></span></td>" +
                        "<td><input class=\'gear-our\' data-gearid=\'"+gear.id+"\' type=\'checkbox\' checked></td>"+
                        "<td>"+scanned+"</td>"+
                        "<td>"+scanned+"</td>"+
                        "<td>"+gear.name+"</td>"+
                        "<td class=\'number-list-model-"+gear.id+"\'></td>"+
                        "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                        "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear.id+"\'></span></td>"+
                    "</tr>";

    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });
    
}
function gearItemRow(gear) {
    return "<tr class=\'gear-item-row item-in-basket\' data-gearitemid=\'"+gear.id+"\'>"+
                "<td>"+gear.id+"</td>"+
                "<td><input class=\'gear-item-our\' data-id=\'"+gear.id+"\' data-gearid=\'"+gear.gear_id+"\' data-number=\'"+gear.number+"\' type=\'checkbox\' checked></td>"+
                "<td></td>"+
                "<td>"+gear.name+"</td>"+
                "<td><span class=\'checkbox-item-gear\' data-id=\'"+gear.id+"\'>"+gear.number+"</span></td>"+
                "<td><span class=\'remove_one_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+gear.id+"\'></span></td>"+
            "</tr>";
}
function addGearItem(gear) {
    var modelRow = $(".gear-row[data-gearid=\'"+gear.gear_id+"\']");
    var itemRow = $(".gear-item-row[data-gearitemid=\'"+gear.id+"\']");

    if (modelRow.length === 1) {
        if (gear.name == "_ILOSC_SZTUK_") {
            addGearNoItems(gear.id, null);
            return;
        }
        if (modelRow.next().hasClass("sub_models")) {
            if (itemRow.length === 1) {
                c = itemRow.find("input:checkbox");
                if (c.is(":checked"))
                {

                }else{
                    itemRow.find("input:checkbox").trigger("click");
                }
                
            }
            else {
                modelRow.next().find("tbody").append(gearItemRow(gear));
                var numberTd =  modelRow.find("td:nth-child(3)");
                numberTd.html((parseInt(numberTd.html())+1));
            }
        }
        else {
            modelRow.after(
                "<tr class=\'sub_models\' style=\'display: none;\'>"+
                    "<td colspan=\'8\'>"+
                        "<table class=\'kv-grid-table table kv-table-wrap\' style=\'width: 70%; margin: auto;\'>"+
                            "<thead><tr><td>Id</td><td>'.Yii::t('app', 'Wydanie').'</td><td></td><td>'.Yii::t('app', 'Nazwa').'</td><td>'.Yii::t('app', 'Numery urządzeń').'</td><td></td></tr></thead>"+
                            "<tbody>"+gearItemRow(gear)+"</tbody>"+
                    "</td>"+
                "</tr>"
            );
            var numberTd =  modelRow.find("td:nth-child(3)");
                numberTd.html((parseInt(numberTd.html())+1));
        }
        
        if ($(".number-list-gear-"+gear.id).length === 0 ) {
            $(".number-list-model-" + gear.gear_id).append("<span class=\'item-in-basket number-list-gear-"+gear.id+"\'>"+gear.number+", </span>");
        }
        else {
            $(".number-list-gear-"+gear.id).addClass("item-in-basket");
        }
    }
    else {
        alert("'.Yii::t('app', 'Błąd nr').': #000158qad666");
    }
}

function addGearNoItems(gear_id, number) { 
    $.ajax({
        url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gear_id,
        async: false,
        success: function(resp) {
            var gearModel = resp;
            var row = $(".gear-no-items-row[data-gearid=\'"+gearModel.id+"\']"); 
            if (number!=null)
                addGearNoItemsRow(gearModel, gear_id, row, number);
            else{
                        swal({
                          text: "Podaj liczbę sztuk "+gearModel.name,
                          content: {
                            element: "input",
                            attributes: {
                              placeholder: "Podaj wartość",
                              type: "number",
                              value:1
                            }
                        },
                          button: {
                            text: "OK",
                            closeModal: true,
                          },
                        })
                        .then(name => {
                          if (!name) name=1;
                            number = name;
                            addGearNoItemsRow(gearModel, gear_id, row, number);
                        });
            }
            
        }
    });
 
}

function addGearNoItemsRow(gearModel, gear_id, row, number)
{
            if (number >0) {
            }
            else {
                if (row.length === 0) {
                    var numb = 0;
                }else{
                    var numb = parseInt(row.find(":nth-child(3)").html());
                }
                numb++;
                number = numb;
            }
           items[gear_id] = number;

            var checkbox = $(".itemnoitems[value=\'"+gear_id+"\']");
            if (!checkbox.prop("checked")) {
                checkbox.prop("checked", true);
            }
        
        
            if (row.length === 0) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gear_id,
                    success: function(gear) {
                        var new_row =   "<tr class=\'gear-no-items-row additional-items item-in-basket\' data-gearid=\'"+gear.id+"\' style=\'cursor:pointer;\' >" +
                                            "<td>"+gear.id+"</td>" +
                                            "<td><input class=\'gear-our\' data-gearid=\'"+gear_id+"\' type=\'checkbox\' checked></td>"+
                                            "<td class=\'gear-item-no-items-number\' data-gearid=\'"+gear_id+"\'>"+number+"</td>"+
                                            "<td>0</td>"+
                                            "<td>"+gear.name+"</td>"+
                                            "<td class=\'number-list-model-"+gear_id+"\'></td>"+
                                            "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                                            "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear_id+"\'></span></td>"+
                                        "</tr>";
                    
                        if ($("#outcomes-table tbody").length === 0) {
                            $("#outcomes-table").append("<tbody></tbody>");
                        }
                        $("#outcomes-table tbody").each(function(index){
                            if (index === 0) {
                                $(this).append(new_row);
                            }
                        });  
                    }
                });
            
            }
            else {
                if (!row.find("input").prop("checked")) {
                    //row.find("input").trigger("click");
                    row.find("input").prop("checked", true);
                }
                if (number != null) {
                    row.find(":nth-child(3)").html(number);
                }
                else {
                    //var numb = parseInt(row.find(":nth-child(3)").html());
                    //numb++;
                    row.find(":nth-child(3)").html(number);
                }
                scannedTd = parseInt(row.find(":nth-child(4)").html());
                if (number>=scannedTd)
                        row.addClass("item-in-basket");
                if (scannedTd<number)
                    row.addClass("more-items");
                } 
}

function addRowGearItem(id) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-item']).'?gear_id=" + id,
        success: function(gearItem) {
            if (gearItem.no_items==1)
            {
                            $.ajax({
                                url: "'.Url::to(['outcomes-warehouse/get-gear-no-items']).'?gear_id=" + gearItem.id,
                                async: false,
                                success: function(resp) {
                                    var gearModel = resp;
                                    var row = $(".gear-no-items-row[data-gearid=\'"+gearModel.id+"\']"); 
                                    addGearNoItemsRow(gearModel, gearItem.id, row, 0);
                                    
                                    
                                }
                            });
            }else{
             // kiedy nie ma rowa z gearem
            items[gearItem.id] = 1;
            if( $(".gear-row").find("[data-gearid=\'"+gearItem.gear_id+"\']").length == 0 ) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "'.Url::to(['outcomes-warehouse/get-gear']).'?gear_id=" + gearItem.gear_id,
                    success: function(gear) {
                            addGearRow(gear);
                            addGearItem(gearItem); 
                    }
                });
            }else{
                            addGearItem(gearItem);
                }
            }


        }
    });
}
function removeRowGearItem(id) {
    $("span.number-list-gear-"+id).remove();
    var itemRow = $(".gear-item-row[data-gearitemid=\'"+id+"\']");
    var gearRow = itemRow.parent().parent().parent().parent().prev();
    var itemNo = parseInt(gearRow.find("td:nth-child(4)").html());
    $(".number-list-gear-"+id).removeClass("item-in-basket");
    itemNo--;
    gearRow.find("td:nth-child(4)").html(itemNo);
    itemRow.remove();
    if (itemNo === 0) {
        gearRow.next().remove();
        gearRow.remove();
    }
}
function addGearGroup(id) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-group']).'?gear_id=" + id,
        success: function(gearGroup) {
            
            // jeżeli jest tylko jednego rodzaju sprzęt w case to dodajemy go w podkategorii tego sprzętu
            if (gearGroup.gear_ids.length === 1) {
                // kiedy nie ma rowa z gearem
                if( $(".gear-row").find("[data-gearid=\'"+gearGroup.gear_ids[0]+"\']").length == 0 ) {
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: "'.Url::to(['outcomes-warehouse/get-gear']).'?gear_id=" +gearGroup.gear_ids[0],
                        success: function(gear) {
                            addGearRow(gear, null);
                            addGroup(gearGroup);
                        }
                    });
                }
                // kiedy jest row z gearem
                else {
                    addGroup(gearGroup);
                }
            }
            // inaczej case wyświetlamy samotnie
            else {
                addAloneCase(gearGroup);
            }
        }
    });
}
function addAloneCase(group) {
    var itemNames = "";
    for (var i = 0; i < group.items.length; i++) {
        itemNames +=  group.items[i].name + " number: " + group.items[i].number + "<br>";
    }
    var new_row =   "<tr class=\'checkbox-group  gear-item-case-row item-in-basket\' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\' >" +
                        "<td>"+group.id+"</td>" +
                        "<td><input class=\'gear-group\' data-id=\'"+group.id+"\' type=\'checkbox\' checked></td>"+
                        "<td><img src=\'/admin/../img/case.jpg\' alt=\'\' style=\'width: 100px;\' ></td>"+
                        "<td>1</td>"+
                        "<td>"+itemNames+"</td>"+
                        "<td>'.Yii::t('app', 'Wewnętrzny').'</td>"+
                        "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\'></span></td>"+
                    "</tr>";

    if ($("#outcomes-table tbody").length === 0) {
        $("#outcomes-table").append("<tbody></tbody>");
    }
    $("#outcomes-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });
}
function addGroup(group) {
    for (var i = 0; i < group.items.length; i++) {                        
        addRowGearItem(group.items[i].id);
    }
}

function gearGroupRow(group) {
    var numbers;
    var numer_list = null;
    var ids = [];
    for (var i = 0; i < group.items.length; i++) {                        
        numbers += group.items[i].number + ", ";
        ids[i] = group.items[i].number;
    }
    numbers += "], ";
    var in_order = true;
    for (var i = Math.min.apply(null, ids); i < Math.max.apply(null, ids); i++) {
        if ($.inArray(i, ids) === -1) {
            in_order = false;
        }
    }
    if (in_order) {
        numbers = "[" + Math.min.apply(null, ids) + " - " + Math.max.apply(null, ids) + "]";
    }
    return "<tr class=\'checkbox-group  gear-item-case-row item-in-basket\' data-id=\'"+group.id+"\' data-groupid=\'"+group.id+"\'>"+
                "<td>"+group.id+"</td>"+
                "<td><input class=\'gear-group\' data-id=\'"+group.id+"\' data-gearid=\'+group.items[0].gear_id+\' data-numbers=\'"+numbers+"\' type=\'checkbox\' checked></td>"+
                "<td><img src=\'/admin/../img/case.jpg\' alt=\'\' style=\'width:100px;\' ></td>"+
                "<td>"+group.items[0].name+"</td>"+
                "<td>"+numbers+"</td>"+
                "<td><span class=\'remove_one_group glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+group.id+"\'></span></td>"+
            "</tr>";
}

function removeGearGroup(id) {
    $("body").find(".checkbox-group.gear-item-case-row[data-groupid=\'"+id+"\']").remove();
    $(".number-list-group-"+id).removeClass("item-in-basket");
}

// ************************

var new_page = true;
$(".category-menu-link").click(function(){
    new_page = false;
});
function confirmExit() {
    if (new_page == true) {
        return "'.Yii::t('app', 'opuścić stronę?').'";
    }
    else {
        new_page = true;
    }
}

window.onbeforeunload = function() { return confirmExit(); }
window.onunload = function() { return clearSession(); }


$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});

function addOuterGearItem(id, number) {
    var gear_row = $(".gear-item-outer-row[data-itemouterid=\'"+id+"\']");
    if ( gear_row.length == 1) {
        gear_row.addClass("item-in-basket");
        gear_row.find("input:checkbox").prop("checked", true);
        var item_no = parseInt(gear_row.find("td:nth-child(4)").html());
        item_no += number;
        gear_row.find("td:nth-child(4)").html(item_no);
    }
    else {
        createRowOuterGear(id, number);
    }
}

function createRowOuterGear(id, number) {
    $.ajax({
        type: "POST",
        async: false,
        url: "'.Url::to(['outcomes-warehouse/get-gear-item-outer']).'?gear_id=" + id,
        success: function(gear) {
        
            var img;
            if (gear.photo) {
                img = "<img src=\'/uploads/outer-gear/"+gear.photo+"\' alt=\'\' width=\'100px\' >";
            }
            var new_row = 
                "<tr class=\'gear-item-outer-row item-in-basket\' data-itemouterid=\'"+id+"\'>"+
                    "<td>"+id+"</td>"+
                    "<td><input class=\'gear-item-outer\' data-id=\'"+id+"\' type=\'checkbox\' checked></td>"+
                    "<td>"+img+"</td>"+
                    "<td>"+number+"</td>"+
                    "<td>0</td>"+
                    "<td>"+gear.name+"<br>"+gear.qrcode+"</td>"+
                    "<td></td>"+
                    "<td>'.Yii::t('app', 'Zewnętrzny').'</td>"+
                    "<td><span class=\'remove_outer_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-id=\'"+id+"\'></span></td>"+
                "</tr>";
            
            if ($("#outcomes-table tbody").length === 0) {
                $("#outcomes-table").append("<tbody></tbody>");
            }
            $("#outcomes-table tbody").each(function(index){
                if (index === 0) {
                    $(this).append(new_row);
                }
            });
        }
    });
}

')

?>

