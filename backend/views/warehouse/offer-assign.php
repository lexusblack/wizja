<?php
/* @var $this \yii\web\View */
/* @var $event \common\models\Event */

use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use common\helpers\ArrayHelper;
use common\models\GearItem;
use common\models\OfferGear;
use yii\bootstrap\Modal;
$user = Yii::$app->user;
$warehouses = \common\models\Warehouse::find()->all();
use kop\y2sp\ScrollPager;
use backend\modules\permission\models\BasePermission;

$eventModelUrl = Url::to(['warehouse/assign-offer-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
$offerGearUrl = Url::to(['offer/default/manage-gear', 'offer_id'=>$event->id]);
$offerGearConnectedUrl = Url::to(['offer/default/manage-gear-connected', 'offer_id'=>$event->id, 'type2'=>$type2, 'item'=>$item]);
$offerGearOuterConnectedUrl = Url::to(['offer/default/manage-gear-outer-connected', 'offer_id'=>$event->id, 'type2'=>$type2, 'item'=>$item]);
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


<div class="menu-pils">
    <?= $this->render('_categoryMenu'); ?>
</div>
<?php
echo $this->render('_toolsAssign',['warehouse'=>$warehouse]);

?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
        <div class="ibox float-e-margins">

            <?php echo Html::a(Html::icon('arrow-left').' '.Yii::t('app', 'Zapisz i wróć'), [$this->context->returnRoute, 'id'=>$event->id], ['class'=>'btn btn-primary']); ?>
            <?php echo Html::a(Yii::t('app', 'Magazyn zewnętrzny'), array_merge(['outer-warehouse/assign'], $_GET), ['class'=>'btn btn-success']); ?>
        </div>
        </div>
    </div>
    <div class="gear gears row">
        <div class="ibox float-e-margins">
                <div class="ibox-title newsystem-bg">
                    <h4><?php echo $title; ?></h4>
                </div>
        <div class="ibox-content">
        <?php
        echo $this->render('_sets', ['gearSet'=>$gearSets, 'event'=>$event, 'type'=>$type, 'category'=>$category, 'type2'=>$type2, 'item'=>$item]);
        $gearColumns = [
            [
                'headerOptions'=>[
                    'width'=>'50'
                ],
                'content'=>function($model, $key, $index, $grid) use ($event,$eventRelation, $type2, $item)
                {
                    $formModel = ArrayHelper::getValue($eventRelation,$model->id, (new \common\models\OfferGear(['offer_id'=>$event->id, 'gear_id'=>$model->id])));
                    $content = $this->render('_offer_gear_form',['gear'=>$model,'offer'=>$event,'model'=> $formModel, 'type2'=>$type2, 'item'=>$item]);
                    $content .="<br/>".OfferGear::getOtherLabel($event->id, $model->id, $type2, $item);
                    return $content ?  $content : false;
                },
                'format'=>'html',
            ],
                        [
                        'label' => Yii::t('app', 'Pakowany'),
                        'format'=>'raw',
                        'value'=> function($model){
                            $result = "";
                            foreach ($model->getPacking() as $case){
                                $result .= $case."</br>";
                            }
                            return $result;
                        }
            ],
            [
                'attribute' => 'photo',
                'value' => function ($model, $key, $index, $column) {
                    /* @var $model \common\models\OuterGear */
                    if ($model->photo == null)
                    {
                        return '-';
                    }
                    return Html::img($model->getPhotoUrl(), ['width'=>'100px']);
                },
                'format'=>'raw',
                'contentOptions'=>['class'=>'text-center'],
            ],
            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) use ($warehouse) {
                    $content = Html::a($model->name, ['gear/view', 'id'=>$model->id]);
                    return Html::a(Html::icon(' fa fa-calendar'), ['/gear/calendar', 'id'=>$model->id, 'start'=>$warehouse->from_date, 'end'=>$warehouse->to_date], ['class'=>'calendar-button btn btn-xs btn-success', 'data-id'=>$model->id])." ".$content;
                },
                'format' => 'html',
            ],

            [
                //'attribute'=>'available',
                'format' => 'html',
                'label' => Yii::t('app', 'Dostępnych'),
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'value'=>function($gear, $key, $index, $column) use ($warehouse, $assignedModels)
                {
                    //$assigned = key_exists($gear->id, $assignedModels) ? $assignedModels[$gear->id] : 0;
                    $assigned = 0;
                    if ($gear->no_items)
                    {
                        return $gear->getAvailabe($warehouse->from_date, $warehouse->to_date)+$assigned;
                    }
                    else
                    {
                        $serwisNumber = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_SERVICE])->count();
                        $needSerwis = GearItem::find()->where(['gear_id'=>$gear->id, 'active'=>1, 'status'=>GearItem::STATUS_NEED_SERVICE])->count();

                        $serwis = null;
                        $need = null;
                        if ($serwisNumber > 0) {
                            $serwis = Html::tag('span', Yii::t('app', 'W serwisie').': ' . $serwisNumber, ['class' => 'label label-danger']);
                        }
                        if ($needSerwis > 0) {
                            $need = Html::tag('span', Yii::t('app', 'Wymaga serwisu').': ' . $needSerwis, ['class' => 'label label-warning']);
                        }

                        return ($gear->getAvailabe($warehouse->from_date, $warehouse->to_date)-$serwisNumber)+$assigned . " " . $serwis." ".$need;
                    }
                }
            ],
            [
                'label'=>Yii::t('app', 'Na stanie'),
                'format'=>'raw',
                'visible'=> ($user->can('gearWarehouseQuantity')),
                'contentOptions'=>['style'=>'white-space:nowrap;'],
                'value'=>function($gear, $key, $index, $column) use ($warehouses)
                {
                    /* @var $gear \common\models\Gear */
                    if ($gear->no_items)
                    {
                        $return = $gear->quantity;
                        
                    }
                    else
                    {
                        $return = $gear->getGearItems()->andWhere(['active'=>1])->count();
                    }
                    $similar = $gear->getSimilarCount();
                    if ($similar){
                        $tot = $return+$similar;
                        $return .="<br/>+".Yii::t('app', 'podobne')." ".$similar." (".$tot.")";
                    }
                    if ($warehouses)
                    {
                            foreach ($warehouses as $w)
                            {
                                $return .= $w->getNumberLabel($gear);
                            }
                    }
                    return $return;

                }
            ],
 [
                'label' => Yii::t('app', 'Zarezerwowany'),
                'format' => 'raw',
                'visible'=> ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL])),
                'value' => function ($model) use ($warehouse, $user) {
                                                            $return = "";
                    if ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL]))
                    {
                    $working = $model->getEvents($warehouse->from_date, $warehouse->to_date);
                    $workingNear = $model->getEventsNear($warehouse->from_date, $warehouse->to_date);
                    $result = "";
                    foreach ($working['events'] as $eventGear)
                    {
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
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
                        $result .= Html::a(substr($eventGear->start_time, 0, 10) . " - " . substr($eventGear->end_time, 0, 10) . " " . $eventGear->packlist->event->name . " (".$eventGear->quantity.")<br>", ['event/view',
                                'id' => $eventGear->packlist->event_id, "#" => "tab-gear"], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    foreach ($workingNear['rents'] as $rentGear)
                    {
                        $result .= Html::a(substr($rentGear->start_time, 0, 10) . " - " . substr($rentGear->end_time, 0, 10) . " " . $rentGear->rent->name . " (".$rentGear->quantity.")<br>", ['rent/view',
                                'id' => $rentGear->rent_id], ['target' => '_blank',
                                'class' => 'linksWithTarget', 'style' => 'color:orange; white-space: nowrap;']);                       
                    }
                    return $result;
                    }else{
                        return "-";
                    }
                }
            ],
            [
                'label' => Yii::t('app', 'W ofertach'),
                    'contentOptions' => function ($model){
                            return ['style'=>'white-space:nowrap; cursor:pointer;', 'class' => 'info2'];
                    },
                    'visible'=> ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL])),
                'value' => function ($model) use ($warehouse, $event, $user){
                                                            $return = "";
                    if ($user->can('eventsEvents'.BasePermission::SUFFIX[BasePermission::ALL]))
                    {
                    $events = $model->getOffersInPeriod($warehouse->from_date, $warehouse->to_date, $event->id);
                    $content = "";
                    foreach ($events as $event)
                    {
                        if ($event->offer->status==1)
                            $content.='<span class="label label-primary" title="'.Yii::t('app', 'Oferta zaakceptowana').'"><i class="fa fa-check"></i></span>';
                        $content.= Html::a(mb_substr($event->offer->name, 0,15)."...", ['/offer/default/view', 'id'=>$event->offer_id])." [".$event->quantity." ".Yii::t('app', 'szt.')."] ".substr($event->offer->getTimeStart(), 0, 16)." - ".substr($event->offer->getTimeEnd(), 0, 16)."<br/>";
                    }
                    if (count($events)>3)
                    {
                        $content ="<div class='display_none'>".$content."</div><span>".Yii::t('app', 'Sprzęt w ').count($events).Yii::t('app', ' ofertach. Pokaż')."</span>";
                    }
                    return $content;
                    }else{
                        return "-";
                    }
                },
                'format' => 'raw',
            ],
            [
                'label'=>Yii::t('app', 'Cena'),
                'visible'=> ($user->can('gearWarehousePrices')),
                'format'=>'raw',
                'value'=> function($model) use($event)
                {
                    $prices = $model->getOfferPrices($event->priceGroup);
                    $content = "";
                    foreach ($prices as $price)
                    {
                        $content .=$price->gearsPrice->name.":<strong>".$price->price.$price->gearsPrice->currency."</strong></br>";
                    }
                    return $content;
                }
            ],
            ];
             ?>


        <div class="panel_mid_blocks">
            <div class="panel_block">

                <?php

        echo GridView::widget([
            'dataProvider' => $warehouse->gearDataProvider,
            'filterModel' => null,
            'columns' => $gearColumns,
                    'pager' => [
            'class'     => ScrollPager::className(),
            'container' => '.grid-view tbody',
            'item'      => 'tr',
            'paginationSelector' => '.grid-view .pagination',
            'eventOnRendered' => 'function() {
                $( ".gear-quantity-field").unbind( "change" );
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
            }',
            'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            'enabledExtensions'  => [
                ScrollPager::EXTENSION_SPINNER,
                //ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
        ],
        ]); ?>
            </div>
        </div>
    </div>
    </div>

</div>

<?php


$this->registerJs('

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

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});
');


$this->registerCss('
    .rc-handle-container { display: none; }
')
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
<?php
$this->registerJs('


$(".info2").click(function(){
    $(this).find("span").first().toggleClass("display_none");

    var ourDiv = $(this).find("div").first();
    if (ourDiv.hasClass("display_none")) {
        ourDiv.slideDown();
    }
    else {
        ourDiv.slideUp();
    }
    ourDiv.toggleClass("display_none");
});

$(".calendar-button").click(function(e){
    e.preventDefault();
    wname = "kalendarz"+ $(this).attr("data-id");
    window.open($(this).attr("href"), wname ,"height=500,width=850");
})

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
');
?>