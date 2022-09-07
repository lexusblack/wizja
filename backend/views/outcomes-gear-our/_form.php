<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OutcomesGearOur */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outcomes-gear-our-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'outcome_id')->textInput() ?>

    <?= $form->field($model, 'gear_id')->textInput() ?>

    <?= $form->field($model, 'return_datetime')->textInput() ?>

    <?= $form->field($model, 'return_user')->textInput() ?>

    <?= $form->field($model, 'return_comments')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Stwórz') : Yii::t('app', 'Aktualizuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
