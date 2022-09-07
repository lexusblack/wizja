<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AgencyOffer */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="agency-offer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

                <?php echo $form->field($model, 'schema_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\OfferSchema::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz schemat...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']) ?>

            <?= $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);?>

            <?= $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className());?>

            <?= $form->field($model, 'location_id')->widget(\common\widgets\LocationField::className()); ?>

            <?php echo $form->field($model, 'manager_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_SUPERADMIN]),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>


    <?= $form->field($model, 'event_start')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Event Start',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'event_end')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Event End',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'offer_date')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Offer Date',
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'payment_date')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => 'Choose Payment Date',
                'autoclose' => true,
            ]
        ],
    ]); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Dodaj') : Yii::t('app','Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
