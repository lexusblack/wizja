<?php

use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;



?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Flota'); ?></h3>
<div class="row">
    <div class="col-md-12">
            <div class="ibox">

        <?php
        if ($user->can('eventsEventEditEyeVehiclesManage')) {
            echo Html::a(Yii::t('app', 'Zarządzaj'), ['vehicle/manage', 'id' => $model->id], ['class' => 'btn btn-success'])." ";
            echo Html::a(Yii::t('app', 'Skopiuj zapotrzebowanie z ofert'), ['event/copy-vehicle-from-offer', 'id' => $model->id], ['class' => 'btn btn-primary']) . " ";
        } ?>
            <div class="alert alert-info">
                    <b><u><?= Yii::t('app', 'Zapotrzebowanie z ofert:') ?></u></b><br/>
                        <?php 
                        foreach (\common\models\EventOfferVehicle::find()->where(['event_id'=>$model->id])->all() as $vehicle){
                            echo $vehicle->schedule." ".$vehicle->quantity."x ".$vehicle->vehicle->name." [".$vehicle->vehicle->capacity."kg] [".$vehicle->vehicle->volume."m3]<br/>";
                            } ?>
            </div>
        </div>
    </div>
</div>


            <?php 
            $vehicles = $model->getVehicleNoModel(); 
            if ($vehicles) {?>
            <div class="row" style="margin-bottom:50px;">
            <div class="col-md-2">
            <h4><?=Yii::t('app', 'Samochody nieprzypisane do żadnego etapu:')?>
            </h4>
            </div>
            <div class="col-md-10">
                <div class="team-members">
                <?php foreach ($vehicles as $vehicle){ ?>
                <a href="#"   data-vehicleid="<?=$vehicle->vehicle_id?>"  class="edit_vehicle " style="position:relative;">
                <img alt="image" class="img-circle img-very-small" src="<?php echo $vehicle->vehicle->getPhotoUrl();?>" title="<?=$vehicle->vehicle->name ?>">
                <?=$vehicle->vehicle->name ?></a>
                <?php } ?>
                </div>
            </div>
            </div>
            <?php } ?>


<div class="row">
    <div class="col-md-12">
                    <?php $data =$model->getAssignedVehiclesByTime(); ?>
                        <div id="vertical-timeline2" class="vertical-container dark-timeline" style="width:100%; max-width:100%;">
                            <?php foreach ($data as $key =>$schedule){ $schedule2 = \common\models\EventSchedule::findOne(['event_id'=>$model->id, 'name'=>$key]); $cl = "";?>
                            <div class="vertical-timeline-block">
                                <div class="vertical-timeline-icon navy-bg" style="left:200px;">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <span class="vertical-date">
                                        <h3><?=$key?>
                                                        <?php if ($schedule2){ ?>
                                                        <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', ['/event/update-schedule', 'id' => $schedule2->id], [
                                                            'class' => 'btn btn-xs  add-schedule-v',
                                                            
                                                        ])
                                                        ?>
                                                        <?= Html::a('<i class="fa fa-trash"></i>', ['/event/delete-schedule', 'id' => $schedule2->id], [
                                                            'class' => 'btn btn-danger btn-xs delete-vehicle-role',
                                                            'data' => [
                                                                'confirm' => Yii::t('app', 'Czy na pewno chcesz usunąć?'),
                                                                'method' => 'post',
                                                            ],
                                                        ])
                                                        ?>
                                                        </div>
                                                        <?php }else{ ?>
                                                        <div class="pull-right" style="text-align:right">
                                                        <?= Html::a('<i class="fa fa-plus"></i>', ['/event/add-schedule', 'id' => $model->id, 'name'=>$key], [
                                                            'class' => 'btn btn-xs  add-schedule-v',
                                                            
                                                        ])
                                                        ?>
                                                        </div>
                                                        <?php    } ?>
                                
                                        </h3>
                                        <?php if ($schedule2){ ?>
                                        <span><?=substr($schedule2->start_time,0,10);?> - <?=substr($schedule2->end_time,0,10);?></span><br/>
                                        <small><?=substr($schedule2->start_time,11,5);?> - <?=substr($schedule2->end_time,11,5);?></small>
                                        <?php if (!$schedule2->start_time){ $cl=" no-time"; } ?>

                                         <?php }else{ $cl=" no-time";} ?>
                                                                         <p><?=Html::a("<i class='fa fa-plus'></i> ".Yii::t('app', 'Dodaj zapotrzebowanie'), ['vehicle/add-event-vehicle', 'id'=>$model->id, 'schedule'=>$key], ['class'=>'btn btn-success btn-xs add-event-vehicle'])?></p>
                                </span>
                                <?php 
                                if (!count($schedule))
                                { ?>
                                    <div class="vertical-timeline-content" style="margin-left:250px; min-height:100px;">
                                    </div>
                                <?php }else{
                                    $cl = "";
                                    if ($schedule2){ 
                                    if (!$schedule2->start_time){ $cl=" no-time"; } ?>

                                         <?php }else{ $cl=" no-time";} ?>
                                    ?>
                                    <?php foreach ($schedule as $index => $vehicle){ 
                                    if ($vehicle['added']>=$vehicle['quantity'])
                                    {
                                        $class="text-info";
                                    }else{
                                        $class="text-danger";
                                    }
                                    ?>
                                <div class="vertical-timeline-content" style="margin-left:250px;">
                                    <h2>
                                    <?php $x = \common\models\EventOfferVehicle::findOne(['event_id'=>$model->id, 'vehicle_model_id'=>$index, 'schedule'=>$key]); if ($x){ ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i>', ['/vehicle/update-event-vehicle', 'vehicle_model_id' => $index, 'event_id'=>$model->id, 'schedule'=>$key], [
                                                            'class' => ' btn pull-left btn-sm add-event-vehicle',
                                                            
                                                        ])
                                                        ?>
                                    <?= Html::a('<i class="fa fa-trash"></i>', ['/vehicle/delete-event-vehicle', 'vehicle_model_id' => $index, 'event_id'=>$model->id, 'schedule'=>$key], [
                                                            'class' => ' btn pull-left btn-sm delete-event-vehicle',
                                                            
                                                        ])
                                                        ?>
                                    <?php } ?>
                                    <a href="/admin/vehicle/manage-ajax?id=<?=$model->id?>&vehicle_id=<?=$index?>&schedule=<?=urlencode($key)?>" style="position:relative;" class="assign-vehicle <?=$cl?>" data-vehicle-id="<?=$index?>" data-time_type="1">
                                                        <?= $vehicle['label'] ?> <span class="<?=$class?>"><?= $vehicle['added']."/".$vehicle['quantity']?></span></a>
                                        <div class="team-members pull-right">
                                    <?php foreach ($vehicle['vehicles'] as $v){ 
                                        ?>
                                    <a href="#"   data-vehicleid="<?=$v->vehicle_id?>"  class="edit_vehicle " style="position:relative;">
                                <img alt="image" class="img-circle img-very-small" src="<?php echo $v->vehicle->getPhotoUrl();?>" title="<?=$v->vehicle->name ?>">
                                <small><?=$v->vehicle->name ?></small></a>
                                    <?php } ?>
                                    <?php 
                                    if ($schedule2){ 
                                    $missing = $vehicle['quantity']-$vehicle['added'];
                                    for ($k=0; $k<$missing;$k++){ ?>
                                    <a href="/admin/vehicle/manage-ajax?id=<?=$model->id?>&vehicle_id=<?=$index?>&schedule=<?=urlencode($key)?>" style="position:relative;" class="assign-vehicle <?=$cl?>" data-vehicle-id="<?=$index?>"><span class="badge badge-default pull-right status-bagde"><i class="fa fa-plus"></i></span><img alt="image" class="img-circle img-very-small" src="/img/truck.png"></a>
                                    <?php } }?>
                                    </div>

                                    </h2>
                                </div>
                                <?php } ?>
                                 <?php   } ?>

                                </div>

                            
                            <?php } ?>
                            </div>
                           
            </div>
           
</div>


<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedVehicles(),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    ['class'=>\common\components\grid\PhotoColumn::className()],
                    [
                        'attribute' => 'name',
                        'value' => function ($model, $key, $index, $column) {
                            $content = Html::a($model->name, ['/vehicle/view', 'id'=>$model->id]);
                            return $content;
                        },
                        'format' => 'html',
                    ],
                    'registration_number',
                    'capacity',
                    'volume',
                    'inspection_date',
                    'oc_date',
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'format' => 'html',
                        'value' => function ($vehicle) use ($model) {
                            $workingTime = \common\models\EventVehicleWorkingHours::find()->where(['event_id' => $model->id])->andWhere(['vehicle_id' => $vehicle->id])->all();
                            $toReturn = null;
                            foreach ($workingTime as $work) {
                                $letter = null;
                                if ($model->event_start == $work->start_time && $model->event_end == $work->end_time) {
                                    $letter = " (E)";
                                }
                                if ($model->disassembly_start == $work->start_time && $model->disassembly_end == $work->end_time) {
                                    $letter = " (D)";
                                }
                                if ($model->montage_start == $work->start_time && $model->montage_end == $work->end_time) {
                                    $letter = " (M)";
                                }
                                $toReturn .= $work->start_time . " - " . $work->end_time . $letter . "<br>";
                            }
                            return $toReturn;
                        },
                    ],
                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{remove-assignment}',
                        'controllerId'=>'warehouse',
                        'buttons' => [
                            'remove-assignment' => function ($url, $item, $key) use ($model, $user) {
                                $button = null;
                                if ($user->can('eventsEventEditEyeVehiclesDelete')) {
                                    $button =  Html::a(Html::icon('remove'), ['/vehicle/assign-vehicle', 'id'=>$model->id], [
                                        'data'=> [
                                            'itemId'=>$item->id,
                                            'add'=>0,
                                        ],
                                        'class'=>'remove-assignment-button'
                                    ]);
                                }
                                if ($user->can('eventsEventEditEyeVehiclesEdit')) {
                                    $button .= Html::icon('pencil', ['style'=>'cursor:pointer;','class'=>'edit_vehicle', 'data' => ['vehicleid' => $item->id ] ]);
                                }

                                return $button;
                            }
                        ],
                        'visible' => ($user->can('eventsEventEditEyeVehiclesDelete') || $user->can('eventsEventEditEyeVehiclesEdit'))
                    ]
                ],
            ])
        ?>
    </div>
        </div>
    </div>
</div>
<div class="row">
        <div class="col-md-12">
    <?php //echo $this->render('_map2', ['model'=>$model]); ?>
    </div>
</div>
</div>

<?php

$this->registerJs('

$(".assign-vehicle").click(function(e){
    e.preventDefault();
    if ($(this).hasClass("no-time"))
    {
        alert ("Nim przypiszesz pracowników do tego etapu, należy uzupełnić jego harmonogram.");
    }else{
           var modal = $("#vehicle_modal");
            modal.find(".modalContent").empty().load($(this).attr("href"));
            modal.modal("show"); 
    }

});
    $(".add-event-vehicle").click(function(e){
    e.preventDefault();
           var modal = $("#vehicle_modal");
           modal.find(".modalContent").empty();
           modal.modal("show");
            modal.find(".modalContent").load($(this).attr("href"));
             
});

$("body").on("click", ".edit_vehicle", function(e){
    e.preventDefault();
    openVehicleModal('.$model->id.', $(this).data("vehicleid"));
});

function openVehicleModal(event_id, vehicle_id){
    var modal = $("#vehicle_modal");
    modal.find(".modalContent").load("'.Url::to(["planboard/vehicle-form"]).'?event_id="+event_id+"&vehicle_id="+vehicle_id);
    modal.modal("show");
}

$("#vehicle_modal").on("hidden.bs.modal", function () {
    if (VehicleChanged)
    {
        $("#tab-vehicle").empty();
        $("#tab-vehicle").load("'.Url::to(["event/vehicle-tab", 'id'=>$model->id]).'");
    }
});

VehicleChanged = false;

$(".delete-event-vehicle").click(function(e){
    e.preventDefault();
                            var data2 = {
                            };
                            $.post($(this).attr("href"), data2, function(response){
                                $("#tab-vehicle").empty();
                                 $("#tab-vehicle").load("'.Url::to(["event/vehicle-tab", 'id'=>$model->id]).'");
                            });
});

');