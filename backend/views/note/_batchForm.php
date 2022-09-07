<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationPanorama */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo $form->field($model, 'note_id')->hiddenInput()->label(false); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#noteattachment-filename").val(response.filename);
                   $("#noteattachment-mime_type").val(response.type);
                   $("#noteattachment-extension").val(response.extension);
                   $("#noteattachment-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("note_id", $("#noteattachment-note_id").val());
                }',
            ]
        ]); ?>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <div class="form-group">
        <?php 
        if ($note->type==1)
            echo Html::a(Yii::t('app', 'Powrót'), ['/project/view', 'id'=>$note->project_id, '#'=>'tab-note'], ['class'=>'btn btn-primary btn-block']); 
        if ($note->type==2)
            echo Html::a(Yii::t('app', 'Powrót'), ['/event/view', 'id'=>$note->event_id, '#'=>'tab-notes'], ['class'=>'btn btn-primary btn-block']); 
        ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
