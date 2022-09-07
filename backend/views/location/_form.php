<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
//use kartik\alert\Alert;
use yii\bootstrap\Alert;
/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?php
            if ($model->existingModels !== null)
            {
                foreach ($model->existingModels as $location)
                {
                    echo Alert::widget([
                        'options' => [
                            'class' => 'alert-info',
                        ],
                        'body' => Html::a($location->displayLabel, ['/location/view', 'id'=>$location->id], ['target'=>'_blank']),
                    ]);
                }

                echo $form->field($model, 'type')->checkbox([
                    'label' => Yii::t('app', 'Dodać mimo to?')
                ]);
            }
            ?>

            <?php echo $form->field($model, 'public')->checkbox(); ?>
            <div class="alert alert-info" role="alert">
            <?= Yii::t('app', 'Miejsce') ?><strong><?= Yii::t('app', 'prywatne') ?></strong> <?= Yii::t('app', 'jest widoczne tylko w obrębie Twojej firmy.') ?>
            <?= Yii::t('app', 'Miejsce') ?> <strong><?= Yii::t('app', 'publiczne') ?></strong>, <?= Yii::t('app', 'po zaakceptowaniu przez moderatora, będzie widoczne u wszystkich użytkowników systemu.') ?>
            </div>

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'zip')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'country_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Country::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

            <?= $form->field($model, 'province_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Province::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'location_type_id')->widget(\common\widgets\LocationTypeField::className()); ?>

            <?= $form->field($model, 'beds')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'biggest_room')->textInput(['maxlength' => true]) ?>


            <?= $form->field($model, 'stars')->dropDownList(\common\models\Location::getStarList()); ?>

            <?= $form->field($model, 'travel_time', [
                'addon' => ['append' => ['content'=>'min']]
            ])->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'manager_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'electrician_phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'distance', [
                'addon' => ['append' => ['content'=>'km']]
            ])->textInput(['maxlength' => true])->hint(Yii::t('app', 'Jeśli nie zostanie wypełnione, zostanie pobrane z Google (jeśli to możliwe).')) ?>

            <?php
            if ($model->getPhotoUrl())
            {
                echo Html::img($model->getPhotoUrl(), ['style'=>'width:200px', 'class'=>'thumbnail']);
            }
            ?>
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
               $("#'.Html::getInputId($model, 'photo').'").val(response.filename);

            }',
                    ]
                ]); ?>
                <?php echo Html::error($model, 'photo'); ?>
            </div>
        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
