<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HallAudienceType */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="hall-audience-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

                <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#hallaudiencetype-photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
