<?php

use common\models\Event;
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearItem;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OuterGear;
use kartik\editable\Editable;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use kartik\tabs\TabsX;

$checkGearConflictsUrl = Url::to(['warehouse/gear-conflicts', 'event_id'=>$model->id]);
$eventGearConflictsUrl = Url::to(['warehouse/gear-conflicts-modal', 'event_id'=>$model->id]);
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

Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Kopiuj sprzęt z innego wydarzenia')."</h4>",
    'id' => 'copy_modal',
    'options'=>[
    'tabindex' => false,],
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Brak dostępnych egzemplarzy')."</h4>",
    'id' => 'similar_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]

]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Konflikty do rozwiązania')."</h4>",
    'id' => 'conflicts_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Grupy sprzętowe')."</h4>",
    'id' => 'packlist_modal',
    'size' => 'modal-lg',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Edycja')."</h4>",
    'id' => 'comment_modal',
    'class'=>'inmodal inmodal',
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
/* @var $model \common\models\Event; */
/* @var $this \yii\web\View; */
$user = Yii::$app->user;




$not_returned_gear = '';
$gear_our_outt = \common\models\EventGearOutcomed::find()->where(['event_id'=>$model->id])->andWhere(['>', 'quantity', 0])->all();

foreach ($gear_our_outt as $gear)
{
    $gear_model = Gear::findOne($gear->gear_id);
    if ($gear_model->no_items)
    {
            $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name  . "</td><td></td></tr>";

    }else{
            
            $numbers = GearItem::find()->where(['gear_id'=>$gear_model->id, 'event_id'=>$model->id])->orderBy(['number'=>SORT_ASC])->all();
            $num = "";
            foreach ($numbers as $n)
            {
                if ($num!="")
                    $num.=", ";
                $num .=$n->number;
            }
            $not_returned_gear .= "<tr><td style='white-space: nowrap;'>" . $gear->quantity  . "x " . $gear_model->name  . "</td><td>".$num."</td></tr>";
    }
}
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęt'); ?></h3>
<?php if ($model->getBlocks('gear')) { ?>
<div class="alert alert-danger">
Wydarzenie posiada status, który uniemożliwia edytowanie sprzętu. Aby zmienić rezerwacje sprzętu, zmień status wydarzenia lub poproś administratora o specjalne uprawnienie.
</div>
<?php } ?>
<div class="row">
    <div class="col-md-8">
            <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Packlista PDF - cały sprzęt'), ['packing-list', 'id' => $model->id, 'sort'=>$sort], ['class' => 'btn btn-success btn-sm', 'target'=>'_blank']);?>
            <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Status wydań'), ['total-outcome', 'id' => $model->id], ['class' => 'btn btn-success btn-sm', 'target'=>'_blank']);?>
            <?= Html::a('<i class="fa fa-sort"></i> ' . Yii::t('app', 'Sortuj po kategoriach'), '#', ['class' => 'btn btn-success sort-gear btn-sm', 'data-sort'=>'cat']);?>
            <?= Html::a('<i class="fa fa-sort"></i> ' . Yii::t('app', 'Sortuj po nazwach'), '#', ['class' => 'btn btn-success sort-gear btn-sm', 'data-sort'=>'name']);?>
            <?= Html::a('<i class="fa fa-sort"></i> ' . Yii::t('app', 'Sortuj po miejscu'), '#', ['class' => 'btn btn-success sort-gear btn-sm', 'data-sort'=>'warehouse']);?>
            <?= Html::a('<i class="fa fa-sort"></i> ' . Yii::t('app', 'Sortuj po komentarzach'), '#', ['class' => 'btn btn-success sort-gear btn-sm', 'data-sort'=>'comment']);?>
         <?php

        if ($user->can('eventEventEditEyeGearManage'))   { ?>
            <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj grupę sprzętową'), ['add-packlist', 'id' => $model->id], ['class' => 'btn btn-success add-packlist btn-sm']);?>
            <?php
        }
        

        if (($user->can('eventEventEditEyeGearManage'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))) {

            echo " ".Html::a(Yii::t('app', 'Skopiuj sprzęt z'), ['event/copy-from', 'id' => $model->id, 'type' => 'gear'], ['class' => 'btn btn-info copy-modal btn-sm']);
        }

 ?>
            </div>
            <div class="col-md-4">
            <?php
        if ($not_returned_gear!="") {
            echo Html::a(Yii::t('app', 'Niezwrócony sprzęt'), '#tab-gear', ['class'=>'btn btn-danger', 'id' => 'display-not-returned-gear'])." ";
        }
 ?>
            </div>
</div>

<table class="table table-stripped btn-danger" style="width: 50%; margin: auto; display: none;" id="not-returned-table">
    <tr><td><strong><?= Yii::t('app', 'Niezwrócony sprzęt') ?>:</strong></td><td><strong><?= Yii::t('app', 'Numery') ?>:</strong></td></tr>
    <?= $not_returned_gear ?>
</table>


<?php
$tabItems = [];

foreach ($model->packlists as $packlist)
{
    $tabItems[] = 
                [
                    'label'=>$packlist->name." <i class='fa fa-circle' style='color:".$packlist->color."'></i>",
                    'content'=>$this->render('_tabGearPacklist2', ['model'=>$model, 'packlist'=>$packlist, 'sort'=>$sort]),
                    'visible'=>true,
                ];
}

echo TabsX::widget([
            'items'=>$tabItems,
            'encodeLabels'=>false,
            'enableStickyTabs'=>false,
        ]);

?>

</div>

<?php

$this->registerCss('


.kv-editable {
    display: block;
}

.row-all-gear-out-planned {
    background-color: #449D44;
    color: white;
}
.row-not-all-gear-out-planned {
   /* background-color: yellow; */
}
.row-all-gear-out-unplanned {
    background-color: gray;
    color: white;
}
.row-all-gear-out-planned a, 
.row-all-gear-out-planned button,
.row-all-gear-out-unplanned a, 
.row-all-gear-out-unplanned span {
    color: white;
}

.working-time-link {
    border-bottom: 1px dashed #428bca;
    color: #428bca;
    cursor: pointer;
}
.working-time-link:hover {
    color: #286090;
    border-bottom-color: #286090;
}
.grouped-category-row {
    background-color: orangered;
    color: white;
}

.grouped-category-row2 {
    background-color: #f89069;
    color: white;
}

.table > tbody > tr > td.grouped-category-row {
    padding: 0px 0px 0px 10px;
}
.table > tbody > tr > td.grouped-category-row2 {
    padding: 0px 0px 0px 10px;
}
.remove-assignment-button {
    margin-top: 5px;
    display:block;
}
.kv-editable-loading {
    display: none !important;
}

.yellow-background {
    background-color: yellow;
}

.yellow-border {
    border: 3px solid #aa21ee;
}

');
$event = $model;
$type = 'event';
$eventGearConnectedUrl = Url::to(['warehouse/assign-gear-connected', 'id'=>$event->id, 'type'=>$type]);
$eventGearSimilarUrl = Url::to(['warehouse/gear-similar', 'id'=>$event->id]);
$saveSimilarUrl = Url::to(['warehouse/save-similar', 'id'=>$event->id]);
$saveConflictUrl = Url::to(['warehouse/save-conflict', 'id'=>$event->id]);
$eventGearUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type]);
$eventGearGroupCheckUrl = Url::to(['warehouse/assign-check-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventGroupUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'group'=>1]);
$eventModelUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'model'=>1]);
//$eventGearQuantityUrl = Url::to(['warehouse/assign-gear', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1]);
$eventGearQuantityUrl = Url::to(['warehouse/assign-gear-packlist', 'id'=>$event->id, 'type'=>$type, 'noItem'=>1]);
$eventGearModelUrl = Url::to(['gear/get-gear-as-json']);
$eventGearList = Url::to(['warehouse/get-assigned-gear', 'event_id'=>$event->id, 'type'=>$type]);

$this->registerJs('
    sort = "'.$sort.'";

    $(".sort-gear").click(function(e){
        e.preventDefault();
        $("#tab-gear").empty();
        $("#tab-gear").load("'.Url::to(["event/gear-tab", 'id'=>$model->id]).'&sort="+$(this).data("sort"));
    });

$(".block-button").on("click", function(e){
    e.preventDefault();
    $.post($(this).attr("href"), [], function(response){
        $("#tab-gear").empty();
        $("#tab-gear").load("'.Url::to(["event/gear-tab", 'id'=>$model->id]).'");
    });
    
});
$(".add-packlist").on("click", function(e){
    e.preventDefault();
    var modal = $("#packlist_modal");
    $("#packlist_modal").find(".modalContent").empty();
    modal.modal("show");
    modal.find(".modalContent").load($(this).attr("href"));
});

$(".copy-modal").on("click", function(e){
    e.preventDefault();
    var modal = $("#copy_modal");
    modal.modal("show").find(".modalContent").load($(this).attr("href"));
});

$(".gear-quantity").on("change", function(e){
    e.preventDefault();
    var pack = $(this).data("packlist")
    var start = $(this).data("start")
    var end = $(this).data("end")
    var form = $(this).closest("form");
    var data = form.serialize();
    var value = $(this).val();
    var oldValue = form.find("#gearassignment-oldquantity").val();
    var gearid = form.find("#gearassignment-itemid").val();

    $.post("'.$eventGearQuantityUrl.'&packlist="+pack+"&start="+start+"&end="+end, data, function(response){
         
        var error = "";
        if (response.success==0)
        {
            var error = [response.error];
             toastr.error(error);
                /*if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }*/
            //brak wolnych egzemplarzy, wyswietlamy okienko z podobnymi
                showSimilarModal(data, pack, start, end);
        }
        else
        {
            if (value>0)
            {
                toastr.success("'.Yii::t('app', 'Sprzęt dodany do eventu').'");
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }
                resolveConflict();
            }
            else{
                toastr.error("'.Yii::t('app', 'Sprzęt usunięty z eventu').'");
            }
            if (value<oldValue)
            {
                $.post("'.$checkGearConflictsUrl.'&gear_id="+gearid, data, function(response){
                    if (parseInt(response.conflicts)>0)
                    {
                        number = oldValue-value;
                        showConflictsToResolveModal(gearid, number);
                    }
                });
            }
        }
        $(".gear-assignment-form").yiiActiveForm("updateAttribute", "gearassignment-quantity", error);
        
    });
    
   var gear_id = $(this).data("gearid");
   
   $.get("'.$eventGearModelUrl.'?id="+gear_id, null, function(gear){
        $.when(addGearRow(gear)).then(function(){
            var gear_row = $(".gear-row[data-gearid=\'"+gear_id+"\']");
            $(gear_row.children()[0]).html(gear_id);
            $(gear_row.children()[2]).html(value);
        });
   });
   
    
   return false;
});

');

?>

<!--<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>-->
<script type="text/javascript">
        function showConnectedModal(gears){
        var modal = $("#connected_modal");
        modal.find(".modalContent").empty();
        var content = "<table class='table'><thead><tr><th>#</th><th>Nazwa</th><th>Liczba sztuk</th></tr></thead><tbody>";
        for (var i=0; i<gears.length; i++)
        {
            if (gears[i].checked==1)
                checked = "checked";
            else
                checked = "";
            checkbox = "<td><input class=\'gear-connectedcheckbox\'  data-gearid=\'"+gears[i].id+"\' type=\'checkbox\' "+checked+"></td>";
            content += "<tr>"+checkbox+"<td>"+gears[i].name+"</td><td><input class=\'gear-connectedinput\'  type='text' value='"+gears[i].count+"'/></td></tr>";
        }
        content += "</tbody></table>";
        content += '<div class="row"><div class="pull-right"><a class="btn btn-primary add-connected-button" href="#">Dodaj</a> ';
        content += '<a class="btn btn-default close-connected-button" href="#">Anuluj</a></div></div>';
        modal.find(".modalContent").append(content);        
        modal.modal("show");
        $(".add-connected-button").click(function(){ saveConnected();})
        $(".close-connected-button").click(function(){  $("#connected_modal").modal("hide");})
        }

        function showSimilarModal(data, pack, start, end){
            <?php if ($type=='event'){ ?>
            var modal = $("#similar_modal");
            modal.find(".modalContent").empty();
            $.post("<?=$eventGearSimilarUrl?>&packlist="+pack+"&start="+start+"&end="+end, data, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
            });        
            <?php } ?>
        
        }

        function bookSimilars(pack, start, end){
            $.post('<?=$saveSimilarUrl?>&packlist_id='+pack+"&start="+start+"&end="+end, $("#similarForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                                $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignment-quantity").first().val(response.responses[i].total);
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
                if (response.connected.length)
                {
                    showConnectedModal(response.connected);
                }
                $("#similar_modal").modal("hide");
            });

        }

        function bookConflicts(pack, start, end){
            $.post('<?=$saveConflictUrl?>&packlist_id='+pack+"&start="+start+"&end="+end, $("#conflictForm").serialize(), function(response){
                    if (response.responses) {
                        for (var i = 0; i < response.responses.length; i++) {
                            $("body").find("[data-key='" + response.responses[i].id + "']").find("#gearassignment-quantity").first().val(response.responses[i].total);
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
                        if (response.connected.length)
                        {
                            showConnectedModal(response.connected);
                        }
                    }
                $("#similar_modal").modal("hide");
            });
        }

     function saveConnected()
     {
        $("#connected_modal").find('.gear-connectedcheckbox').each(function(){
            if ($(this).is(":checked"))
            {
            var gear_id = $(this).data('gearid');
            var quantity = $(this).parent().parent().find('.gear-connectedinput').first().val();
            if (!isNaN(quantity)){
                quantity = parseInt(quantity);
                var data = {
                gear_id : gear_id,
                quantity: quantity
                }
                $.post("<?=$eventGearConnectedUrl?>", data, function(response){
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

        })


        $("#connected_modal").modal("hide");
     }   
    function resolveConflict()
    {

    }

    function showConflictsToResolveModal(gear_id, number){
            var modal = $("#conflicts_modal");
            modal.find(".modalContent").empty();
            data = [];
            $.post("<?=$eventGearConflictsUrl?>&gear_id="+gear_id+"&number="+number, data, function(response){
                modal.find(".modalContent").append(response); 
                modal.modal("show");
            });        
        
        }
</script>