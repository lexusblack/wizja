<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearModelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-gear-model-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'brightness')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Jasność')]) ?>

    <?= $form->field($model, 'power_consumption')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Pobór prądu')]) ?>

    <?= $form->field($model, 'type')->textInput(['placeholder' => Yii::t('app', 'Typ')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
