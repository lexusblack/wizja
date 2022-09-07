<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="task-schema-form">

    <?php $form = ActiveForm::begin(['id'=>'ChangeStatusForm']); ?>

    <?= $form->errorSummary($model); ?>

     <?php echo $form->field($model, 'event_additional_statut_name_id')->dropDownList($s->getStatusList(1))->label($s->name); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

 $this->registerJs("
$('#ChangeStatusForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (success) {
            $('#change-additional-statut').find('.modalContent').empty();
            $('#change-additional-statut').modal('hide');
            $('#statut-".$model->event_id."-".$model->event_additional_statut_id."').html(success.name);
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");   
?>