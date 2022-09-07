<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-service-statut-form">

    <?php $form = ActiveForm::begin(['id'=>'comment-form']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true, 'placeholder' =>Yii::t('app', 'Komentarz')])->label(Yii::t('app', 'Komentarz')) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'add-comment-form-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php


$this->registerJs('
    $("#comment-form").submit(function(e){
        e.preventDefault();
    });


    $("#comment-form").on("beforeSubmit", function(e){
        $("#add-comment-form-submit").attr("disabled", true);
        $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                async: false,
                success: function(data){
                    if (data.ok)
                    {
                        $("#comment'.$model->id.'").empty().append(data.output);
                        $("#comment_modal").modal("hide");
                        $("#comment_modal").find(".modalContent").empty();

                    }else{
                        alert(data.error);
                        $("#add-comment-form-submit").attr("disabled", false);
                    }
                    
                }    
            });
        return false;
    });

');
?>