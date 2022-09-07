<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOurSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outcomes-gear-our-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'outcome_id') ?>

    <?= $form->field($model, 'gear_id') ?>

    <?= $form->field($model, 'return_datetime') ?>

    <?= $form->field($model, 'return_user') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
