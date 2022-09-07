<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventExpense */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-expense-form">

    <?php $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_HORIZONTAL
    ]); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?php echo $form->field($model, 'sections')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\EventExpense::getSectionList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kwota netto')]) ?>
            <?= $form->field($model, 'amount_customer')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kwota dla klienta')]) ?>
            <?= $form->field($model, 'profit')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Zysk')]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'invoice_nr')->textInput(['maxlength' => true]) ?>

            <?php //echo $form->field($model, 'customer_id')->dropDownList(\common\models\Customer::getList(), ['prompt'=>'Wybierz...']) ?>
            <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), [
                    'supplier' => 1,
            ]);//->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>

            <?= $form->field($model, 'status')->dropDownList(\common\models\EventExpense::getStatusList()) ?>

            <?= $form->field($model, 'info')->textarea() ?>

        </div>
    </div>






    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#eventexpense-amount").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});
    $("#eventexpense-amount_customer").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});
    $("#eventexpense-profit").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});
');

?>

