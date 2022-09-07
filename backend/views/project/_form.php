<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>
 <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'placeholder' => 'Identyfikator']) ?>
            <?php 
            if ($schema_change_possible)
            echo $form->field($model, 'tasks_schema_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\TasksSchema::getList('project'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);
            ?>

            <?php
            echo $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className())
            ?>

    <?= $form->field($model, 'start_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Start Time',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'end_time')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose End Time',
                'autoclose' => true,
            ]
        ],
    ]); ?>
            <?php echo $form->field($model, 'departmentIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Department::getModelList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
            <?php if ($model->isNewRecord)
             echo $form->field($model, 'managerIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_SUPERADMIN]),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>

    <?php echo $form->field($model, 'description')->widget(\common\widgets\RedactorField::className()); ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
