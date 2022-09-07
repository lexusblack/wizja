<?php

use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

Modal::begin([
    'header' => Yii::t('app', 'Rezerwacja'),
    'id' => 'hall_modal',
    'class'=>'inmodal inmodal',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

$this->title = Yii::t('app', 'Rezerwacja powierzchni na '). $event->name;
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['/event/view', 'id'=>$event->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
        <div class="row">
        
        <div class="col-md-12">
        <?php echo Html::a(Html::icon('arrow-left').Yii::t('app', ' Wróć'),  ['/event/view', 'id'=>$event->id, '#'=>'tab-hall'], ['class'=>'btn btn-primary btn-sm btn-return']); ?>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
        <?php

            echo $event->getScheduleDiv();

        ?>
        </div>
        <div class="col-md-6">
        <input type="text" id="js-range-slider-packlist" data-start="<?=substr($event->event_start, 0, 16)?>" data-end="<?=substr($event->event_end, 0, 16)?>" name="range" value="0;10"/>
        <input type="hidden" id="warehouse_start" name="warehouse_start" value="<?=$event->event_start?>"/>
        <input type="hidden" id="warehouse_end" name="warehouse_end" value="<?=$event->event_end?>"/>
        </div>
        </div>
        <div class="row">
        <div class="col-md-12">
        <?php
        $gearColumns = [
                    [ 
                            'label'=>Yii::t('app', 'Zarezerwuj'),
                            'format'=>'raw',
                            'value'=>function($model) use ($event)
                            {
                                $checked = \common\models\EventHallGroup::find()->where(['event_id'=>$event->id, 'hall_group_id'=>$model->id])->count();
                                if ($checked)
                                {
                                    return '<input type="checkbox" data-hallid='.$model->id.' checked class="hall-checkbox">';
                                }else{
                                    return '<input type="checkbox" data-hallid='.$model->id.' class="hall-checkbox">';
                                }
                            }
                    ],
                    [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) use($event) {
                    $content = Html::a($model->name, ['hall/view', 'id'=>$model->id]);
                    return Html::a(Html::icon(' fa fa-calendar'), ['/hall-group/calendar', 'id'=>$model->id, 'start'=>$event->event_start, 'end'=>$event->event_end], ['class'=>'calendar-button btn btn-xs btn-success', 'data-id'=>$model->id, 'target'=>'_blank'])." ".$content;
                },
                'format' => 'raw',
            ],
            [
                'attribute'=>'area'
            ],
            [
                'label'=>Yii::t('app', 'Segmenty'),
                'value'=>function($model)
                {
                    $content = "";
                    $first = true;
                    foreach ($model->halls as $hall)
                    {
                        if (!$first)
                            $content.=", ";
                        $first=false;
                        $content.=$hall->name;
                    }
                    return $content;
                }
            ],
            [
                'label'=>Yii::t('app', 'Rezerwacje'),
                'format'=>'raw',
                'value'=>function($model) use ($event)
                {
                    $content = "";
                    $class="";
                    foreach ($model->getEventsOverlapping($event->event_start, $event->event_end) as $e)
                    {
                        $content .= "<i class='fa fa-circle' style='color:".$e->statut->color."'></i> ".$e->event->name." ".substr($e->start_time, 0, 16)." - ".substr($e->end_time, 0, 16)."<br/>";
                        if ($e->statut->final)
                        {
                            $class="booked";
                        }
                    }
                    return "<div class='bookings ".$class."' data-hallid=".$model->id.">".$content."</div>";
                }
            ]
        ];
        ?>
                <?php

        echo GridView::widget([
            'layout'=>'{items}',
            'dataProvider' => $hallDataProvider,
            'tableOptions' => [
                'class' => 'kv-grid-table table table-condensed kv-table-wrap',
                'id'=>'assign-hall-grid'
            ],
            'filterModel' => null,
            'columns' => $gearColumns,
        ]); ?>
        </div>
        </div>
<script type="text/javascript">
    var tvalues = [];
        tvalues = [<?php $date = new DateTime($event->event_start); 
        while($date->format('Y-m-d H:i')<$event->event_end){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($event->event_end, 0, 16)."'"; ?> ];
</script>
<?php
$checkAvability = Url::to(['/hall-group/check-avability', 'event_id'=>$event->id]);
$removeHall = Url::to(['/hall-group/remove', 'event_id'=>$event->id]);
$bookingsUrl = Url::to(['/hall-group/bookings']);
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

    $(".hall-checkbox").click(function(e){
            //sprawdzamy czy jest wolne w tym czasie
        if ($(this).is(":checked"))
        {
                
                href = "'.$checkAvability.'&hall_id="+$(this).data("hallid")+"&end="+encodeURIComponent($("#warehouse_end").val())+"&start="+encodeURIComponent($("#warehouse_start").val());
                var modal = $("#hall_modal");
                    modal.find(".modalContent").load(href);
                    modal.modal("show");
        }else{
            href = "'.$removeHall.'&hall_id="+$(this).data("hallid");
            $.post(href, {}, function(response){toastr.error("'.Yii::t('app', 'Rezerwacja usunięta').'");});
        }
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

        reloadBookings();


    });

        markBooked();
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
                                reloadBookings();
                },
            });
');
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";
?>

<script type="text/javascript">
    function markBooked()
    {
        //$(".booked").parent().parent().addClass("booked-row");
    }

            function reloadBookings()
        {
            $(".bookings").each(function(){
                $(this).empty();
                hall_id = $(this).data("hallid");
                start = $("#warehouse_start").val();
                end = $("#warehouse_end").val();
                data = [];
                $(this).append("<?=$spinner?>");
                var bdiv = $(this);
                $.post("<?=$bookingsUrl?>"+"?hall_id="+hall_id+"&start="+start+"&end="+end, data, function(response){
                bdiv.empty();
                bdiv.append(response); 
                }); 
            });
        }
</script>
