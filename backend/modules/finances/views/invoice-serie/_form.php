<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\InvoiceSerie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-serie-form panel panel-default">
    <div class="panel-body">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(\common\models\Invoice::getTypeList()) ?>

    <?= $form->field($model, 'pattern')->textInput(['maxlength' => true]) ?>
    <p class="text-muted">
        <?= Yii::t('app', 'Dostępne znaczniki') ?>:<br />
        [<?= Yii::t('app', 'numer') ?>] - <?= Yii::t('app', 'generowany podczas wystawiania dokumentu') ?><br />
        [<?= Yii::t('app', 'dzień') ?>] - <?= Yii::t('app', 'dzień z daty wystawienia') ?><br />
        [<?= Yii::t('app', 'miesiąc') ?>] - <?= Yii::t('app', 'miesiąc z daty wystawienia') ?><br />
        [<?= Yii::t('app', 'rok') ?>] - <?= Yii::t('app', 'rok z daty wystawienia (czterocyfrowy) np. 2014') ?><br />
        [<?= Yii::t('app', 'dzień_roku') ?>] - <?= Yii::t('app', 'dzień roku z daty wystawienia') ?><br />
        [<?= Yii::t('app', 'rok:format_dwucyfrowy') ?>] - <?= Yii::t('app', 'rok z daty wystawienia (dwucyfrowy) np. 14') ?>
    </p>

    <?= $form->field($model, 'start_number')->textInput() ?>

    <?= $form->field($model, 'reset_number_period')->dropDownList(\common\models\InvoiceSerie::resetNumberPeriodList()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>