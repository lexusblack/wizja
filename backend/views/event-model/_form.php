<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventModel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="event-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' =>Yii::t('app', 'Nazwa')]) ?>

      <?php echo $form->field($model, 'type')->dropDownList([1=>Yii::t('app', 'Widok rozbudowany'), 2=>Yii::t('app', 'Widok uproszczony'), 3=>Yii::t('app', 'Rezerwacja powierzchni')]); ?>

      <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor w kalendarzu'));
                ?>

        <?php echo $form->field($model, "color_line")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor paska w kalendarzu'));
                ?>
                <?php
        echo $form->field($model, 'schedule_type_id')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \common\helpers\ArrayHelper::map(\common\models\ScheduleType::find()->asArray()->all(), 'id', 'name'),
                    'options' => [
                    'placeholder' => Yii::t('app', 'Schemat harmonogramu'),
                    ],
                    'language' => 'pl',
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                    ],
            
            ])->label(Yii::t('app', 'Domyślny schemat harmonogramu'));
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
