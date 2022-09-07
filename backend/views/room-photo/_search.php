<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RoomPhotoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-room-photo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Nazwa pliku')]) ?>

    <?= $form->field($model, 'extension')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Rozszerzenie')]) ?>

    <?= $form->field($model, 'status')->textInput(['placeholder' =>  Yii::t('app', 'Status')]) ?>

    <?= $form->field($model, 'room_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Room::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz pokój')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('app', 'Szukaj'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton( Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
