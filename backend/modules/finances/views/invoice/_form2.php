<?php
/* @var $this \yii\web\View; */

use backend\models\SettingsForm;
use kartik\widgets\Select2;

/* @var $model \common\models\Invoice */
/* @var $form \kartik\form\ActiveForm */
?>

<div class="row">
    <div class="col-lg-6">
        <?php echo $form->field($model, 'currency')->widget(Select2::className(), [
	        'value'=> $model->currency,
	        'data' => $model->getCurrencyList(),
	        'pluginOptions' => [
		        'placeholder' => 'Wpisz walutę',
		        'tags' => true,
		        'allowClear' => true,
	        ],
        ]); ?>
        <?php echo $form->field($model, 'bank_name')->textInput(); ?>
        <?php echo $form->field($model, 'bank_account')->textInput(); ?>
    </div>
    <div class="col-lg-6">
        <?php echo $form->field($model, 'language_id')->dropDownList(\backend\modules\finances\Module::getLanguageList()); ?>
        <?php echo $form->field($model, 'template')->textInput(); ?>
        <?php echo $form->field($model, 'auto_send')->checkbox(); ?>

    </div>
</div>
