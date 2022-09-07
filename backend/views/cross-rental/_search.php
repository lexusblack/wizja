<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CrossRentalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-cross-rental-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'owner')->textInput(['maxlength' => true, 'placeholder' => 'Owner']) ?>

    <?= $form->field($model, 'gear_model_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\GearModel::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => 'Choose Gear model'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'owner_name')->textInput(['maxlength' => true, 'placeholder' => 'Owner Name']) ?>

    <?= $form->field($model, 'owner_city')->textInput(['maxlength' => true, 'placeholder' => 'Owner City']) ?>

    <?php /* echo $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder' => 'Price']) */ ?>

    <?php /* echo $form->field($model, 'owner_gear_id')->textInput(['placeholder' => 'Owner Gear']) */ ?>

    <?php /* echo $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
