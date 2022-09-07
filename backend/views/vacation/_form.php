<?php

use common\models\Vacation;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vacation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vacation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php

    $disabled_calendar = false;
    if ($model->status == Vacation::STATUS_ACCEPTED || $model->status == Vacation::STATUS_REJECTED) {
        if (!Yii::$app->user->can('eventsVacationsStatus')) {
            $this->registerCss('.daterangepicker { display: none !important } ');
            $disabled_calendar = true;
        }
    }

    echo $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\User::getList(),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'disabled' => $disabled_calendar,
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
            'tags' => false,
        ],

    ])->hint(Yii::t('app', 'Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"'));
    ?>

    <div class="form-group">
        <?php

        $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
        $calendarOptions = [
            'timePicker'=>false,
            'timePickerIncrement'=>5,
            'timePicker24Hour' => true,
            'linkedCalendars'=>false,
            'locale'=>['format' => 'Y-m-d H:i'],
        ];


        echo '<label class="control-label">'.$model->getAttributeLabel('dateRange').'</label>';
        echo '<div class="input-group drp-container">';
        echo \kartik\daterange\DateRangePicker::widget([
                'model'=>$model,
                'attribute' => 'dateRange',
                'useWithAddon'=>true,
                'disabled' => $disabled_calendar,
                'convertFormat'=>true,
                'startAttribute' => 'start_date',
                'endAttribute' => 'end_date',
                'pluginOptions'=>$calendarOptions,
            ]) . $addon;
        echo '</div>';
        ?>
    </div>



    <?php
        if (Yii::$app->user->can('eventsVacationsStatus'))
        {
            echo $form->field($model, 'status')->dropDownList(\common\models\Vacation::getStatusList(true));
        }

    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
