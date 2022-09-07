<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearOuterConnected */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-outer-connected-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


    <?= $form->field($model, 'connected_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz sprzęt')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Liczba sztuk')]) ?>
    <?= $form->field($model, 'gear_quantity')->textInput(['placeholder' => Yii::t('app', 'Na')]) ?>
    <?php echo $form->field($model, 'checked')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'in_offer')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'subgroup')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Zapisz') : Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
