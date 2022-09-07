<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AddonRate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="addon-rate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'amount')->widget(\yii\widgets\MaskedInput::className(), [
        'clientOptions'=> [
            'alias'=>'decimal',
            'rightAlign'=>false,
            'digits'=>2,
        ]
    ]);
    ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength'=>true]); ?>

    <?php echo $form->field($model, 'level')->dropDownList(\common\models\Event::getLevelList()); ?>

    <?php echo $form->field($model, 'period')->dropDownList(\common\models\AddonRate::getPeriodList()); ?>

	<?php echo $form->field($model, 'roleIds')->widget(\kartik\widgets\Select2::className(), [
		'data' => \common\models\UserEventRole::getModelList(),
		'options' => [
			'placeholder' => Yii::t('app', 'Wybierz...'),
		],
		'pluginOptions' => [
			'allowClear' => true,
			'multiple' => true,
		],
	]);
	?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
