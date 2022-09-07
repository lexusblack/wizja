<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-attachment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'gear_model_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\GearModel::getSelectList(),
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
                   $("#gearmodelattachment-filename").val(response.filename);
                   $("#gearmodelattachment-mime_type").val(response.type);
                   $("#gearmodelattachment-extension").val(response.extension);
                   $("#gearmodelattachment-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("info", $("#gearmodelattachment-info").val());
                    formData.append("gear_model_id", $("#gearmodelattachment-gear_model_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?php echo Yii::t('app', 'Możesz dodawać wiele plików naraz, ich typ będzie taki, jak ustawiony poniżej w czasie dodawania.'); ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>




    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['gear-model/view', 'id'=>$model->gear_model_id], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
