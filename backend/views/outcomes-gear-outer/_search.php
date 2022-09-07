<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOuterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outcomes-gear-outer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'outcome_id') ?>

    <?= $form->field($model, 'outer_gear_id') ?>

    <?= $form->field($model, 'gear_quantity') ?>

    <?= $form->field($model, 'return_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
