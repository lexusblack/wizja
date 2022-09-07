<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Contact */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-form">

    <?php $form = ActiveForm::begin(["id"=> "EventOfferRole"]); ?>
    <?php if ($model->isNewRecord){ ?>
    <?php echo $form->field($model, 'user_role_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => $roles,
                
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])
                ->label(Yii::t('app', 'Rola'));
            ?>
    <?php } ?>
    <?= $form->field($model, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(Yii::t('app', 'Liczba')) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
$('#EventOfferRole').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            var modal = $('#ekipa_modal');
            modal.modal('hide');
            $('#tab-crew').empty();
        $('#tab-crew').load('".Url::to(["event/crew-tab", 'id'=>$model->event_id])."');
 
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");
?>
