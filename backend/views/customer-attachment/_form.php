<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-attachment-form">

    <?php $form = ActiveForm::begin(['id' => 'attachment-form']); ?>
    <?php echo $form->field($model, 'customer_id')->hiddenInput()->label(false); ?>
    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                    var filnameInput = "<input type=\"hidden\" name=\"CustomerAttachment["+file_number+"][filename]\" value=\""+response.filename+"\" >";
                    var baseNameInput = "<input type=\"hidden\" name=\"CustomerAttachment["+file_number+"][base_name]\" value=\""+response.type+"\" >";
                    var extensionInput = "<input type=\"hidden\" name=\"CustomerAttachment["+file_number+"][extension]\" value=\""+response.extension+"\" >";
                    var mimeTypeInput = "<input type=\"hidden\" name=\"CustomerAttachment["+file_number+"][mime_type]\" value=\""+response.name+"\" >";
                    var gearIdInput = "<input type=\"hidden\" name=\"CustomerAttachment["+file_number+"][customer_id]\" value=\"'.$model->customer_id.'\" >";

                    $("#attachment-form").append(filnameInput);
                    $("#attachment-form").append(baseNameInput);
                    $("#attachment-form").append(extensionInput);
                    $("#attachment-form").append(mimeTypeInput);
                    $("#attachment-form").append(gearIdInput);
                    file_number++;
            }',
            'sending' => 'function(file, xhr, formData){
                    formData.append("customer_id", '.$model->customer_id.');
                }'
            ]
        ]); ?>
    </div>


    <?php ActiveForm::end(); ?>
<?= Html::a(Yii::t('app', 'Powrót'),['/customer/view', 'id'=>$model->customer_id, '#'=>'tab-attachment'], ['class' =>  'btn btn-success']) ?>
</div>

<?php

$this->registerJs('
    var file_number = 0;
    
');
