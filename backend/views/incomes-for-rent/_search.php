<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesForRentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incomes-for-rent-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'income_id') ?>

    <?= $form->field($model, 'rent_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
