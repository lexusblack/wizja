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

<?php $form = ActiveForm::begin(['id'=>'TaskSmallForm']); ?>

                        <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa zadania'), 'autocomplete'=>"off", 'class'=>'new-task-input form-control'])->label(false) ?>
                        <?= $form->field($model, 'task_category_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
                        <?= $form->field($model, 'event_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
                        <?php ActiveForm::end(); ?>
<?php

$this->registerJs("
$('#TaskSmallForm').on('submit', function(e){
    e.preventDefault();
});");

$this->registerCss("
.help-block{
	display:none;
}");
?>