<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchemaCat */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="tasks-schema-cat-form">

    <?php $form = ActiveForm::begin(['id'=>'TasksSchemaCatForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor'));
                ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
if ($model->isNewRecord){
 $this->registerJs("
$('#TasksSchemaCatForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            addNewCategoryRow(data);
            $('#new-service-category').find('.modalContent').empty();
            $('#new-service-category').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");   
}else{
    $this->registerJs("
$('#TasksSchemaCatForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            UpdateCategoryRow(data);
            $('#edit-service-category').find('.modalContent').empty();
            $('#edit-service-category').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");
}

?>