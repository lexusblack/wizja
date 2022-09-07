<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Wygeneruj maskę LED');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'width')->textInput() ?>
    <?= $form->field($model, 'height')->textInput() ?>
    <?= $form->field($model, 'cols')->textInput() ?>
    <?= $form->field($model, 'rows')->textInput() ?>
    <?php echo $form->field($model, 'color')->dropDownList([1=>Yii::t('app', 'Biały'),2=>Yii::t('app', 'Czerwony'),3=>Yii::t('app', 'Niebieski'),4=>Yii::t('app', 'Zielony')]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wygeneruj'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
