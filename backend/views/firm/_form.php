<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Firm */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="firm-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>
    <div class="row">
    <div class="col-lg-6">

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Adres')]) ?>

    <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kod pocztowy')]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Miejscowość')]) ?>


    <?= $form->field($model, 'nip')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'NIP')]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Telefon')]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Email')]) ?>
    </div>
    <div class="col-lg-6">

    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa banku')]) ?>

    <?= $form->field($model, 'bank_number')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Numer konta')]) ?>

    <h5><?=Yii::t('app', 'Magazyn')?></h5>

    <?= $form->field($model, 'warehouse_adress')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Adres')]) ?>

    <?= $form->field($model, 'warehouse_zip')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kod pocztowy')]) ?>

    <?= $form->field($model, 'warehouse_city')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Miejscowość')]) ?>

    <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'logo'); ?>
                <?php echo Html::activeLabel($model, 'logo'); ?>
                <?php
                    echo devgroup\dropzone\DropZone::widget([
                        'url'=>\common\helpers\Url::to(['upload']),
                        'name'=>'file',
                        'options'=>[
                            'maxFiles' => 1,
                        ],
                        'eventHandlers' => [
                            'success' => 'function(file, response) {
                            $("#firm-logo").val(response.filename);
                        }',
                        ]
                    ]);
                echo Html::error($model, 'companyLogo'); ?>
            </div>
    </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
