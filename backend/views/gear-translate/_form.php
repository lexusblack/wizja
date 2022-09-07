<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearTranslate */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-translate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

        <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>

    <?php 
       // if ($model->isNewRecord)
    echo $form->field($model, 'language_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Language::getCodesList2(),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz język')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
