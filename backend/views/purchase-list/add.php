<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseList */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="purchase-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'purchase_list_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\helpers\ArrayHelper::map(\common\models\PurchaseList::find()->orderBy(['datetime'=>SORT_DESC])->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?php
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
