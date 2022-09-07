<?php

use kartik\widgets\Select2;
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
$user = Yii::$app->user;

/** @var \backend\models\SettingsForm $model */


if ($user->can('financesInvoiceSeries')) {
    echo Html::a(Html::icon('file') . ' ' . Yii::t('app', 'Serie faktur'), ['/finances/invoice-serie/index'], ['class' => 'btn btn-default']);
}
if ($user->can('financesPaymentMethods')) {
    echo Html::a(Html::icon('piggy-bank') . ' ' . Yii::t('app', 'Metody płatności'), ['/finances/paymentmethod/index'], ['class' => 'btn btn-default']);
}
if ($user->can('financesVatRate')) {
    echo Html::a(Html::icon('scale') . ' ' . Yii::t('app', 'Stawki VAT'), ['/finances/vat-rate/index'], ['class' => 'btn btn-default']);
}
    echo Html::a(Html::icon('scale') . ' ' . Yii::t('app', 'Grupy cenowe'), ['/price-group/index'], ['class' => 'btn btn-default']);
?>

<div class="panel panel-default">
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'defaultCurrency')->dropDownList(\backend\modules\finances\Module::getCurrencyList()); ?>
	    <?= $form->field($model, 'defaultInvoiceCurrency')->widget(Select2::className(), [
		    'value'=> $model->defaultInvoiceCurrency,
		    'data' => $model->getCurrencyList(),
		    'pluginOptions' => [
			    'placeholder' => 'Wpisz walutę',
			    'tags' => true,
			    'allowClear' => true,
		    ],
	    ]); ?>

        <?php if (Yii::$app->user->can('settingsFinancesSave')) { ?>
        <div class="">
            <?php echo Html::submitButton(Html::icon('ok'), ['class'=>'btn btn-success']); ?>
        </div>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>