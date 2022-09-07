<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-service-statut-form">

    <?php $form = ActiveForm::begin(['id'=>'packlist-add-form']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'start_time')->textInput(['style' => 'display:none'])->label(false); ?>
    <?= $form->field($model, 'end_time')->textInput(['style' => 'display:none'])->label(false); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' =>Yii::t('app', 'Nazwa')])->label(Yii::t('app', 'Nazwa')) ?>

    <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor'));
                ?>
    <label class="control-label"><?=Yii::t('app', 'Czas rezerwacji sprzętu')?></label>
    <div class="row">
        <div class="col-xs-6">
        <?=$model->getScheduleDiv()?>
        </div>
        <div class="col-xs-6">
        <input type="text" id="js-range-slider-packlist" data-start="<?=substr($model->start_time, 0, 16)?>" data-end="<?=substr($model->end_time, 0, 16)?>" name="range" value="0;10"/>
        </div>
    </div>
    
    <?php echo $form->field($model, 'info')->textarea(['rows'=>5])->label(Yii::t('app', 'Opis')); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'add-packlist-form-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    var tvalues = [];
        tvalues = [<?php $date = new DateTime($model->event->event_start); 
        while($date->format('Y-m-d H:i')<$model->event->event_end){ echo "'".$date->format('Y-m-d H:i')."', "; $date->add(new DateInterval('PT30M'));} echo "'".substr($model->event->event_end, 0, 16)."'"; ?> ];
</script>
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
    $("#packlist-add-form").submit(function(e){
        e.preventDefault();
    });

    $(".schedule-checkbox-packlist").click(function(e){
        start = "'.substr($model->event->event_end, 0, 16).'";
        end = "'.substr($model->event->event_start, 0, 16).'";
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
            $("#packlist-start_time").val(start);
            $("#packlist-end_time").val(end);
            }else
            {
            $("#js-range-slider-packlist").ionRangeSlider("update", {
                from: tvalues.indexOf(end),
                to: tvalues.indexOf(start)
                });
            $("#packlist-start_time").val(end);
            $("#packlist-end_time").val(start);
            }


    });

    $("#packlist-add-form").on("beforeSubmit", function(e){
        $("#add-packlist-form-submit").attr("disabled", true);
        $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                async: false,
                success: function(data){
                    if (data.ok)
                    {
                        $("#packlist_modal").modal("hide");
                        $("#packlist_modal").empty();
                        //$("#tab-gear").empty();
                        $("#tab-gear").load("'.Url::to(['event/gear-tab', 'id'=>$model->event_id]).'");
                    }else{
                        alert(data.error);
                        $("#add-packlist-form-submit").attr("disabled", false);
                    }
                    
                }    
            });
        return false;
    });

        $("#js-range-slider-packlist").ionRangeSlider({
                type: "double",
                min:0,
                max: tvalues.length,
                from: tvalues.indexOf($("#js-range-slider-packlist").data("start")),
                to: tvalues.indexOf($("#js-range-slider-packlist").data("end")),
                values: tvalues,
                onFinish: function (data) {
                                $("#packlist-start_time").val(data.fromValue);
                                $("#packlist-end_time").val(data.toValue);
                },
            });
');
?>