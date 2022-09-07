<?php

use common\models\EventBreaks;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use kartik\daterange\DateRangePicker;

$format = <<< SCRIPT
function format(obj) {
	if (!obj.id) return obj.text;
	icon = '<span class="glyphicon glyphicon-'+obj.text+'" aria-hidden="true"></span>';
    return icon;
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);

$user_id = $user->id;
$form = ActiveForm::begin(['type' => ActiveForm::TYPE_INLINE, 'id' => 'add_working_time_form',
    'action' => ['planboard/user-form', 'event_id' => $model->id, 'user_id' => $user_id,
        'update_event_user_data' => 1]]);

?>
    <div id="event_id" style="display: none;"><?= $model->id ?></div>
    <h2><?= $user->last_name . ' ' . $user->first_name ?></h2>
    <div class="row">
        <div class="col-md-12">


            <div class="panel panel-primary">
                <div class="panel-heading"><h4><?php echo Yii::t('app', 'Godziny pracy:'); ?></h4></div>
                <div class="panel-body">

                    <?php
                    foreach ($model->eventSchedules as $schedule)
                    {
                        if ($schedule->start_time)
                        {
                            //sprawdzamy czy ma blisko eventy 
                            $checked = \common\models\EventUserPlannedWrokingTime::find()->where(['user_id'=>$user->id, 'event_schedule_id'=>$schedule->id])->one();
                            $checkbox = false;
                            $roles = [];
                            $class = "";
                            if ($checked)
                            {
                                $checkbox = true;
                                $roles = \common\helpers\ArrayHelper::map(\common\models\EventUserRole::find()->where(['working_hours_id'=>$checked->id])->asArray()->all(), 'user_event_role_id', 'user_event_role_id');
                            }
                            $overlapping = \common\models\EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $model->id])->andWhere(['user_id'=>$user->id])->andWhere(['<', 'start_time', $schedule->end_time])->andWhere(['>', 'end_time', $schedule->start_time])->all();
                            $close = [];
                            if ($overlapping)
                            {
                                $class = 'alert alert-danger';
                            }else{
                                $work_start = new DateTime($schedule->start_time);
                                $work_end = new DateTime($schedule->end_time);
                                $test1 = clone($work_start)->sub(new DateInterval('PT12H'));
                                $test2 = clone($work_end)->add(new DateInterval('PT24H'));
                                $close = \common\models\EventUserPlannedWrokingTime::find()->where(['<>', 'event_id', $model->id])->andWhere(['user_id'=>$user->id])->andWhere(['<', 'start_time', $test2->format('Y-m-d H:i:s')])->andWhere(['>', 'end_time', $test1->format('Y-m-d H:i:s')])->all();
                                if ($close)
                                {
                                    $class = 'alert alert-warning';
                                }
                            }
                            $vacationsAcc = \common\models\Vacation::find()->where(['user_id'=>$user->id])->andWhere(['status'=>\common\models\Vacation::STATUS_ACCEPTED])->andWhere(['<', 'start_date', $schedule->end_time])->andWhere(['>', 'end_date', $schedule->start_time])->all();
                            $vacationsPlanned = [];
                            if ($vacationsAcc)
                            {
                                $class = 'alert alert-danger';
                            }else{
                                $vacationsPlanned = \common\models\Vacation::find()->where(['user_id'=>$user->id])->andWhere(['status'=>\common\models\Vacation::STATUS_NEW])->andWhere(['<', 'start_date', $schedule->end_time])->andWhere(['>', 'end_date', $schedule->start_time])->all();
                                if ($vacationsPlanned)
                                {
                                    $class = 'alert alert-warning';
                                }
                            }

                             ?>
                            <div class="row <?= $class ?>" style="margin-bottom: 0; border-bottom: 0; padding: 0px">
                                <div class="col-md-2">
                                <label>
                                    <?= Html::checkbox('workWhole'.$schedule->id, $checkbox, ['value' => 1]) ?>
                                    <?=  $schedule->name ?>
                                </label>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-6">
                                <?= Select2::widget([
                                'data' => $role_list,
                                'value'=>$roles,
                                'name' => 'roles-'.$schedule->id,
                                'id' => 'select-user-evet-role'.$schedule->id,
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz role...'),
                                    'id'=>'select-user-role'.$schedule->id,
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                        </div>
                        </div>
                        <?php if((count($vacationsAcc)>0 || count($overlapping)>0)){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-danger" style="padding: 0px">
                                    <?php
                                    foreach ($overlapping as $packingEvent) {
                                        $packingEvent = $packingEvent->event;
                                        echo Html::a($packingEvent->getTimeStart(). " - " .$packingEvent->getTimeEnd(). " ".$packingEvent->name, ['event/view', 'id' => $packingEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                    foreach ($vacationsAcc as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if((count($close)>0 || count($vacationsPlanned)>0)){ ?>
                            <div class="row">
                                <div class="col-md-12 alert alert-warning" style="padding: 0px">
                                    <?php
                                    if (isset($close))
                                    foreach ($close as $packingEvent) {
                                        $packingEvent = $packingEvent->event;
                                        echo Html::a($packingEvent->getTimeStart(). " - " .$packingEvent->getTimeEnd(). " ".$packingEvent->name, ['event/view', 'id' => $packingEvent->id], ['target' => '_blank'])."<br>";
                                    }
                                     if (isset($vacationsPlanned))                                   
                                    foreach ($vacationsPlanned as $vacation) {
                                        echo Html::a(Yii::t('app','Urlop'). ": " . $vacation->start_date. " - " .$vacation->end_date, ['vacation/view', 'id' => $vacation->id], ['target' => '_blank'])."<br>";
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
                            echo GridView::widget(['dataProvider' => $userWorkingHoursDataProvider,
                                                    'toolbar' => false,

                                'columns' => ['start_time', 'end_time', ['label'=>Yii::t('app', 'Role'), 'value'=>function($model){ $return =""; foreach ($model->roles as $r){ $return.=$r->userEventRole->name." ";} return $return;}], 
                                    ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',
                                        'buttons' => ['delete' => function ($url, $model) use ($user_id, $event_id) {
                                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['planboard/delete-user-working-hours',
                                                'id' => $model->id, 'user_id' => $user_id,
                                                'event_id' => $event_id]), ['class' => 'delete_working_hours']);

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
                        <div class="col-md-6">
                            <?php
                            $start = $model->event_start;
                            $end = $model->event_end;
                            
                            echo DateRangePicker::widget(
                                    [
                                        'name' => 'eventRange',
                                        'convertFormat' => true,
                                        'pluginOptions' => [
                                            'opens'=> 'left',
                                            'drops'=>'up',
                                            'timePicker' => true,
                                            'timePickerIncrement' => 5,
                                            'timePicker24Hour' => true,
                                            'linkedCalendars'=>false,
                                            'locale' => [
                                                'format' => 'Y-m-d H:i'
                                            ],
                                            'minDate' => $start,
                                            'maxDate' => $end,
                                            'startDate' => $start,
                                            'endDate' => $end
                                        ],
                                        'options' => [
                                            'id' => 'working-hours-daterange',
                                            'style' => 'width: 300px;',
                                            'autocomplete'=>'off'
                                        ],
                                    ]
                            ) ?>
                        </div>
                            <div class="col-md-4">
                                <?= Select2::widget([
                                'data' => $role_list,
                                'name' => 'roles-additional',
                                'id' => 'select-user-evet-role-additional',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Wybierz role...'),
                                    'id'=>'select-user-role-additional',
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                        'allowClear' => true,
                                ],
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-success',
                            'id' => 'btn-add-workin-hours']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= Html::button(Yii::t('app', 'Zamknij'), ['class' => 'btn btn-primary', 'id' => 'close-modal-btn']) ?>
            </div>
            </div>
            </div>

<?php ActiveForm::end(); ?>
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
        });');
if ($inevent)
{
$this->registerJs('

$("#ekipa_modal").on("hidden.bs.modal", function () {
    var event_id = $("#event_id").html();
    $("body").find("#ekipa_modal").find(".modalContent").html("");
});

$("#close-modal-btn").click(function(){
     $("body").find("#ekipa_modal").modal("hide");
     //location.reload();
});

$("#add_working_time_form").on("beforeSubmit", function(e) {
    CrewChanged = true;
    $.post($(this).attr("action"), $(this).serialize(), function(resp){
            if (resp.error) {
                alert(resp.error);
            }
        })
        .done(function(result) {
            reloadModal();
        })
        .fail(function() {
            alert("Error.");
        }
    );
}).on("submit", function(e){
    e.preventDefault();
});

$(".delete_working_hours").click(function(e){
    e.preventDefault();

    var url = $(this).attr("href");
    if (confirm("Czy na pewno usunąć te godziny pracy?")) {
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


function reloadModal() {
    var modal = $("body").find("#ekipa_modal .modalContent");
    modal.html("");
    modal.load("' . Url::to(["planboard/user-form"]) . '?event_id=' . $model->id . '&user_id=' . $user_id . '&in_event=1");
}


');
}else{
  $this->registerJs('


$("#ekipa_modal").on("hidden.bs.modal", function () {
    var event_id = $("#event_id").html();
    $("body").find("#ekipa_modal").find(".modalContent").html("");
});

$("#close-modal-btn").click(function(){
     $("body").find("#ekipa_modal").modal("hide");
});

$("#add_working_time_form").on("beforeSubmit", function(e) {
    $.post($(this).attr("action"), $(this).serialize(), function(resp){
            if (resp.error) {
                alert(resp.error);
            }
        })
        .done(function(result) {
            reloadModal();
        })
        .fail(function() {
            alert("Error.");
        }
    );
}).on("submit", function(e){
    e.preventDefault();
});

$(".delete_working_hours").click(function(e){
    e.preventDefault();

    var url = $(this).attr("href");
    if (confirm("Czy na pewno usunąć te godziny pracy?")) {
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


function reloadModal() {
    $("#calendar").fullCalendar("refetchEvents");
    var modal = $("body").find("#ekipa_modal .modalContent");
    modal.html("");
    modal.load("' . Url::to(["planboard/user-form"]) . '?event_id=' . $model->id . '&user_id=' . $user_id . '");
}


', View::POS_READY);  
}



$this->registerCss('.select2-dropdown { z-index: 5050; }');