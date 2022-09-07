<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */

$from_date = $offer->getTimeStart();
$to_date = $offer->getTimeEnd();
use yii\bootstrap\Modal;
$user = Yii::$app->user;

Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Sprzęt powiązany')."</h4>",
    'id' => 'connected_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
    <p><?=Html::a(Yii::t('app', 'Zamknij okno i odśwież ofertę'), ['/offer/default/view', 'id'=>$offer->id], ['class'=>'btn btn-primary pull-right'])?></p>
    <h1><?=Yii::t('app', 'Magazyn wewnętrzny')?></h1>
<table class="table">
    <tr><th><?=Yii::t('app', 'SZT')?></th><th style="width:100px;"><?=Yii::t('app', 'Zdjęcie')?></th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Dostępnych')?></th><th><?=Yii::t('app', 'Na stanie')?></th><th><?=Yii::t('app', 'Zarezerwowany')?></th></tr>
    <?php foreach ($gears as $gear){ ?>
<tr>
<td><?php $formModel = \common\helpers\ArrayHelper::getValue($eventRelation,$gear->id, (new \common\models\OfferGear(['offer_id'=>$offer->id, 'gear_id'=>$gear->id])));
                    $content = $this->render('_offer_gear_form',['gear'=>$gear,'offer'=>$offer,'model'=> $formModel, 'type2'=>null, 'item'=>null]);
                    $content .="<br/>".\common\models\OfferGear::getOtherLabel($offer->id, $gear->id, null, null);
                    echo $content;
?>
                    </td>
<td><?php
    if ($gear->photo != null)
                    echo Html::img($gear->getPhotoUrl(), ['width'=>'70px']);
                    ?>
</td>
<td><?=$gear->name?></td>
<td><center>
    <?php
                    if ($gear->type!=1)
                    {
                        echo $gear->quantity;
                    }                  
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $serwis = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        echo $gear->getAvailabe($from_date, $to_date)-$serwisNumber . " " . $serwis;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        $needSerwis = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === \common\models\GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                            if ($item->active == 1 && $item->status === \common\models\GearItem::STATUS_NEED_SERVICE) {
                                $needSerwis++;
                            }
                        }

                        $serwis = null;
                        $need = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        if ($needSerwis > 0) {
                            $need = Html::tag('span', Yii::t('app', 'Wymaga serwisu').': ' . $needSerwis, ['class' => 'label label-warning']);
                        }
                        echo ($gear->getAvailabe($from_date, $to_date)-$serwisNumber) . " " . $serwis." ".$need;
                    }
?>
</center>
</td>
<td><center><?php           if ($gear->no_items)
                    {
                        echo $gear->quantity;
                        
                    }
                    else
                    {
                        echo $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
?>
</center></td>
<td>
<?php
$working = $gear->getEvents($from_date, $to_date);
                    $workingNear = $gear->getEventsNear($from_date, $to_date);
                    $result = "";
                    foreach ($working['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
                                'id' => $eventGear->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($working['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:red; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
                                'id' => $eventGear->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    echo $result;
?>

</td>
</tr>
   <?php } 
$type = "offer";
$type2 = null;
$item = null;
   ?>
</table>
<h1><?=Yii::t('app', 'Magazyn zewnętrzny')?></h1>
<table class="table">
    <tr><th><?=Yii::t('app', 'SZT')?></th><th><?=Yii::t('app', 'Zdjęcie')?></th><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'Na stanie')?></th></tr>
    <?php foreach ($outerGears as $gear){ ?>
    <tr>
        <td>
            <?php
                        $value = '';
                        if ($gear->getIsGearAssigned($offer, $gear, $type2, $item)) {
                            $value = $gear->getAssignedGearNumber($offer, $gear, $type2, $item);
                        }
                        echo Html::input('number', '', $value,
                            [
                                'class' => 'quantity-input2 form-control item-input-id-'.$gear->id,
                                'min' => 0,
                                'max' => $gear->quantity,
                                'data' => [
                                    'id' => $gear->id
                                ],
                            ]);
            ?>

        </td>
        
        <td><?php
if ($gear->photo != null)
                    {
                        echo Html::img($gear->getFileThumbUrl(), ['width'=>'100px']);
                    }
                    
                    ?>
        </td>
        <td><?=$gear->name?></td>
        <td><center><?=$gear->numberOfAvailable()?></center></td>
    </tr>
    <?php } ?>
</table>
    </div>
</div>
</div>

<?php

$eventModelUrl = Url::to(['warehouse/assign-offer-gear', 'id'=>$offer->id, 'type'=>$type, 'model'=>1]);
$offerGearUrl = Url::to(['offer/default/manage-gear', 'offer_id'=>$offer->id]);
$offerGearConnectedUrl = Url::to(['offer/default/manage-gear-connected', 'offer_id'=>$offer->id, 'type2'=>$type2, 'item'=>$item]);
$offerGearOuterConnectedUrl = Url::to(['offer/default/manage-gear-outer-connected', 'offer_id'=>$offer->id, 'type2'=>$type2, 'item'=>$item]);
$eventGearUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$offer->id, 'type'=>$type]);
$eventGroupUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$offer->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$offer->id, 'type'=>$type, 'model'=>1, 'type2'=>$type2, 'item'=>$item]);
$eventGearQuantityUrl = Url::to(['outer-warehouse/assign-outer-gear', 'id'=>$offer->id, 'type'=>$type, 'noItem'=>1]);
$this->registerJs('
$(".quantity-input2").change(function(){
    var id = $(this).data("id");
    var add;
    if (parseInt($(this).val()) === 0) {
        add = false;
    }
    else {
        add = true;
    }
    eventModel(id, add, $(this).val());
});

$(".gear-quantity-field").on("change", function(e)
{
    e.preventDefault();
    var form = $(this).closest("form");
    
    var data = form.serialize();
    $(".gear-quantity-field").prop("disabled", true);
    $.post("'.$offerGearUrl.'", data, function(response){    
        var error = "";
        $(".gear-quantity-field").prop("disabled", false);
        if (response.success==0)
        {
            var error = [response.error];
            toastr.error(error);
        }
        else
        {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do oferty').'");
            form.find("#offergear-id").val(response.gear_id);
                if ((response.connected.length)||(response.outerconnected.length))
                {
                    showConnectedModal(response.connected, response.outerconnected);
                }
        }        
    });
    return false;
});

$(".gear-quantity-field").on("keyup keypress", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 
    e.preventDefault();
    return false;
  }
});


function eventModel(id, add, quantity) {
    var data = {
        itemId : id,
        add : add ? 1 : 0,
        quantity: quantity,
    }
    $.post("'.$eventModelUrl.'", data, function(response){
        if (add) {
            toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
        }
        else {
            toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
        }
    });
}


'
);
?>
<script type="text/javascript">
        function showConnectedModal(gears, outergears){
        var modal = $("#connected_modal");
        modal.find(".modalContent").empty();
        var content = "<table class='table'><thead><tr><th>#</th><th>Nazwa</th><th>Liczba sztuk</th></tr></thead><tbody>";
        for (var i=0; i<gears.length; i++)
        {
            if (gears[i].subgroup==1)
                parent = "data-parentid=\'"+gears[i].gear_id+"\'";
            else
                parent = "data-parentid=\'0\'";
            if (gears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-connectedcheckbox\'  "+parent+" data-gearid=\'"+gears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+gears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+gears[i].count+"'/></td></tr>";
        }
        for (var i=0; i<outergears.length; i++)
        {
            if (outergears[i].subgroup==1)
                parent = "data-parentid=\'"+outergears[i].gear_id+"\'";
            else
                parent = "data-parentid=\'0\'";
            if (outergears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-outerconnectedcheckbox\' "+parent+" data-gearid=\'"+outergears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+outergears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+outergears[i].count+"'/></td></tr>";
        }
        content += "</tbody></table>";
        content += '<div class="row"><div class="pull-right"><a class="btn btn-primary add-connected-button" href="">Dodaj</a> ';
        content += '<a class="btn btn-default close-connected-button" href="">Anuluj</a></div></div>';
        modal.find(".modalContent").append(content);        
        modal.modal("show");
        $(".add-connected-button").click(function(e){ e.preventDefault(); saveConnected();})
        $(".close-connected-button").click(function(e){ e.preventDefault(); $("#connected_modal").modal("hide");})
        }

     function saveConnected()
     {
        $("#connected_modal").find('.gear-connectedcheckbox').each(function(){
            if ($(this).is(":checked"))
            {
            var gear_id = $(this).data('gearid');
            var parent_id = $(this).data('parentid');
            var quantity = $(this).parent().parent().find('.gear-connectedinput').first().val();
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity,
                parent_id:parent_id
                }
                $.post("<?=$offerGearConnectedUrl?>", data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                <?php if ($type=='offer'){ ?>
                                    $("body").find("[data-key='" + response.responses[i].id + "']").find("#offergear-quantity").first().val(response.responses[i].total);
                                    <?php } ?>
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                                }
                                else
                                {
                                    var error = [response.responses[i].error];
                                    toastr.error(response.responses[i].name+" "+error);                               
                                }

                            }
                        }                
                });
            }
               
            }

        });
        $("#connected_modal").find('.gear-outerconnectedcheckbox').each(function(){
            if ($(this).is(":checked"))
            {
            var gear_id = $(this).data('gearid');
            var quantity = $(this).parent().parent().find('.gear-connectedinput').first().val();
            var parent_id = $(this).data('parentid');
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity,
                parent_id:parent_id
                }
                $.post("<?=$offerGearOuterConnectedUrl?>", data, function(response){
                        if (response.responses) {
                            for (var i = 0; i < response.responses.length; i++) {
                                <?php if ($type=='offer'){ ?>
                                    $("body").find("[data-key='" + response.responses[i].id + "']").find("#offergear-quantity").first().val(response.responses[i].total);
                                    <?php } ?>
                                if (response.responses[i].success==1)
                                {
                                    toastr.success("<?=Yii::t('app', 'Dodano')?> "+response.responses[i].name+" "+response.responses[i].quantity+" <?=Yii::t('app', 'szt.')?> ");
                                }
                                else
                                {
                                    var error = [response.responses[i].error];
                                    toastr.error(response.responses[i].name+" "+error);                               
                                }

                            }
                        }                
                });
            }
               
            }

        });

        $("#connected_modal").modal("hide");
     }
</script>