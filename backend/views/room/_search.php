<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RoomSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-room-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'podkowa')->textInput(['placeholder' =>  Yii::t('app', 'Podkowa')]) ?>

    <?= $form->field($model, 'bankiet')->textInput(['placeholder' =>  Yii::t('app', 'Bankiet')]) ?>

    <?= $form->field($model, 'teatr')->textInput(['placeholder' =>  Yii::t('app', 'Teatr')]) ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton( Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
