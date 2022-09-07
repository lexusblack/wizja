<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VehicleAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vehicle-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'vehicle_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Vehicle::getModelList(),
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
                   $("#'.Html::getInputId($model, 'filename').'").val(response.filename);
                   $("#'.Html::getInputId($model, 'mime_type').'").val(response.type);
                   $("#'.Html::getInputId($model, 'extension').'").val(response.extension);
                   $("#'.Html::getInputId($model, 'base_name').'").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
//                    formData.append("type", $("#locationattachment-type").val());
                    formData.append("vehicle_id", $("#'.Html::getInputId($model, 'vehicle_id').'").val());
                }',
            ]
        ]);

        ?>
        <div class="text-muted">
            <?= Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.') ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>

    <div class="form-group">
        <?php
        if ($model->vehicle_id != null) {
            echo Html::a(Yii::t('app', 'Powrót'), ['vehicle/view', 'id'=>$model->vehicle_id], ['class'=>'btn btn-primary btn-block']);
        }
        else {
            echo Html::a(Yii::t('app', 'Powrót'), ['vehicle-attachment/index', 'id'=>$model->vehicle_id], ['class'=>'btn btn-primary btn-block']);
        }?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
