<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventExtraItem */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="event-extra-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nazwa']) ?>
    <?php 
    if ($model->isNewRecord){ ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Ilość']) ?>
    <?php } ?>

    <?= $form->field($model, 'gear_category_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\GearCategory::getMainList(),
        'options' => ['placeholder' => 'Wybierz sekcję'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
<?php
            echo $form->field($model, 'weight')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
<?php
            echo $form->field($model, 'volume')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
