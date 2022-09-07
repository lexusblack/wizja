<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseListItem */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="purchase-list-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'purchase_list_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\PurchaseList::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Ilość')]) ?>

    <?php
            echo $form->field($model, 'price')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>2,
                ]
            ]);
            ?>
    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Firma')]) ?>

    <?= $form->field($model, 'company_address')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Adres')]) ?>

    <?= $form->field($model, 'event_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\Event::getList(),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'status')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\PurchaseListItem::getStatusList(),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Uwagi')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
