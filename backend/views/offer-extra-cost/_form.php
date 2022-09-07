<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OfferExtraCost */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="offer-extra-cost-form">

    <?php $form = ActiveForm::begin(['id'=>'offer-extra-cost-form',
        'enableAjaxValidation' => false,
        'enableClientScript' => false,]); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'cost')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Koszt jednostkowy')]) ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Ilość')]) ?>

    <?php echo $form->field($model, 'section')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\EventExpense::getSectionList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
$('#offer-extra-cost-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            $('#add-cost').find('.modalContent').empty();
            $('#add-cost').modal('hide');
            location.reload();
        },
        error: function () {
            alert('Something went wrong');
        }
    });
    return false;
});");