<?php

use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;

// $list = [];


$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;

$form = ActiveForm::begin(['id' => 'add_working_time_form',
    'action' => \Yii::$app->request->url . '&update_event_vehicle_data=1',]); ?>
    <div class="form-group">
        <div class="panel panel-primary">
            <div class="panel-heading"><h4><?php echo Yii::t('app', 'Godziny pracy:'); ?></h4></div>
            <div class="panel-body">

                    <?php
                    $roles = \common\helpers\ArrayHelper::map(\common\models\VehicleModel::find()->where(['active'=>1])->asArray()->all(), 'id', 'name');
                    foreach ($model->eventSchedules as $schedule)
                    {
                        if ($schedule->start_time)
                        {
                            //sprawdzamy czy ma blisko eventy 
                            $checked = \common\models\EventVehicleWorkingHours::find()->where(['vehicle_id'=>$vehicle->id, 'event_schedule_id'=>$schedule->id])->one();
                            $checkbox = false;
                            
                            $class = "";
                            $role = null;
                            if ($checked)
                            {
                                $checkbox = true;
                                $role = $checked->vehicle_model_id;
                            }
                            $overlapping = \common\models\EventVehicleWorkingHours::find()->where(['<>', 'event_id', $model->id])->andWhere(['vehicle_id'=>$vehicle->id])->andWhere(['<', 'start_time', $schedule->end_time])->andWhere(['>', 'end_time', $schedule->start_time])->all();
                            $close = false;
                            if ($overlapping)
                            {
                                $class = 'alert alert-danger';
                            }

                             ?>
                            <div class="row <?= $class ?>" style="margin-bottom: 0; border-bottom: 0; padding: 0px">
                                <div class="col-md-3">
                                <label>
                                    <?= Html::checkbox('workWhole'.$schedule->id, $checkbox, ['value' => 1]) ?>
                                    <?=  $schedule->name ?>
                                </label>
                            </div>
                            <div class="col-md-5">
                            <?php if ($checked)
                            {
                                $start = $checked->start_time;
                                $end = $checked->end_time;
                            }else{
                                    $start = $schedule->start_time;
                                    $end = $schedule->end_time;                               
                            }
                            ?>
                            <input type="hidden" name="start<?=$schedule->id?>" id="start<?=$schedule->id?>" value="<?=$start?>"/>
                            <input type="hidden" name="end<?=$schedule->id?>" id="end<?=$schedule->id?>" value="<?=$end?>"/>
                            <p>
                                <input type="text" class="range-slider" data-scheduleid="<?=$schedule->id?>" data-id="<?=$schedule->id?>" id="ranger<?=$schedule->id?>" data-start="<?=substr($start, 0, 16)?>" data-end="<?=substr($end, 0, 16)?>" name="ranger<?=$schedule->id?>" value="0;10"/>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <?= Select2::widget([
                                'data' => $roles,
                                'value'=>$role,
                                'name' => 'vehicles-'.$schedule->id,
                                'id' => 'select-user-evet-role'.$schedule->id,
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz role...'),
                                    'id'=>'select-user-role'.$schedule->id,
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                        </div>
                        </div>
                        <?php if(count($overlapping)>0){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-danger" style="padding: 0px">
                                    <?php
                                    foreach ($overlapping as $packingEvent) {
                                        $packingEvent = $packingEvent->event;
                                        echo Html::a($packingEvent->getTimeStart(). " - " .$packingEvent->getTimeEnd(). " ".$packingEvent->name, ['event/view', 'id' => $packingEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
                            <?php
                        }
                    }
                    ?>

                <div class="row">
                    <div class="col-md-12" id="modal_breaks_grid">
                        <?php
                        $event_id = $model->id;
                        echo GridView::widget(['dataProvider' => $vehicleWorkingHoursDataProvider,
                                'toolbar'=>false,
                            'columns' => [
                            'start_time', 
                            'end_time',
                             ['label'=>Yii::t('app', 'Typ'), 
                             'value'=>function ($model){if ($model->vehicle_model_id) return $model->vehicleModel->name; else return "-";}
                             ],
                                ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',
                                    'buttons' => ['delete' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['planboard/delete-vehicle-working-hours',
                                            'id' => $model->id]), ['class' => 'delete_working_hours']);

                                    },]],],]);
                        ?>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-3">
                        <label>
                            <?=  Yii::t('app', 'Dodaj godziny pracy') ?>:
                        </label>
                    </div>
                    <div class="col-md-5">
                        <?php echo DateRangePicker::widget(['name' => 'eventRange', 'convertFormat' => true,
                            'pluginOptions' => ['timePicker' => true, 'timePickerIncrement' => 5,
                                'timePicker24Hour' => true, 'linkedCalendars'=>false, 'locale' => ['format' => 'Y-m-d H:i'],],
                            'options' => ['id' => 'working-hours-daterange', 'style' => 'width: 200px;',],]) ?>
                    </div>
                    <div class="col-md-4">
                                <?= Select2::widget([
                                'data' => \common\models\VehicleModel::getList(),
                                'name' => 'vehicles-all',
                                'value' => null,
                                'id' => 'select-user-evet-role-all',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz typ...'),
                                    'id'=>'select-vehicle-model-all',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success', 'id' => 'btn-add-workin-hours']) ?>
    <div class="pull-right">
        <?= Html::button(Yii::t('app', 'Zamknij'), ['class' => 'btn btn-primary', 'id' => 'close-modal-btn']) ?>
    </div>
<?php

ActiveForm::end(); ?>
<script type="text/javascript">
    var valuesp = [];
    <?php foreach ($model->eventSchedules as $schedule)
    { if ($schedule->start_time)
        {
            ?>
            valuesp[<?=$schedule->id?>] = [<?php $date = new DateTime($schedule->start_time); while($date->format('Y-m-d H:i')<$schedule->end_time){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($schedule->end_time, 0, 16)."'"; ?> ];
        <?php }
    }
?>
</script>
<?php
$this->registerJs('

        $(".range-slider").each(function()
        {
            $(this).ionRangeSlider({
                type: "double",
                min:0,
                max: valuesp[$(this).data("scheduleid")].length,
                from: valuesp[$(this).data("scheduleid")].indexOf($(this).data("start")),
                to: valuesp[$(this).data("scheduleid")].indexOf($(this).data("end")),
                values: valuesp[$(this).data("scheduleid")],
                onFinish: function (data) {
                    //zapisujemy
                    $("#start"+data.input.data("id")).val(data.fromValue);
                    $("#end"+data.input.data("id")).val(data.toValue);
                },
            });
        });
    $("#vehicle_modal").on("hidden.bs.modal", function () {
        $("body").find("#vehicle_modal").find(".modalContent").html("");
    });
    
    $("#add_working_time_form").submit(function(e){        
        var form = $(this);
        VehicleChanged = true;
        $.post(
            form.attr("action"),
            form.serialize()
        )
        .done(function(result){
            reloadModal();
        })
        .fail(function(){
            console.log("Server error!");
        });
        return false;
    });
    
    $(".delete_working_hours").click(function(e){
        e.preventDefault();
        
        var url = $(this).attr("href");
        if (confirm("'.Yii::t('app', 'Czy na pewno usunąć te godziny pracy?').'")) {
            $.ajax({
                url: url,
                async: false,
                success: function(resp) {
                    console.log(resp);
                }
            });
            reloadModal();
        }    
    });
    
    $("#close-modal-btn").click(function(){
         $("body").find("#vehicle_modal").modal("hide");
    });
    
    function reloadModal() {
        var modal = $("body").find("#vehicle_modal .modalContent");
        modal.html("");
        modal.load("' . Url::to(["planboard/vehicle-form"]) . '?event_id=' . $model->id . '&vehicle_id=' . $vehicle->id .'");
    }
');