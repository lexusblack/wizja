<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesGearOur */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incomes-gear-our-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'income_id')->textInput() ?>

    <?= $form->field($model, 'gear_id')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
