<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MonthCost */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="month-cost-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Nazwa')]) ?>

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
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Kwota')]) ?>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs('
    $("#monthcost-amount").on("input", function(){
    $(this).val($(this).val().replace(",", "."));
    $(this).val($(this).val().replace(" ", ""));
});

');

?>