<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app', 'Przypomnij hasło');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

?>
<div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <img alt="image" class="img-responsive" src="/themes/e4e/img/newem.png">

            </div>
            <h3><?= Yii::t('app', 'Przywracanie hasła') ?></h3>
            <p><?= Yii::t('app', 'Podaj swój login. Na Twoją skrzynkę mailową zostanie wysłane nowe hasło tymczasowe.') ?></p>
                <?php $form = ActiveForm::begin(['id' => 'forget-password', 'enableClientValidation' => false]); ?>

                <?= $form
                    ->field($model, 'username', $fieldOptions1)
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

                <?php //echo $form->field($model, 'rememberMe')->checkbox(); ?>
                <?= Html::submitButton(Yii::t('app', 'Wyślij'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                <?php ActiveForm::end(); ?>
            <p class="m-t"> <small><?= Html::a(Yii::t('app', 'Wróc na stronę logowania'), '/admin/site/login') ?></small> </p>
            <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems © 2017') ?></small> </p>
        </div>
    </div>
