<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="warehouse-form">

    <?php $form = ActiveForm::begin(['id'=>'edit-location-form']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Miejsce w magazynie'),])->label(Yii::t('app', 'Miejsce w magazynie')) ?>


    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Zapisz'), ['edit-location', 'id'=>$model->gear_id, 'w'=>$model->warehouse_id], ['class'=>'btn btn-primary save-form'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
    $this->registerJs('
    $(".save-form").click(function(e){
        e.preventDefault();
        $(this).attr("disabled", "disabled");
        //pobieramy id
        data = $("#edit-location-form").serialize();
        $.post($(this).attr("href"), data, function(response){
            $("#location-div'.$model->gear_id.'").html($("#warehousequantity-location").val());
            $("#gear-location").modal("hide");

        });

    });
        ');

?>
