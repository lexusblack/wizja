<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\HallGroupPrice */
/* @var $form yii\widgets\ActiveForm */

\mootensai\components\JsBlock::widget(['viewFile' => '_script', 'pos'=> \yii\web\View::POS_END, 
    'viewParams' => [
        'class' => 'HallGroupPricePercent', 
        'relID' => 'hall-group-price-percent', 
        'value' => \yii\helpers\Json::encode($model->hallGroupPricePercents),
        'isNewRecord' => ($model->isNewRecord) ? 1 : 0
    ]
]);
?>

<div class="hall-group-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Cena')]) ?>

    <?= $form->field($model, 'vat')->textInput(['maxlength' => true, 'placeholder' => 'Vat']) ?>

    <?= $form->field($model, 'currency')->dropDownList(\backend\modules\finances\Module::getCurrencyList()); ?>

    <?= $form->field($model, 'default')->dropDownList([0=>Yii::t('app', 'Nie'), 1=>Yii::t('app', 'Tak')]); ?>

    <?php
    $forms = [
        [
            'label' => Yii::t('app', 'Procent dnia pierwszego'),
            'content' => $this->render('_formHallGroupPricePercent', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->hallGroupPricePercents),
            ]),
        ],
    ];
    echo kartik\tabs\TabsX::widget([
        'items' => $forms,
        'position' => kartik\tabs\TabsX::POS_ABOVE,
        'encodeLabels' => false,
        'pluginOptions' => [
            'bordered' => true,
            'sideways' => true,
            'enableCache' => false,
        ],
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
