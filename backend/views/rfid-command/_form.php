<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RfidCommand */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="rfid-command-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'reader')->textInput(['maxlength' => true, 'placeholder' => 'Reader']) ?>

    <?= $form->field($model, 'command')->textInput(['maxlength' => true, 'placeholder' => 'Command']) ?>

    <?= $form->field($model, 'content')->textInput(['maxlength' => true, 'placeholder' => 'Content']) ?>

    <?= $form->field($model, 'status')->textInput(['placeholder' => 'Status']) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
