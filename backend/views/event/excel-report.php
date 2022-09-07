<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */


$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
?>
<div class="event-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'enableClientScript' => false,
    ]); ?>
    <?php
        echo $form->errorSummary($model);
    ?>
    <div class="row">
    <h1><?=Yii::t('app', 'Raport wydarzeń')?></h1>
    <p><?=Yii::t('app', 'Wybierz parametry i kliknij wygeneruj')?></p>
        <div class="col-md-6">
        <label><?=Yii::t('app', 'Miesiąc księgowania')?></label>

        <?php
            echo $form->field($model, 'paying_date')->widget(\kartik\widgets\Select2::className(), [
                    'data' => \common\models\Event::getPayingDateList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ])->label(false);
            
            ?>
            </div>
        <div class="col-md-6">
        <label><?=Yii::t('app', ' Termin wydarzeń')?></label>
        <?php echo \kartik\daterange\DateRangePicker::widget([
                    'options' => ['class'=>' form-control'],
                    'model' => $model,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'startAttribute' => 'dateStart',
                    'endAttribute' => 'dateEnd',
                    'startInputOptions' => [
                        'class'=>'grid-filters',
                    ],
                    'endInputOptions' => [
                        'class'=>'grid-filters',
                    ],
                    'pluginOptions' => [
                    'linkedCalendars'=>false,
                        'locale'=>[
                            'format'=>'Y-m-d'
                        ]
                    ],
                    'pluginEvents' => [
                        'apply.daterangepicker'=>'function(ev,picker){
                            $("#date-use-range").val(1).trigger("change");
                        }',
                    ]
                ]);
                ?>



        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t('app', 'Generuj raport XLS'), ['class' =>  'btn btn-success']) ?>
            </div>
        </div>
    </div>














    <?php ActiveForm::end(); ?>
</div>
