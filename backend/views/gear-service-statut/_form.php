<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="gear-service-statut-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' =>Yii::t('app', 'Nazwa')]) ?>

    <?php echo $form->field($model, "color")->widget(\kartik\widgets\ColorInput::className(),[
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz kolor...'),
                    ],
                ])->label(Yii::t('app', 'Kolor'));
                ?>

     <?php echo $form->field($model, 'type')->dropDownList(\common\models\GearServiceStatut::getTypes()) ?>

    <?= $form->field($model, 'in_menu')->checkbox();?>

    <?php echo $form->field($model, 'permissions')->widget(\kartik\widgets\Select2::className(), [
                'data' => \common\models\AuthItem::getList(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz...'),
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])->label(Yii::t('app', 'Ogranicz możliwość nadawania tego statusu do grup uprawnień'));
            ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
