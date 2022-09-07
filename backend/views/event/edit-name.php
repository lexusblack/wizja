<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="event-form">
    <?php $form = ActiveForm::begin([
        'id' => 'event-form',
        'enableAjaxValidation' => false,
        'enableClientScript' => false,
    ]); ?>
    <?php
        echo $form->errorSummary($model);
    ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete'=>"off"]) ?>
            </div>
            </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
            </div>
        </div>
    </div>



    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("
$('#event-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            changeRow(data);
            $('#edit-name').find('.modalContent').empty();
            $('#edit-name').modal('hide');
            $('#add-task').find('.modalContent').empty();
            $('#add-task').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});");