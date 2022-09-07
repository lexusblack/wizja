<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */
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
                   $("#locationattachment-filename").val(response.filename);
                   $("#locationattachment-mime_type").val(response.type);
                   $("#locationattachment-extension").val(response.extension);
                   $("#locationattachment-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("type", $("#locationattachment-type").val());
                    formData.append("location_id", $("#locationattachment-location_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?= Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.') ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <?php echo $form->field($model, 'type')->dropDownList(\common\models\LocationAttachment::getTypeList()); ?>

    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['location/view', 'id'=>$model->location_id], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
