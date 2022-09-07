<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?php echo Html::activeLabel($model, 'filename'); ?>
        <?php echo devgroup\dropzone\DropZone::widget([
            'url'=>\common\helpers\Url::to(['upload']),
            'name'=>'file',
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#'.Html::getInputId($model, 'filename').'").val(response.filename);
                   $("#'.Html::getInputId($model, 'mime_type').'").val(response.type);
                   $("#'.Html::getInputId($model, 'extension').'").val(response.extension);
                   $("#'.Html::getInputId($model, 'base_name').'").val(response.name);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("type", $("#'.Html::getInputId($model, 'type').'").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?= Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.') ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>

    <?php
        $model->type = \common\models\SettingAttachment::TYPE_OFFER;
        echo Html::activeHiddenInput($model, 'type');
    ?>

    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['location/view', 'id'=>$model->location_id], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
