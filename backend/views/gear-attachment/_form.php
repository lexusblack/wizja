<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-attachment-form">

    <?php $form = ActiveForm::begin(['id' => 'attachment-form']); ?>

    <?php echo $form->field($model, 'gear_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Gear::getModelList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
        ],
    ]);
    ?>

     <?php echo $form->field($model, 'type')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\helpers\ArrayHelper::map(\common\models\GearAttachemntType::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
        ],
    ])->label(Yii::t('app', 'Folder'));
    ?>   

    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                    var info = $("#gearInfo").val();
                    var type = $("#gearattachment-type").val();
                    var filnameInput = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][filename]\" value=\""+response.filename+"\" >";
                    var typef = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][type]\" value=\""+type+"\" >";
                    var baseNameInput = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][base_name]\" value=\""+response.type+"\" >";
                    var extensionInput = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][extension]\" value=\""+response.extension+"\" >";
                    var mimeTypeInput = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][mime_type]\" value=\""+response.name+"\" >";
                    var gearIdInput = "<input type=\"hidden\" name=\"GearAttachment["+file_number+"][gear_id]\" value=\"'.$model->gear_id.'\" >";
                    var gearInfoInput = "<textarea style=\"display:none;\" name=\"GearAttachment["+file_number+"][info]\" class=\"gearInfoInput\" >"+info+"</textarea>";

                    $("#attachment-form").append(filnameInput);
                    $("#attachment-form").append(typef);
                    $("#attachment-form").append(baseNameInput);
                    $("#attachment-form").append(extensionInput);
                    $("#attachment-form").append(mimeTypeInput);
                    $("#attachment-form").append(gearIdInput);
                    $("#attachment-form").append(gearInfoInput);
                    file_number++;
            }',
            ]
        ]); ?>
    </div>

    <?= $form->field($model, 'info')->textarea(['rows' => 6, 'id' => 'gearInfo']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    var file_number = 0;
    
    $("#gearInfo").bind("input propertychange", function() {
        $(".gearInfoInput").val($(this).val());
    });
    
');
