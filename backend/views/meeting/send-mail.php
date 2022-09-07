<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Wyślij zaproszenie do uczestników')." ".$model->meeting->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Spotkanie'), 'url' => ['view', 'id'=>$model->meeting->id]];
$this->params['breadcrumbs'][] = $this->title;
$model->subject = $model->meeting->name;
$model->text = $model->meeting->description;
?>
<div class="offer-create">
<div class="row"><?= Html::a("Pomiń wysyłanie", ["view", 'id'=>$model->meeting->id], ['class'=>'btn btn-danger btn-sm'])?></div>
    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput() ?>
	<?= $form->field($model, 'recipients')->multiselect($model->getRecipientsList()); ?>
    <?= $form->field($model, 'subject')->textInput() ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
