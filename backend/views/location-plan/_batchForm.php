<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationPanorama */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'location_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Location::getModelList(),
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

    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#locationplan-filename").val(response.filename);
                   $("#locationplan-mime_type").val(response.type);
                   $("#locationplan-extension").val(response.extension);
                   $("#locationplan-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("location_id", $("#locationplan-location_id").val());
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
