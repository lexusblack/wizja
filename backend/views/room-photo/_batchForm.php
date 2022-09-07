<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationPhoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="room-photo-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'room_id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#roomphoto-filename").val(response.filename);
                   $("#roomphoto-mime_type").val(response.type);
                   $("#roomphoto-extension").val(response.extension);
                   $("#roomphoto-base_name").val(response.name);
                    console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("room_id", $("#roomphoto-room_id").val());
                }',
            ]
        ]); ?>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <div class="form-group">
        <?php echo Html::a( Yii::t('app', 'Powrót'), ['room/update', 'id'=>$model->room_id], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
