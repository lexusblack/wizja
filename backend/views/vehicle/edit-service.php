<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vehicle */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Serwis pojazdu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pojazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vehicle->name, 'url' => ['view', 'id'=>$model->vehicle_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vehicle-service-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'description')->textArea() ?>
                    <?= $form->field($model, 'price')->textInput(['maxlength' => true])?>

            </div>
            <div class="col-md-6">
                <label class="control-label"><?= Yii::t('app', 'Koniec naprawy') ?></label>
                <?= DatePicker::widget([
                    'model' => $model,
                    'attribute' => 'end_time',
                    'options' => ['placeholder' => Yii::t('app', 'Wybierz...')],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ],
                ]);
                ?>
                </div>
        </div>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
