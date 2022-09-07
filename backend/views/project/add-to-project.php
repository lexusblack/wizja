<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="project-form">

    <?php $form = ActiveForm::begin([
        'id' => 'event-form',
        'enableAjaxValidation' => false,
        'enableClientScript' => false,
    ]); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?php
            echo $form->field($model, 'event_ids')->widget(\kartik\widgets\Select2::className(), [
                'data' => $events,
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Wybierz eventy do dodania'));
            ?>
            

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
            $('#add-to-pr').modal('hide');
            location.reload();
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});");