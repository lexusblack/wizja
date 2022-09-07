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
                if ($model->packing_start && $model->packing_end) { 
                        if(count($overlapingEvents['packing'])>0) {
                            $classPacking = 'alert alert-danger';
                            $disablePacking = 'disabled';
                            $checked_packing = false;
                        }else{
                        $classPacking = null;
                        $disablePacking=null;
                            
                        }
                    ?>
                    <div class="row <?= $classPacking ?>">
                        <div class="col-md-2">
                            <label>
                                <?= Html::checkbox('workWholePacking', $checked_packing, ['value' => 1, 'disabled' => $disablePacking]) ?>
                                <?= Yii::t('app', 'Pakowanie') ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <?= substr($model->packing_start, 0, strlen($model->packing_start) - 3) . " - " . substr($model->packing_end, 0, strlen($model->packing_end) - 3) ?>
                        </div>
                        <div class="col-md-6">
                                <?= Select2::widget([
                                'data' => \common\models\VehicleModel::getList(),
                                'name' => 'vehicles-packing',
                                'value' => $vm[1],
                                'id' => 'select-user-evet-role-packing',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz typ...'),
                                    'id'=>'select-vehicle-model-packing',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                    </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if ($model->montage_start && $model->montage_end) { 
                        if(count($overlapingEvents['montage'])>0) {
                            $classMontage = 'alert alert-danger';
                            $disableMontage = 'disabled';
                            $checked_montage = false;
                        }else{
                        $classMontage = null;
                        $disableMontage=null;

                        }
                    ?>
                    <div class="row <?= $classMontage ?>">
                        <div class="col-md-2">
                            <label>
                                <?= Html::checkbox('workWholeMontage', $checked_montage, ['value' => 1, 'disabled' => $disableMontage]) ?>
                                <?= Yii::t('app', 'Montaż') ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <?= substr($model->montage_start, 0, strlen($model->montage_start) - 3) . " - " . substr($model->montage_end, 0, strlen($model->montage_end) - 3) ?>
                        </div>
                        <div class="col-md-6">
                                <?= Select2::widget([
                                'data' => \common\models\VehicleModel::getList(),
                                'name' => 'vehicles-montage',
                                'value' => $vm[2],
                                'id' => 'select-user-evet-role-montage',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz typ...'),
                                    'id'=>'select-vehicle-model-montage',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                    </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if ($model->event_start && $model->event_end) { 
                        if(count($overlapingEvents['event'])>0) {
                            $classEvent = 'alert alert-danger';
                            $disableEvent = 'disabled';
                            $checked_event = false;
                        }else{
                        $classEvent = null;
                        $disableEvent=null;

                        }
                    ?>
                    <div class="row <?= $classEvent ?>">
                        <div class="col-md-2">
                            <label>
                                <?= Html::checkbox('workWholeEvent', $checked_event, ['value' => 1, 'disabled' => $disableEvent]) ?>
                                <?= Yii::t('app', 'Event') ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <?= substr($model->event_start, 0, strlen($model->event_start) - 3) . " - " . substr($model->event_end, 0, strlen($model->event_end) - 3) ?>
                        </div>
                        <div class="col-md-6">
                                <?= Select2::widget([
                                'data' => \common\models\VehicleModel::getList(),
                                'name' => 'vehicles-event',
                                'value' => $vm[3],
                                'id' => 'select-user-evet-role-event',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz typ...'),
                                    'id'=>'select-vehicle-model-event',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                    </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if ($model->disassembly_start && $model->disassembly_end) { 
                        if(count($overlapingEvents['disassembly'])>0) {
                            $classDisassembly = 'alert alert-danger';
                            $disableDisassembly= 'disabled';
                            $checked_disassembly = false;
                        }else{
                        $classDisassembly = null;
                        $disableDisassembly=null;

                        }
                    ?>
                    <div class="row <?= $classDisassembly ?>">
                        <div class="col-md-2">
                            <label>
                                <?= Html::checkbox('workWholeDisassembly', $checked_disassembly, ['value' => 1, 'disabled' => $disableDisassembly]) ?>
                                <?= Yii::t('app', 'Demontaż') ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <?= substr($model->disassembly_start, 0, strlen($model->disassembly_start) - 3) . " - " . substr($model->disassembly_end, 0, strlen($model->disassembly_end) - 3) ?>
                        </div>
                        <div class="col-md-6">
                                <?= Select2::widget([
                                'data' => \common\models\VehicleModel::getList(),
                                'name' => 'vehicles-disassembly',
                                'value' => $vm[4],
                                'id' => 'select-user-evet-role-disassembly',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz typ...'),
                                    'id'=>'select-vehicle-model-disassembly',
                                    'multiple' => false,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                    </div>
                    </div>
                    <?php
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
                    <div class="col-md-2">
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
                    <div class="col-md-5">
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

ActiveForm::end();

$this->registerJs('

    $("#vehicle_modal").on("hidden.bs.modal", function () {
        $("body").find("#vehicle_modal").find(".modalContent").html("");
    });
    
    $("#add_working_time_form").submit(function(e){        
        var form = $(this);
    
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
        modal.load("' . Url::to(["event/vehicle-form"]) . '?event_id=' . $model->id . '&vehicle_id=' . $vehicle->id .'");
    }
');