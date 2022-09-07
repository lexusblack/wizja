<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearsPrice */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="gears-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')])->label(Yii::t('app', 'Nazwa')) ?>

    <?php echo $form->field($model, 'gears_price_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\GearsPrice::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Skopiuj ceny sprzętów ze stawki'));
            ?>

    <?php echo $form->field($model, 'type')->dropDownList(\common\models\GearsPrice::getTypeList()) ?>

    <?= $form->field($model, 'gear_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Gear::find()->where(['active'=>1])->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

    <?= $form->field($model, 'gear_category_id')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\GearCategory:: getFullList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

            <?= $form->field($model, 'currency')->dropDownList(\backend\modules\finances\Module::getCurrencyList()); ?>

    <?php echo $form->field($model, 'priceGroupIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\PriceGroup::find()->asArray()->all(), 'id', 'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Powiązane grupy cenowe'));
            ?>
    

    <?php
            echo $form->field($model, 'vat')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions'=> [
                    'alias'=>'decimal',
                    'rightAlign'=>false,
                    'digits'=>1,
                ]
            ])->label(Yii::t('app', 'Stawka VAT'));
            ?>
<?php
    $forms = [
        [
            'label' => '<i class="glyphicon glyphicon-book"></i> ' .Yii::t('app', 'Cena za kolejne dni'),
            'content' => $this->render('_formPercent', [
                'row' => \yii\helpers\ArrayHelper::toArray($model->gearsPricePercents),
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
<?php
$this->registerJs('

$("#'.Html::getInputId($model, 'type').'").change(function(e){
    type = $(this).val();
    changeType(type);
    });

function changeType(type)
{
    if (type==1)
    {
        $(".field-'.Html::getInputId($model, 'gear_category_id').'").hide();
        $(".field-'.Html::getInputId($model, 'gear_id').'").hide();
        
    }
    if (type == 2)
    {
        $(".form-group").show();
        $(".field-'.Html::getInputId($model, 'gear_id').'").hide();

    }
    if (type ==3)
    {
        $(".form-group").show();
        $(".field-'.Html::getInputId($model, 'gear_category_id').'").hide();
    }
}

changeType($("#'.Html::getInputId($model, 'type').'").val());
');