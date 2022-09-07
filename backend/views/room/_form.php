<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Room */
/* @var $form yii\widgets\ActiveForm */

\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'RoomPhoto', 
        'relID' => 'room-photo', 
        'value' => \yii\helpers\Json::encode($model->roomPhotos),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>

<div class="room-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' =>  Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'area')->textInput(['placeholder' =>  Yii::t('app', 'Powierzchnia [m2]')]) ?>

    <?= $form->field($model, 'podkowa')->textInput(['placeholder' =>  Yii::t('app', 'Podkowa')]) ?>

    <?= $form->field($model, 'bankiet')->textInput(['placeholder' =>  Yii::t('app', 'Bankiet')]) ?>

    <?= $form->field($model, 'teatr')->textInput(['placeholder' =>  Yii::t('app', 'Teatr')]) ?>

    <?= $form->field($model, 'location_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Location::find()->where(['<>', 'public',2])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' =>  Yii::t('app', 'Wybierz miejsce')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?  Yii::t('app', 'Dodaj') :  Yii::t('app', 'Edytuj'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
