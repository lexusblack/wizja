<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OuterGear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outer-gear-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

    <?= $form->field($model, 'outer_gear_model_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\OuterGearModel::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label(Yii::t('app', 'Sprzęt/Mat. eksploatacyjny')); ?>

            <?= $form->field($model, 'quantity')->textInput() ?> 

            <?= $form->field($model, 'company_id')->widget(\common\widgets\CustomerField::className(), [])->label(Yii::t('app', 'Firma/Dostawca'));; ?>

            <?= $form->field($model, 'price')->textInput()->hint(Yii::t('app', 'Dla materiałów eskploatacyjnych cena zakupu')) ?>

            <?= $form->field($model, 'selling_price')->textInput() ?>
        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
