<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupPhoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-attachment-form">

    <?php $form = ActiveForm::begin(['id' => 'attachment-form']); ?>

    <?php echo $form->field($model, 'hall_group_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\helpers\ArrayHelper::map(\common\models\HallGroup::find()->asArray()->all(), 'id', 'name'),
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
        'data' => \common\helpers\ArrayHelper::map(\common\models\HallGroupPhotoType::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
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
                    var type = $("#HallGroupPhoto-type").val();
                    var filnameInput = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][filename]\" value=\""+response.filename+"\" >";
                    var typef = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][type]\" value=\""+type+"\" >";
                    var baseNameInput = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][base_name]\" value=\""+response.type+"\" >";
                    var extensionInput = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][extension]\" value=\""+response.extension+"\" >";
                    var mimeTypeInput = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][mime_type]\" value=\""+response.name+"\" >";
                    var gearIdInput = "<input type=\"hidden\" name=\"HallGroupPhoto["+file_number+"][hall_group_id]\" value=\"'.$model->hall_group_id.'\" >";

                    $("#attachment-form").append(filnameInput);
                    $("#attachment-form").append(typef);
                    $("#attachment-form").append(baseNameInput);
                    $("#attachment-form").append(extensionInput);
                    $("#attachment-form").append(mimeTypeInput);
                    $("#attachment-form").append(gearIdInput);
                    file_number++;
            }',
            ]
        ]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    var file_number = 0;

    
');