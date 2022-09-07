<div class="row">
    <div class="col-lg-6">
        <?php echo $form->field($model, 'paymentmethod')->dropDownList(\common\models\Invoice::getPaymentmethodList()); ?>

        <fieldset>
            <legend><?= Yii::t('app', 'Zapłacono') ?></legend>
            <?php echo $form->field($model, 'alreadypaid_initial')->textInput(); ?>
            <?php echo $form->field($model, 'paid')->checkbox(); ?>
            <?php echo $form->field($model, 'payment_date')->widget(\kartik\datecontrol\DateControl::className()); ?>
        </fieldset>
    </div>
    <div class="col-lg-6">
        <?php echo $form->field($model, 'currency')->dropDownList(\backend\modules\finances\Module::getCurrencyList()); ?>
        <?php echo $form->field($model,'date')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <?php echo $form->field($model, 'disposaldate')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <?php echo $form->field($model, 'paymentdate')->widget(\kartik\datecontrol\DateControl::className()); ?>
    </div>
</div>

