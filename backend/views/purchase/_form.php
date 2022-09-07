<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Purchase */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="purchase-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'user_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \common\models\User::getList(),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'datetime')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Wybierz datę'),
                'autoclose' => true,
            ]
        ],
    ]); ?>

    <?php echo $form->field($model, 'sections')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\EventExpense::getSectionList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
    <?php echo $form->field($model, 'eventIds')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\Event::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Cena')]) ?>


    <?= $form->field($model, 'purchase_type_id')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\PurchaseType::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Wybierz')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#purchase-price").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});

');