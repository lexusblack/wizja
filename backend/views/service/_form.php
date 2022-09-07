<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="service-form">

    <?php $form = ActiveForm::begin(['id'=>'ServiceForm']); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'service_category_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\ServiceCategory::find()->where(['id'=>$model->service_category_id])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz kategorię')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php echo $form->field($model, 'in_offer')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
$('#ServiceForm').on('beforeSubmit', function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (data) {
            addNewRow(data);
            $('#new-service').find('.modalContent').empty();
            $('#new-service').modal('hide');
        },
        error: function () {
            alert('Something went wrong');
        }
    });
}).on('submit', function(e){
    e.preventDefault();
});");
?>
