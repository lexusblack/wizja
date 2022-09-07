<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-item-form">

    <?php $form = ActiveForm::begin(['id'=>'edit-item-form']); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'tester')->textInput() ?>

            <?= $form->field($model, 'lamp_hours')->textInput() ?>

            <?= $form->field($model, 'info')->textInput() ?>

        </div>
    </div>






    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
    $("#edit-item-form").on("beforeSubmit", function(e){
        $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                async: false,
                success: function(data){
                    toastr.success("'.Yii::t('app', 'Zapisano!').'");
                    $("#gear-item-modal").modal("hide");
                    $("#tester"+data.id).html(data.tester);
                    $("#lamp"+data.id).html(data.lamp_hours);
                    $("#info"+data.id).html(data.info);
                }    
            });
        return false;
    });
        $("#edit-item-form").submit(function(e){
        e.preventDefault();
    });
');