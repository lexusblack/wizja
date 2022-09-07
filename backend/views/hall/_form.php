<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Hall */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="hall-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'area')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Powierzchnia')]) ?>

    <?= $form->field($model, 'width')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Szerokość')]) ?>

    <?= $form->field($model, 'length')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Długość')]) ?>

    <?= $form->field($model, 'height')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Wysokość')]) ?>

                <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'main_photo'); ?>
                <?php echo Html::activeLabel($model, 'main_photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#hall-main_photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'main_photo'); ?>
            </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
