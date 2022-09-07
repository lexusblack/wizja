<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Contact */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <?php
    if ($model->getPhotoUrl())
    {
        echo Html::img($model->getPhotoUrl(), ['style'=>'width:200px', 'class'=>'thumbnail']);
    }
    ?>
    <?php echo Html::activeHiddenInput($model, 'photo'); ?>

    <?php echo devgroup\dropzone\DropZone::widget([
        'url'=>\common\helpers\Url::to(['upload']),
        'name'=>'file',
//        'model'=>$model,
//        'attribute'=>'logo',
        'options'=>[
            'maxFiles' => 1,
        ],
        'eventHandlers' => [
            'success' => 'function(file, response) {
               $("#contact-photo").val(response.filename);

            }',
        ]
    ]); ?>

    <?= $form->field($model, 'customer_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Customer::getList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
