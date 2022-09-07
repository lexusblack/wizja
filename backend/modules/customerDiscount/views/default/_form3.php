<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use wbraganca\dynamicform\DynamicFormWidget;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-discount-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>

    <?= $form->field($model, 'discount')->textInput() ?>   

    <div class="clearfix"></div>

    <hr>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Aktualizuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


