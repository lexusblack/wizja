<?php
/* @var $this \yii\web\View; */
/* @var $warehouse \common\models\form\WarehouseSearch */
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Html;


?>

<div class="date-range-dialog-content">
    <?php
    $form = ActiveForm::begin([
        'id'=>'custom_date_range-form-'.$model->itemId,
        'type'=>ActiveForm::TYPE_HORIZONTAL,
        'options' => [
            'class'=>'custom-date-range-form'
        ],

    ]);
    ?>
    <?php
    echo Html::activeHiddenInput($model, 'itemId');
    echo Html::hiddenInput('asModel', 1);
    ?>
    <?php
    echo DateRangePicker::widget([
        'options' => [
            'id'=>'custom_date_range-item',
        ],
        'model'=>$model,
        'attribute'=>'dateRange',
        'hideInput'=>true,
        'startAttribute'=>'startTime',
        'endAttribute'=>'endTime',
        'useWithAddon'=>false,
        'convertFormat'=>true,
        'pluginOptions' => [
            'timePicker'=>true,
            'timePickerIncrement'=>5,
            'timePicker24Hour' => true,
            'locale'=>['format' => 'Y-m-d H:i'],
        ]
    ]);
    ?>
    <?php echo Html::submitButton(Html::icon('ok'), ['class'=>'btn btn-success item-custom-dates']); ?>

    <?php
    ActiveForm::end();
    ?>
</div>
