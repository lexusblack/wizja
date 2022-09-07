<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\estimate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-estimate-form">

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
                   $("#estimate-filename").val(response.filename);
                   $("#estimate-mime_type").val(response.type);
                   $("#estimate-extension").val(response.extension);
                   $("#estimate-base_name").val(response.name);
    //                console.log(file, response);
    
                }',
                'sending' => 'function(file, xhr, formData){
                    formData.append("type", $("#estimate-type").val());
                    formData.append("public", $("#estimate-public").val());
                    formData.append("event_id", $("#estimate-event_id").val());
                }',
            ]
        ]); ?>
        <div class="text-muted">
            <?php echo Yii::t('app', 'Możesz dodawać wiele plików na raz.'); ?>
        </div>
        <?php echo Html::error($model, 'filename'); ?>
    </div>


    <div class="form-group">
        <?php echo Html::a(Yii::t('app', 'Powrót'), ['event/view', 'id'=>$model->event_id, '#'=>'tab-estimate'], ['class'=>'btn btn-primary btn-block']); ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
