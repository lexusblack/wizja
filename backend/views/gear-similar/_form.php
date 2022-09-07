<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearSimilar */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-similar-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


    <?= $form->field($model, 'similar_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->andWhere(['<>', 'id', $model->gear_id])->orderBy('name')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app','Wybierz sprzęt')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <?php echo $form->field($model, 'both')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Zapisz') : Yii::t('app','Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
