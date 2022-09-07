<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Event::getModelList(),
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
            'options'=>[
//                'maxFiles' => 1,
            ],
            'eventHandlers' => [
                'success' => 'function(file, response) {
                   $("#attachment-filename").val(response.filename);
                   $("#attachment-mime_type").val(response.type);
                   $("#attachment-extension").val(response.extension);
                   $("#attachment-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("type", $("#attachment-type").val());
                    formData.append("public", $("#attachment-public").val());
                    formData.append("event_id", $("#attachment-event_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?php echo Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.'); ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <?php echo $form->field($model, 'type')->dropDownList(\common\models\Attachment::getTypeList()); ?>

    <?php echo $form->field($model, 'public')->dropDownList(\common\models\Attachment::getPublicList()); ?>

    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['event/view', 'id'=>$model->event_id, '#'=>'tab-attachment'], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
