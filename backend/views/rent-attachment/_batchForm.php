<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'rent_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\Rent::getModelList(),
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
                   $("#rentattachment-filename").val(response.filename);
                   $("#rentattachment-mime_type").val(response.type);
                   $("#rentattachment-extension").val(response.extension);
                   $("#rentattachment-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("rent_id", $("#rentattachment-rent_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?php echo Yii::t('app', 'Możesz dodawać wiele plików naraz.'); ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>



    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['rent/view', 'id'=>$model->rent_id, '#'=>'tab-attachment'], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
