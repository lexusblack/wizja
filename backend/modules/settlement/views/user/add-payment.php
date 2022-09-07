<?php

use yii\helpers\Html;
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
                'attribute' => 'datetime',
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
    <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Opis')])->label(Yii::t('app', 'Uwagi')) ?>
    <h5><?=Yii::t('app', 'Dotychczasowe płatności')?></h5>
    <?php 
    foreach ($payments as $p){
        echo "<p>".substr($p->datetime, 0, 11)." ".Yii::$app->formatter->asCurrency($p->amount)." [".$p->payment_method."] (".Yii::t('app', '').$p->creator->displayLabel.") <br/>".$p->description." ".Html::a("<i class='fa fa-pencil'></i>", ['add-payment', 'id'=>$p->id, 'user_id'=>$p->user_id, 'month'=>$p->month, 'year'=>$p->year], ['class'=>'edit-payment'])." ".Html::a("<i class='fa fa-trash'></i>", ['delete-payment', 'id'=>$p->id], ['class'=>'delete-payment'])."</p>";
        }
        ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#userpayment-amount").change(function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});');
 $this->registerJs("


$('#AddPaymentForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            $('.add-payment-'+data.user_id).html(data.sum);
            $('#new-payment').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
}); 

 $('.delete-payment').click(function(e)
 {
    e.preventDefault();
    var t = $(this);
    $.ajax({
        url: $(this).attr('href'),
        data: [],
        success: function (data) {
            t.parent().hide();

            $('.add-payment-'+data.user_id).html(data.sum);
        },
        error: function () {
            alert('Something went wrong');
        }
    });
 });

 $('.edit-payment').click(function(e)
 {
    e.preventDefault();
    $('#new-payment').modal('show').find('.modalContent').load($(this).attr('href'));
});
 ");  

?>