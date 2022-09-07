<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Wyślij pliki');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput() ?>
    <?php echo $form->field($model, 'users')->widget(\kartik\widgets\Select2::className(), [
                    'data' => \common\models\User::getList(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Wybierz...'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ])->label(Yii::t('app', 'Inni użytkownicy'));
                ?>
    <?php
            echo $form->field($model, 'customer_id')->widget(\common\widgets\CustomerField::className(), [])->label(Yii::t('app', 'Wyślij do klienta'));
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>

            <?php
            echo $form->field($model, 'contact_id')->widget(\common\widgets\ContactField2::className())->label(Yii::t('app', 'Wybierz kontakty'));
                //->hint('Możesz dodać nową opcję wpisując nazwę i naciskając "Enter"');
            ?>
    <?= $form->field($model, 'subject')->textInput() ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>