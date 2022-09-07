<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Personal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="personal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php

        $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
        $calendarOptions = [
            'timePicker'=>true,
            'timePickerIncrement'=>5,
            'timePicker24Hour' => true,
            'locale'=>['format' => 'Y-m-d H:i'],
            'linkedCalendars'=>false,
        ];

        echo '<label class="control-label">'.$model->getAttributeLabel('dateRange').'</label>';
        echo '<div class="input-group drp-container">';
        echo \kartik\daterange\DateRangePicker::widget([
                'model'=>$model,
                'attribute' => 'dateRange',
                'useWithAddon'=>true,
                'convertFormat'=>true,
                'startAttribute' => 'start_time',
                'endAttribute' => 'end_time',
                'pluginOptions'=>$calendarOptions,
            ]) . $addon;
        echo '</div>';
        ?>
    </div>

    <?= $form->field($model, 'repeat')->dropDownList(\common\models\Personal::getRepeatList()); ?>

    <?= $form->field($model, 'repeat_since')->widget(\kartik\widgets\DatePicker::className(), [
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
        ]
    ]) ?>

    <?php echo $form->field($model, 'reminder')->dropDownList(\common\models\Personal::getReminderList(), ['prompt'=> Yii::t('app', 'Brak')]); ?>

    <fieldset>
        <legend><?=  Yii::t('app', 'Forma przypomnienia') ?></legend>
        <?php echo $form->field($model, 'remind_email')->checkbox(); ?>
        <?php echo $form->field($model, 'remind_sms')->checkbox(); ?>
        <?php echo $form->field($model, 'remind_push')->checkbox(); ?>
    </fieldset>




    <div class="form-group">
        <?= Html::submitButton( Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
