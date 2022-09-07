<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Edycja uwag').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modele'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja uwag');
?>

<div class="gear-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($formModel, 'gear_item_id')->dropDownList($items) ?>

    <?php echo $form->field($formModel, 'info')->textArea(['maxlength' => true, 'rows'=>5]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
