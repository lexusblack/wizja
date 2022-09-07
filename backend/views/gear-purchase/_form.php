<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearPurchase */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-purchase-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'gear_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->andWhere(['type'=>3])->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz sprzęt')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => Yii::t('app', 'Liczba sztuk')]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Cena')]) ?>

    <?= $form->field($model, 'total_price')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Cena łącznie')]) ?>

    <?= $form->field($model, 'datetime')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Wybierz datę zakupu'),
                'autoclose' => true,
            ]
        ],
    ]); ?>
    <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), []);
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('

$("#'.Html::getInputId($model, 'quantity').'").change(function(e){
    total_price = $("#'.Html::getInputId($model, 'quantity').'").val()*$("#'.Html::getInputId($model, 'price').'").val();
    $("#'.Html::getInputId($model, 'total_price').'").val(total_price);
});
$("#'.Html::getInputId($model, 'price').'").change(function(e){
    total_price = $("#'.Html::getInputId($model, 'quantity').'").val()*$("#'.Html::getInputId($model, 'price').'").val();
    $("#'.Html::getInputId($model, 'total_price').'").val(total_price);
});
$("#'.Html::getInputId($model, 'total_price').'").change(function(e){
    if ($("#'.Html::getInputId($model, 'quantity').'").val()>0)
    {
        price = $("#'.Html::getInputId($model, 'total_price').'").val()/$("#'.Html::getInputId($model, 'quantity').'").val();
        $("#'.Html::getInputId($model, 'price').'").val(price);       
    }

});
');
?>
