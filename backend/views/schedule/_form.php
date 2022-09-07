<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Schedule */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>
    <?= $form->field($model, 'prefix')->textInput(['maxlength' => 3, 'placeholder' => Yii::t('app', 'Prefix')]) ?>
    <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor'));
                ?>
    <?php echo $form->field($model,'is_required')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'book_gears')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
