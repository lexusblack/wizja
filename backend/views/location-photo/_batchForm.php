<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationPhoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php echo $form->field($model, 'location_id')->hiddenInput()->label(false); ?>
    <?php /* echo $form->field($model, 'location_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Location::getModelList(),
        'options' => [
            'placeholder' => 'Wybierz...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
            'tags' => true,
        ],
    ]); */
    ?>

    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#locationphoto-filename").val(response.filename);
                   $("#locationphoto-mime_type").val(response.type);
                   $("#locationphoto-extension").val(response.extension);
                   $("#locationphoto-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("location_id", $("#locationphoto-location_id").val());
                    formData.append("name", $("#locationphoto-name").val());
                }',
            ]
        ]); ?>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['location/view', 'id'=>$model->location_id], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
