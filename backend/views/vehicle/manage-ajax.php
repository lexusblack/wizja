<?php
/* @var $this \yii\web\View */
/* @var $model \common\models\Event */

use common\models\Vacation;
use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use common\models\Event;
use kartik\dynagrid\DynaGrid;

$vehicle = \common\models\VehicleModel::findOne($vehicle_id);
?>
<div class="warehouse-container">
    <div class="row">
        <div class="col-md-12">
        <h3><?=Yii::t('app', 'Rezerwacja: ')."<strong>".$vehicle->name."</strong>"?>
                        <?php echo Html::a(Html::icon('arrow-left').' Zapisz i zamknij', ['#'], ['class'=>'btn btn-primary pull-right close-modal-v']); ?>

        </h3>
        <P>
            <?php 
            $data =$model->getAssignedVehiclesByTime();;
            foreach ($data as $key =>$schedule){ 
                $schedule2 = \common\models\EventSchedule::findOne(['event_id'=>$model->id, 'name'=>$key]);
                if ($schedule2)
                {
                if (isset($schedule[$vehicle_id]))
                {
                    $r = $schedule[$vehicle_id];
                    echo $schedule2->name.": <span id='schedule-number".$schedule2->id."'>".$r['added']."</span>/".$r['quantity']." ";
                }else{
                    echo $schedule2->name.": <span id='schedule-number".$schedule2->id."'>0</span>/0 ";
                }
                }

            }
            ?>
        </P>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12" style="max-height:600px; overflow-y: scroll;">
            <div class="panel_mid_blocks">
                <div class="panel_block">
                <?php
                $columns =  [
                    [
                        'value'=>function($vehicle) use ($model)
                        {
                            return Html::a('<i class="fa fa-calendar"></i>', ['/vehicle/conflict-calendar', 'vehicle_id'=>$vehicle->id, 'event_id'=>$model->id], ['class'=>"show-calendar-user btn btn-xs btn-default"]);
                        },
                        'format'=>'raw'
                    ],
                    ['class'=>\common\components\grid\PhotoColumn::className()],
                    'name',
                    [
                        'label' => Yii::t('app', 'Zajęte'),
                        'value' => function ($item, $key, $index, $grid) use ($model)
                        {
                            /* @var $item \common\models\Vehicle */
                            $data = $item->getUnavailableRanges($model, true);
                            return implode(' ', $data);
                        },
                        'format' => 'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Serwis'),
                        'value' => function ($item)
                        {
                            if (!$item->status)
                                return Yii::t('app', 'Uwaga! Samochód w serwisie');
                        }
                    ],
                    [
                        'header'=>Yii::t('app', 'Zarezerwuj'),
                        'value'=>function ($vehi, $key, $index, $column) use($vehicle, $model)
                        {
                            return $vehi->getMangeCrewDiv($vehicle, $model);
                        },
                        'format' => 'raw',
                    ]
                ];
                ?>

<?= DynaGrid::widget([
        'gridOptions'=>[
            'filterSelector'=>'.grid-filters',
            'showPageSummary' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'afterRow' => function($model, $key, $index, $grid)
                {
                    $content = "<div class='conflict-calendar' style='height:250px'></div>";
                    return Html::tag('tr',Html::tag('td', $content, ['colspan'=>8, 'style'=>"padding:0; background-color:white;"]), ['class'=>'event-task-details']);
                },
            'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
            ],
            'id'=>'crew-manage-grid',

        
            'toolbar' => [

                '{dynagrid}',
                '{dynagridFilter}',
                '{dynagridSort}'

                ],
        ],
        'allowThemeSetting'=>false,
        'storage'=>DynaGrid::TYPE_COOKIE,
        'options'=>['id'=>'dynagrid-crew'],
        'columns' => $columns,
        
    ]); ?>


                </div>
            </div>
        </div>

    </div>

<?php

$assignUrl = Url::to(['vehicle/assign-vehicle2', 'id'=>$model->id,]);
$calUrl = Url::to(['/crew/conflict-calendar', 'event_id'=>$model->id]);

$this->registerJs('
VehicleChanged = false;


$(".close-modal-v").click(function(e){
    e.preventDefault();
    $("#vehicle_modal").modal("hide");
})

$(":checkbox").on("change", function(e){
    e.preventDefault();
    if ($(this).hasClass("all"))
    {
        var add = $(this).prop("checked");
        $(this).parent().parent().find(":checkbox").each(function(){
            var add2 = $(this).prop("checked");
            if (add2!=add)
            {
                $(this).prop( "checked", add );
                var vehicle_id = $(this).data("vehicle-id");
                var vehicle_model_id = $(this).data("vehicle-model-id");
                var schedule_id = $(this).data("schedule-id");
                var number = parseInt($("#schedule-number"+schedule_id).html());
                if (add){
                    number++;
                    add=1;
                }
                else{
                    add=0;
                    number--;
                }
                if (($(this).hasClass("overlapping"))&&(add))
                {
                       toastr.error("'.Yii::t('app', 'Samochód zajęty w tym okresie - dopasuj godziny w kalendarzu').'");
                       $(this).closest("tr").next().show();
                        $(this).closest("tr").next().find(".conflict-calendar").empty().load("'.$calUrl.'"+"&schedule_id="+schedule_id+"&user_id="+user_id+"&role_id="+role_id);
                }else{
                        $("#schedule-number"+schedule_id).html(number);
                    var data = {
                        vehicle_id: vehicle_id,
                        vehicle_model_id: vehicle_model_id,
                        schedule_id: schedule_id,
                        add: add
                    };
                    $.post("'.$assignUrl.'", data, function(response){
                        if (response.success==1)
                        {
                            toastr.success(response.message);
                        }
                        if (response.success==2)
                        {
                            toastr.error(response.message);
                        }
                        VehicleChanged = true;
                    });
                }

            }

        });
    }else{

        var add = $(this).prop("checked");
        var vehicle_id = $(this).data("vehicle-id");
        var vehicle_model_id = $(this).data("vehicle-model-id");
        var schedule_id = $(this).data("schedule-id");
        var number = parseInt($("#schedule-number"+schedule_id).html());
        if (add){
            number++;
            add=1;
        }
        else{
            add=0;
            number--;
        }
        if (($(this).hasClass("overlapping"))&&(add))
        {
               toastr.error("'.Yii::t('app', 'Samochód zajęty w tym okresie - dopasuj godziny w kalendarzu').'");
               $(this).closest("tr").next().show();
                $(this).closest("tr").next().find(".conflict-calendar").empty().load("'.$calUrl.'"+"&schedule_id="+schedule_id+"&user_id="+user_id+"&role_id="+role_id);
        }else{
                $("#schedule-number"+schedule_id).html(number);
            var data = {
                vehicle_id: vehicle_id,
                vehicle_model_id: vehicle_model_id,
                schedule_id: schedule_id,
                add: add
            };
            $.post("'.$assignUrl.'", data, function(response){
                if (response.success==1)
                {
                    toastr.success(response.message);
                }
                if (response.success==2)
                {
                    toastr.error(response.message);
                }
                VehicleChanged = true;
            });
        }
    }


});

');

$this->registerCss('
    .display_none {display: none;}
    .panel .panel-heading{display:none}
    .kv-editable-link{border-bottom:0}

    .manage-crew-div{float:left; border:1px solid white; padding-left:5px;}

    input[type="checkbox"] {
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

$(".show-calendar-user").click(function(e)
{
    e.preventDefault();
    if ($(this).hasClass("opened"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().show();
        $(this).parent().parent().next().find(".conflict-calendar").empty().load($(this).attr("href"));
    }
    $(this).toggleClass("opened");

});
$(".show-calendar-user").on("contextmenu",function(){
       return false;
    });
');



