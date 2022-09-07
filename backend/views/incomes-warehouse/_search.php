<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesWarehouseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incomes-warehouse-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'outcome_id') ?>

    <?= $form->field($model, 'user') ?>

    <?= $form->field($model, 'datetime') ?>

    <?= $form->field($model, 'comments') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
