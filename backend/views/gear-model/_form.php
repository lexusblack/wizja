<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearModel */
/* @var $form yii\widgets\ActiveForm */

\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'GearModelAttachment', 
        'relID' => 'gear-model-attachment', 
        'value' => \yii\helpers\Json::encode($model->gearModelAttachments),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>

<div class="gear-model-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-md-6">


                <?php
            echo $form->field($model, 'company_id')->widget(\common\widgets\CompanyField::className())
            ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'brightness')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Jasność')]) ?>

    <?= $form->field($model, 'power_consumption')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Zużycie prądu')]) ?>

    <?= $form->field($model, 'category_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\GearModelCategory::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz kategorię')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'width')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Szerokosć')]) ?>

    <?= $form->field($model, 'height')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Wysokość')]) ?>

    <?= $form->field($model, 'depth')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Głębokość')]) ?>

    </div>
    <div class="col-md-6">

    <?= $form->field($model, 'weight')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Waga')]) ?>


                <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ]);?>

            <div class="form-group">
                <?php echo Html::activeHiddenInput($model, 'photo'); ?>
                <?php echo Html::activeLabel($model, 'photo'); ?>
                <?php echo devgroup\dropzone\DropZone::widget([
                    'url'=>\common\helpers\Url::to(['upload']),
                    'name'=>'file',
                    'options'=>[
                        'maxFiles' => 1,
                    ],
                    'eventHandlers' => [
                        'success' => 'function(file, response) {
               $("#gearmodel-photo").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>

    </div>
    </div>
            <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Dodaj') : Yii::t('app', 'Edytuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
