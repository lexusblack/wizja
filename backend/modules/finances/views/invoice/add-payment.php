<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchemaCat */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="tasks-schema-cat-form">

    <?php $form = ActiveForm::begin(['id'=>'AddPaymentForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


    <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kwota')])->label(Yii::t('app', 'Kwota')) ?>
    <label class="control-label"><?= Yii::t('app', 'Data płatności') ?></label>
            <?php
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'date',
                'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);

            ?>

    <?= $form->field($model, 'payment_method')->widget(Select2::classname(), [
                    'data' => \common\helpers\ArrayHelper::map(\common\models\Paymentmethod::find()->asArray()->all(), 'name', 'name'),
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
            ])->label(Yii::t('app', 'Metoda płatności'));?>
    <h5><?=Yii::t('app', 'Dotychczasowe płatności')?></h5>
    <?php 
    foreach ($payments as $p){
        echo "<p>".substr($p->date, 0, 11)." ".Yii::$app->formatter->asCurrency($p->amount)." [".$p->payment_method."]  (".Yii::t('app', '').$p->creator->displayLabel.")"; ?>
        <?php echo Html::a(Html::icon('pencil'), ['history-edit', 'id'=>$p->id], ['class'=>'edit-history'] ); ?>
                            <?php echo Html::a(Html::icon('remove'), ['history-remove', 'id'=>$p->id], ['class'=>'remove-history'] ); ?></p>
        <?php }
        ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#invoicepaymenthistory-amount").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});
    $(".remove-history").on("click", function(e){
        e.preventDefault();
        $el = $(this);
        $.get($el.prop("href"), {}, function(){
            location.reload();
        });
        return false;
    });

        $(".edit-history").on("click", function(e){
        e.preventDefault();
        $("#new-payment").modal("show").find(".modalContent").load($(this).attr("href"));
        return false;
    });
');
 $this->registerJs("
$('#AddPaymentForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            $('.add-payment-'+data.id).html(data.sum);
            $('#new-payment').modal('hide');
            location.reload();
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});"

);   

?>