<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */

$user = Yii::$app->user;
?>
<h1><?=Yii::t('app', 'Zmiana czasu rezerwacji')?></h1>
        <div class="row">
        <div class="col-md-6">
        <?php

            echo $packlist->getScheduleDiv();

        ?>
        </div>
        <div class="col-md-6">
        <div style="width:400px;">
        <input type="text" id="js-range-slider-packlist" data-start="<?=substr($packlist->start_time, 0, 16)?>" data-end="<?=substr($packlist->end_time, 0, 16)?>" name="range" value="0;10"/>
        <input type="hidden" id="warehouse_start" name="warehouse_start" value="<?=substr($packlist->start_time, 0, 16)?>"/>
        <input type="hidden" id="warehouse_end" name="warehouse_end" value="<?=substr($packlist->end_time, 0, 16)?>"/>
        </div>
        </div>
        </div>

<table class="table" id="gear-table">
<tr><th><?=Yii::t('app', 'Nazwa')?></th><th><?=Yii::t('app', 'L. sztuk')?></th><th><?=Yii::t('app', 'Obecny czas pracy')?></th></tr>
<?php foreach ($gears as $gear){ ?>
<tr data-gear-id=<?=$gear->id?> data-start="<?=$gear->start_time?>" data-end="<?=$gear->end_time?>">
    <td><?=$gear->gear->name?></td>
    <td><?=$gear->quantity?></td>
    <td><?=substr($gear->start_time,0,16)." - ".substr($gear->end_time,0,16)?></td>
    <td class="missing"></td>
</tr>
<?php } ?>
</table>
<?=Html::a(Yii::t("app", "Zapisz"), "#", ['class'=>"btn btn-primary pull right", 'id'=>"save-hours"])?>
<?=Html::a(Yii::t("app", "Zapisz i stwórz konflikty"), "#", ['class'=>"btn btn-danger pull right", 'id'=>"save-hours2"])?>
<?php
$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}

    .manage-crew-div{float:left; border:1px solid white; padding-left:5px;}

    .manage-crew-div input[type="checkbox"] {
  transform: scale(1.5);
  -ms-transform: scale(1.5);
  -webkit-transform: scale(1.5);
  -o-transform: scale(1.5);
  -moz-transform: scale(1.5);
  transform-origin: 0 0;
  -ms-transform-origin: 0 0;
  -webkit-transform-origin: 0 0;
  -o-transform-origin: 0 0;
  -moz-transform-origin: 0 0;
  margin-left:10px;
}
');

$this->registerJs('

    $("#save-hours").click(function(e){
            e.preventDefault();
            saveHours();
    });
    $("#save-hours2").click(function(e){
            e.preventDefault();
            saveHoursMax();
    });
    $(".schedule-checkbox-packlist").click(function(e){
        start = "'.substr($event->event_end, 0, 16).'";
        end = "'.substr($event->event_start, 0, 16).'";
        $("#schedule-box").find(".schedule-checkbox-packlist").each(function(){
            if ($(this).prop("checked"))
            {
                if ($(this).data("start")<start)
                {
                    start = $(this).data("start");
                }
                if ($(this).data("end")>end)
                {
                    end = $(this).data("end");
                }
            }
        });
        if (start<=end)
        {
            $("#js-range-slider-packlist").ionRangeSlider("update", {
                from: tvalues.indexOf(start),
                to: tvalues.indexOf(end)
                });
            $("#warehouse_start").val(start);
            $("#warehouse_end").val(end);
            }else
            {
            $("#js-range-slider-packlist").ionRangeSlider("update", {
                from: tvalues.indexOf(end),
                to: tvalues.indexOf(start)
                });
                $("#warehouse_start").val(end);
                $("#warehouse_end").val(start);
            }
            reloadAvability();


    });


        $("#js-range-slider-packlist").ionRangeSlider({
                type: "double",
                min:0,
                max: tvalues.length,
                from: tvalues.indexOf($("#js-range-slider-packlist").data("start")),
                to: tvalues.indexOf($("#js-range-slider-packlist").data("end")),
                values: tvalues,
                onFinish: function (data) {
                                $("#warehouse_start").val(data.fromValue);
                                $("#warehouse_end").val(data.toValue);
                                reloadAvability();
                },
            });
        reloadAvability();
');
$eventGearCheckAvability = Url::to(['warehouse/check-avability', 'id'=>$event->id]);
$eventGearSaveHours = Url::to(['warehouse/save-gear-hours', 'id'=>$event->id]);
$eventGearSaveHoursMax = Url::to(['warehouse/save-gear-hours-max', 'id'=>$event->id]);
?>

<script type="text/javascript">
    var tvalues = [];
        tvalues = [<?php $date = new DateTime($event->event_start); 
        while($date->format('Y-m-d H:i')<$event->event_end){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($event->event_end, 0, 16)."'"; ?> ];

        function saveHours() 
        {
                //sprawdzamy i zaznaczamy na czerwono, którym nie można zmienić
                var start = $("#warehouse_start").val();
                var end = $("#warehouse_end").val();
                $("#gear-table").find("tr").each(function()
                {
                        start2 = $(this).data("start");
                        end2 = $(this).data("end");
                        var tr = $(this);
                        if (start2)
                        {
                                    data = [];
                                    $.post("<?=$eventGearSaveHours?>&start="+start+"&end="+end+"&gear_id="+tr.data("gear-id"), data, function(response){
                                        if (response.success==1)
                                        {
                                                    
                                                    tr.after("<tr><td colspan=4>Zapisano!</td></tr>");
                                                    tr.remove();
                                        }else{
                                            tr.css("background-color", "#fff");
                                            tr.find(".missing").empty().append("<span class='label label-danger'><?=Yii::t('app', 'Brakuje:')?>"+response.missing+"</span>");
                                            tr.find(".missing").empty().append("<a href='#' class='btn btn-xs btn-danger'><?=Yii::t('app', 'Stwórz konflikt')?></a>");
                                        }
                                    });
                                    
                        }

                });
        }
        function saveHoursMax() 
        {
                //sprawdzamy i zaznaczamy na czerwono, którym nie można zmienić
                var start = $("#warehouse_start").val();
                var end = $("#warehouse_end").val();
                $("#gear-table").find("tr").each(function()
                {
                        start2 = $(this).data("start");
                        end2 = $(this).data("end");
                        var tr = $(this);
                        if (start2)
                        {
                                    data = [];
                                    $.post("<?=$eventGearSaveHoursMax?>&start="+start+"&end="+end+"&gear_id="+tr.data("gear-id"), data, function(response){
                                        if (response.success==1)
                                        {
                                                    
                                                    tr.after("<tr><td colspan=4>Zapisano!</td></tr>");
                                                    tr.remove();
                                        }
                                    });
                                    
                        }

                });
        }
        function reloadAvability()
        {
                //sprawdzamy i zaznaczamy na czerwono, którym nie można zmienić
                var start = $("#warehouse_start").val();
                var end = $("#warehouse_end").val();
                $("#gear-table").find("tr").each(function()
                {
                        start2 = $(this).data("start");
                        end2 = $(this).data("end");
                        var tr = $(this);
                        if (start2)
                        {
                            if ((start<start2)||(end>end2))
                                {
                                    data = [];
                                    $.post("<?=$eventGearCheckAvability?>&start="+start+"&end="+end+"&gear_id="+tr.data("gear-id"), data, function(response){
                                        if (response.success==1)
                                        {
                                            tr.css("background-color", "#d4edda");
                                            tr.find(".missing").empty();
                                        }else{
                                            tr.css("background-color", "#f8d7da");
                                            tr.find(".missing").empty().append("<span class='label label-danger'><?=Yii::t('app', 'Brakuje:')?>"+response.missing+"</span>");
                                        }
                                    });
                                    
                            }else{
                                tr.css("background-color", "#d4edda");
                                tr.find(".missing").empty();
                            }
                        }

                });            
        }
</script>