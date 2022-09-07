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
               $("#'.Html::getInputId($model, 'filename').'").val(response.filename);
                $("#'.Html::getInputId($model, 'mime_type').'").val(response.type);
                $("#'.Html::getInputId($model, 'extension').'").val(response.extension);
                $("#'.Html::getInputId($model, 'base_name').'").val(response.name);
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

    <?php echo $form->field($model, 'type')->dropDownList(\common\models\LocationAttachment::getTypeList()); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
