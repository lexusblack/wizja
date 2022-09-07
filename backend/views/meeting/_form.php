<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Meeting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength'=>true]) ?>

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

    <?php
        echo $form->field($model, 'location')->textInput(['maxlength'=>true]);
    ?>

    <?php
        echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), [])->hint(Yii::t('app', 'Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"'));
    ?>

    <?php
        echo $form->field($model, 'contact_id')->widget(\common\widgets\ContactField::className())->hint(Yii::t('app', 'Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"'));
    ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'userIds')->widget(\kartik\widgets\Select2::className(), [
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
    <?php echo $form->field($model, 'reminder')->dropDownList(\common\models\Meeting::getReminderList(), ['prompt'=>Yii::t('app', 'Brak')]); ?>

    <fieldset>
        <legend><?= Yii::t('app', 'Forma przypomnienia') ?></legend>
        <?php echo $form->field($model, 'remind_email')->checkbox(); ?>
        <?php echo $form->field($model, 'remind_sms')->checkbox(); ?>
        <?php echo $form->field($model, 'remind_push')->checkbox(); ?>
    </fieldset>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
