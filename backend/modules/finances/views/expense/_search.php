<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExpenseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expense-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'unit') ?>

    <?= $form->field($model, 'netto') ?>

    <?php // echo $form->field($model, 'brutto') ?>

    <?php // echo $form->field($model, 'lumpcode') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'classification') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'documents') ?>

    <?php // echo $form->field($model, 'tags') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <?php // echo $form->field($model, 'count') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
