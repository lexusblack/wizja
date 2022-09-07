<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EventGearItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-gear-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //$form->field($model, 'event_id')->textInput() ?>


    <?php echo $form->field($model, 'gear_item_id')->widget(\kartik\widgets\Select2::className(), [
        'data' => \common\models\GearItem::getAvaibleGroupedList($model->event_id),
        'options' => [
            'placeholder' => Yii::t('app', 'Wybierz...'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
