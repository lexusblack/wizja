<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\brief */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-brief-form">

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
                   $("#brief-filename").val(response.filename);
                   $("#brief-mime_type").val(response.type);
                   $("#brief-extension").val(response.extension);
                   $("#brief-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("type", $("#brief-type").val());
                    formData.append("public", $("#brief-public").val());
                    formData.append("event_id", $("#brief-event_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?php echo Yii::t('app', 'Możesz dodawać wiele plików na raz.'); ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>


    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['event/view', 'id'=>$model->event_id, '#'=>'tab-brief'], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
