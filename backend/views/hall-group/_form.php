<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroup */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="hall-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?php echo $form->field($model, 'hallIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map(\common\models\Hall::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Segmenty'));
            ?>

    <?= $form->field($model, 'area')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Powierzchnia')]) ?>

    <?= $form->field($model, 'width')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Szerokość')]) ?>

    <?= $form->field($model, 'length')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Długość')]) ?>

    <?= $form->field($model, 'height')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Wysokość')]) ?>


    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                    <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'main_photo'); ?>
                <?php echo Html::activeLabel($model, 'main_photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#hallgroup-main_photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'main_photo'); ?>
            </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
