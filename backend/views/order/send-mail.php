<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Wyślij Zamówienie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['type' => 'email'])->label(Yii::t('app', "Adres e-mail")) ?>
	<?= $form->field($model, 'recipients')->multiselect($model->getRecipientsList()); ?>
    <?= $form->field($model, 'subject')->textInput() ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
