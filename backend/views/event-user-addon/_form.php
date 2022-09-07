<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventUserAddon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-user-addon-form">
<div class="alert alert-danger">
                     <?=Yii::t('app', 'Zwróć uwagę, aby daty pokrywały się z czasem trwania wydarzenia!') ?>
</div>
    <?php $form = ActiveForm::begin(); ?>

     <?php if (!$model->user_id){
        echo $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $model->event->getUserList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Pracownik'));
            
    } ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?php echo $form->field($model, 'dateRange')->widget(\common\widgets\DateRangeField::className()); ?>

    <?php
    echo $form->field($model, 'amount')->widget(\yii\widgets\MaskedInput::className(), [
        'clientOptions'=> [
            'alias'=>'decimal',
            'rightAlign'=>false,
            'digits'=>2,
        ]
    ]);
    ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
