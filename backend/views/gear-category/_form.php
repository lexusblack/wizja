<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'parent_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\GearCategory::getModelList(),
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
