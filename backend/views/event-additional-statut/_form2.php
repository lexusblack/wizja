<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventAdditionalStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="event-additional-statut-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'icon')->widget(\Iconpicker\Widgets\Iconpicker::className()) ?>

     <?php echo $form->field($model, 'reminder_sms')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
     <?php echo $form->field($model, 'reminder_mail')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'users')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Wyślij powiadomienie do'));
            ?>
        <?php echo $form->field($model, 'teams')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map(\common\models\Team::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Wyślij powiadomienie do'));
            ?>
    <?php echo $form->field($model, 'reminder_pm')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
