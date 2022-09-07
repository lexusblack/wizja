<?php
/* @var $payment \common\models\ExpensePaymentHistory */
?>
<div class="row">
    <div class="col-lg-6">
        <?php echo $form->field($model, 'number')->textInput(['maxlength'=>true]); ?>
        <?php echo $form->field($model, 'paymentmethod_id')->dropDownList(\common\models\Invoice::getPaymentmethodList()); ?>

        <fieldset>
            <legend><?= Yii::t('app', 'Zapłacono') ?></legend>
<!--            --><?php //echo $form->field($model, 'alreadypaid')->textInput(); ?>
            <?php echo $form->field($payment, 'amount')->textInput(['id' => 'amount-input']); ?>
            <?php echo $form->field($payment, 'date')->widget(\kartik\datecontrol\DateControl::className(), [
                'type'=>\kartik\datecontrol\DateControl::FORMAT_DATE,
            ]); ?>
            <?php echo $form->field($model, 'paid')->checkbox(); ?>
            <?php echo $form->field($model, 'payment_date')->widget(\kartik\datecontrol\DateControl::className(), [
                    'type'=>\kartik\datecontrol\DateControl::FORMAT_DATE,
            ]); ?>
        </fieldset>
    </div>
    <div class="col-lg-6">
        <?php echo $form->field($model,'date')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <?php echo $form->field($model, 'disposaldate')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <?php echo $form->field($model, 'paymentdate')->widget(\kartik\datecontrol\DateControl::className()); ?>
    </div>
</div>



<?php
$this->registerJs('
    $("#amount-input").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});');
?>