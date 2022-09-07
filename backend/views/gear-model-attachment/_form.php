<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gear-attachment-form">

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
            ],
            'eventHandlers' => [
                'success' => 'function(file, response) {
               $("#gearmodelattachment-filename").val(response.filename);
               $("#gearmodelattachment-mime_type").val(response.type);
               $("#gearmodelattachment-extension").val(response.extension);
               $("#gearmodelattachment-base_name").val(response.name);
//                console.log(file, response);

            }',
            ]
        ]); ?>
        <?php echo Html::error($model, 'filename'); ?>
    </div>


    <?php
    echo Html::activeHiddenInput($model, 'filename');
    echo Html::activeHiddenInput($model, 'base_name');
    echo Html::activeHiddenInput($model, 'extension');
    echo Html::activeHiddenInput($model, 'mime_type');
    ?>




    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
