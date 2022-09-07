<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearCategoryTranslate */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-category-translate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

        <?php 
            if ($model->isNewRecord)
        echo $form->field($model, 'gear_category_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => \common\models\GearCategory::getMainList(),
            'options' => ['placeholder' =>  Yii::t('app', 'Wybierz kategorię')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Tłumaczenie')]) ?>

    <?php 
            if ($model->isNewRecord)
        echo $form->field($model, 'language_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => \common\models\Language::getCodesList2(),
            'options' => ['placeholder' =>  Yii::t('app', 'Wybierz język')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
