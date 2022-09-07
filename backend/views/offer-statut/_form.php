<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OfferStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="offer-statut-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nazwa statusu']) ?>

    <?php echo $form->field($model, 'visible_in_planning')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'visible_in_finances')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'blocked')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, 'is_accepted')->dropDownList(['1'=>Yii::t('app', 'Tak'), '0'=>Yii::t('app', 'Nie')]) ?>
    <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor'));
                ?>
    <?php echo $form->field($model, 'reminder_sms')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
     <?php echo $form->field($model, 'reminder_mail')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
     <?= $form->field($model, 'reminder_text')->textarea(['rows' => 6]) ?>
    <?php echo $form->field($model, 'reminder_pm')->dropDownList([1=>Yii::t('app', 'Tak'), 0=>Yii::t('app', 'Nie')]) ?>
     <?php echo $form->field($model, 'users')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\User::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
    <?php echo $form->field($model, 'groups')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\helpers\ArrayHelper::map(\common\models\Team::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Dodaj') : Yii::t('app','Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
