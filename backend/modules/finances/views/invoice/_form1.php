<?php
/* @var $this \yii\web\View; */
/* @var $model \common\models\Invoice */
/* @var $form \kartik\form\ActiveForm */
/* @var $payment \common\models\InvoicePaymentHistory */

\kartik\daterange\MomentAsset::register($this);
use kartik\widgets\DatePicker;
use kartik\select2\Select2;

use common\models\Invoice;
use yii\bootstrap\Html;
?>

<div class="row">
    <div class="col-lg-6">
        <?php echo $form->field($model, 'paymentmethod_id')->dropDownList(\common\models\Invoice::getPaymentmethodList()); ?>
        <?php echo Html::a(Html::icon('plus'), ['invoice-serie/create', 'type'=>$model->type], ['class'=>'btn btn-xs btn-default pull-right']); ?>
        <?php echo $form->field($model, 'series_id')->dropDownList(\common\models\InvoiceSerie::getListForType($model->type), ['prompt'=>Yii::t('app', 'Domyślna'), 'style'=>'width:80%']); ?>
        <?php

        if (in_array($model->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS])) {
            echo $form->field($model, 'correction_explanation')->textarea();
            echo $form->field($payment, 'amount')->hiddenInput()->label(false);
            echo $form->field($payment, 'date')->hiddenInput()->label(false);

        }
        else {
            if (!$model->alreadypaid)
                $model->alreadypaid = 0;
            echo "<p><strong>".Yii::t('app', 'Zapłacono').":</strong> ".Yii::$app->formatter->asCurrency($model->alreadypaid, $model->currency);
            echo Html::a(Html::icon('plus').Yii::t('app', 'Dodaj płatność'), ['#'], ['class'=>'btn btn-xs btn-primary pull-right add-payment']);
            echo "</p>";
            echo "<div class='payments' style='display:none'>"; ?>
        <?= $form->field($payment, 'amount')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kwota')])->label(Yii::t('app', 'Kwota')) ?>
        <label class="control-label"><?= Yii::t('app', 'Data płatności') ?></label>
            <?php
            $payment->date = date("Y-m-d");
            echo DatePicker::widget([
                'model' => $payment,
                'attribute' => 'date',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?>

    <?= $form->field($payment, 'payment_method')->widget(Select2::classname(), [
                    'data' => \common\helpers\ArrayHelper::map(\common\models\Paymentmethod::find()->asArray()->all(), 'name', 'name'),
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ])->label(Yii::t('app', 'Metoda płatności'));
            echo "</div>";
        }
        ?>

                <div class="row">
                    <div class="col-lg-6">
                        <?php // echo $form->field($model, 'paid')->checkbox(); ?>
                    </div>
                    <div class="col-lg-6">
                        <div id="paid-date">
                            <?php echo $form->field($model, 'payment_date')->widget(\kartik\datecontrol\DateControl::className())->label(false); ?>
                        </div>
                    </div>
                </div>
    </div>
    <div class="col-lg-6">
        <?php echo $form->field($model,'date')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <?php echo $form->field($model, 'disposaldate')->widget(\kartik\datecontrol\DateControl::className()); ?>
        <div class="row">
            <div class="col-lg-10">
                <?php echo $form->field($model, 'paymentdate')->widget(\kartik\datecontrol\DateControl::className()); ?>
            </div>
            <div class="col-lg-2">

                <?php echo $form->field($model, 'paymentDatePeriod')->dropDownList(\common\models\Invoice::paymentDatePeriodList())->label('&nbsp;'); ?>
            </div>
        </div>


    </div>
</div>
<?php
$this->registerJs('
$("#amount-input").on("input", function(e)
{
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});

');
$this->registerJs('

$(".add-payment").click(function(e){
    e.preventDefault();
    var bruttoSum = 0;
    bruttoSum -= '.$model->alreadypaid.';
    
    $(".item-invoice").each(function(i,element){
        var el = $(element);
        var discount = el.find(".item-discount").val();
        var price = el.find(".item-price").val();
        var count = el.find(".item-count").val();
        var value = price * count;
        var netto = value - (value *(discount/100));
        var vat = el.find(".item-vat").val()/100;
        var tax = netto * vat;
        var brutto = netto * (1 + vat);
        bruttoSum += brutto;
    });
    $("#invoicepaymenthistory-amount").val(bruttoSum.toFixed(2));
    $(".payments").show();

});

paid();

$("#invoice-paid").on("change", paid);

function paid()
{
    var paid = $("#invoice-paid").prop("checked");
    var d = $("#paid-date");
    if (paid)
    {
        d.show();
    }
    else
    {
        d.find("input").val("");
        d.hide();
        
    }
}
$("#invoice-paymentdateperiod, #invoice-date").on("change", function(){
   datePeriod();
});
function datePeriod()
{
     var d = $("#invoice-date").val();
    var m = moment(d, "YYYY-MM-DD");
    var days = $("#invoice-paymentdateperiod").val();
    var newDate = m.add(days, "d").format("DD.MM.YYYY");
//    console.log(d, m, days, newDate);
    $("#invoice-paymentdate-disp").val(newDate).trigger("change");
}
');