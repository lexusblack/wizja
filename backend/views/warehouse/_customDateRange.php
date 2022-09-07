<?php
/* @var $this \yii\web\View; */
/* @var $item \common\models\GearItem */
/* @var $warehouse \common\models\form\WarehouseSearch */
/* @var $context \backend\controllers\WarehouseController */
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Html;

$context = $this->context;

$model = new \common\models\form\GearAssignment();
$model->warehouse = $warehouse;
$model->itemId = $item->id;
$model->setOwner($owner);
$model->initDates();
?>

<div class="date-range-modal-content">
    <?php
    $form = ActiveForm::begin([
        'id'=>'custom_date_range-form-'.$item->id,
        'type'=>ActiveForm::TYPE_INLINE,
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
    echo $form->field($model, 'dateRange')->widget(DateRangePicker::className(), [
        'options' => [
            'id'=>'custom_date_range-item-'.$item->id,
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
    <?php echo Html::submitButton(Html::icon('ok'), ['class'=>'btn btn-success set-custom-dates', 'data'=>['add'=>0, 'itemId'=>$item->id, 'pjax'=>0]]); ?>

    <?php
//        if ($item->isAssignedTo($owner))
//        {
            echo Html::a(Html::icon('remove'), '#', ['class'=>'btn btn-danger remove-custom-dates', 'data-pjax'=>0, 'data'=>['add'=>0, 'itemId'=>$item->id, 'pjax'=>0]]);
//        }

    ?>
    <?php
    ActiveForm::end();
    ?>
</div>
