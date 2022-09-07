<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RoomPhoto */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="room-photo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <?= $form->field($model, 'room_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Nazwa pliku')]) ?>

    <?= $form->field($model, 'extension')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Rozszerzenie')]) ?>

    <?= $form->field($model, 'status')->textInput(['placeholder' =>  Yii::t('app', 'Status')]) ?>

    <?= $form->field($model, 'mime_type')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Typ Mime')]) ?>

    <?= $form->field($model, 'base_name')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Nazwa podstawowa')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?  Yii::t('app', 'Zapisz') :  Yii::t('app', 'Edytuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
