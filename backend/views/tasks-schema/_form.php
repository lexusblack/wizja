<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchema */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasks-schema-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?php echo $form->field($model, 'default')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>

            <?php echo $form->field($model, 'type')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TasksSchema::getSchemaTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

