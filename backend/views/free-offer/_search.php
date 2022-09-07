<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FreeOfferSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-free-offer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']) ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Start Time',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose End Time',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'company')->textInput(['maxlength' => true, 'placeholder' => 'Company']) ?>

    <?php /* echo $form->field($model, 'desctiption')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'city_id')->textInput(['placeholder' => 'City']) */ ?>

    <?php /* echo $form->field($model, 'work_info')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'transport_info')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'money_info')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'deal_type')->textInput(['placeholder' => 'Deal Type']) */ ?>

    <?php /* echo $form->field($model, 'skills')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'devices')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'own_device')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'user_id')->textInput(['placeholder' => 'User']) */ ?>

    <?php /* echo $form->field($model, 'user_mail')->textInput(['maxlength' => true, 'placeholder' => 'User Mail']) */ ?>

    <?php /* echo $form->field($model, 'company_name')->textInput(['maxlength' => true, 'placeholder' => 'Company Name']) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
