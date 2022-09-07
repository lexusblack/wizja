<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use backend\models\SettingsForm;

/* @var $this yii\web\View */
/* @var $model common\models\PriceGroup */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="price-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

        <?= $form->field($model, 'currency')->widget(Select2::className(), [
            'data' => SettingsForm::getCurrencyListAll(),
            'pluginOptions' => [
                'placeholder' => 'Wpisz walutę',
                'tags' => true,
                'allowClear' => true,
            ],
        ]); ?>

    <?php echo $form->field($model, 'gearsPriceIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\GearsPrice::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Powiązane stawki'));
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
