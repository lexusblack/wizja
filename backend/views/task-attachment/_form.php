<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-attachment-form">

    <?php $form = ActiveForm::begin(['id' => 'attachment-form']); ?>
    <?php echo $form->field($model, 'task_id')->hiddenInput()->label(false); ?>
    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                    var filnameInput = "<input type=\"hidden\" name=\"TaskAttachment["+file_number+"][filename]\" value=\""+response.filename+"\" >";
                    var baseNameInput = "<input type=\"hidden\" name=\"TaskAttachment["+file_number+"][base_name]\" value=\""+response.type+"\" >";
                    var extensionInput = "<input type=\"hidden\" name=\"TaskAttachment["+file_number+"][extension]\" value=\""+response.extension+"\" >";
                    var mimeTypeInput = "<input type=\"hidden\" name=\"TaskAttachment["+file_number+"][mime_type]\" value=\""+response.name+"\" >";
                    var gearIdInput = "<input type=\"hidden\" name=\"TaskAttachment["+file_number+"][task_id]\" value=\"'.$model->task_id.'\" >";

                    $("#attachment-form").append(filnameInput);
                    $("#attachment-form").append(baseNameInput);
                    $("#attachment-form").append(extensionInput);
                    $("#attachment-form").append(mimeTypeInput);
                    $("#attachment-form").append(gearIdInput);
                    file_number++;
            }',
            'sending' => 'function(file, xhr, formData){
                    formData.append("task_id", '.$model->task_id.');
                }'
            ]
        ]); ?>
    </div>


    <?php ActiveForm::end(); ?>
<?= Html::a(Yii::t('app', 'Powrót'), '', ['class' =>  'btn btn-success']) ?>
</div>

<?php

$this->registerJs('
    var file_number = 0;
    
');

$this->registerCss('.dz-hidden-input {
    z-index: 10000;
}');
