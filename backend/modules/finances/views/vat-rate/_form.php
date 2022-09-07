<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VatRate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vat-rate-form panel panel-default">
<div class="panel-body">
    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'value')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
</div>
