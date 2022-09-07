<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app', 'Przypomnij hasło');


?>
<div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <img alt="image" class="img-responsive" src="/themes/e4e/img/newem.png">

            </div>
            <h3><?= Yii::t('app', 'Przywracanie hasła') ?></h3>
            <?php if (!$error){ ?>
            <p><?= Yii::t('app', 'Na Twoją skrzynkę pocztową zostało wysłane tymczasowe hasło do systemu.') ?></p>
            <?php }else{ if ($error==1){ ?>
            <p><?= Yii::t('app', 'Wystąpił błąd podczas wysyłania tymczasowego hasła.') ?></p>

            <?php    }else { ?>
            <p><?= Yii::t('app', 'Konto o podanej nazwie użytkownika nie istnieje.') ?></p>

            <?php }} ?>


            <p class="m-t"> <small><?= Html::a(Yii::t('app', 'Wróc na stronę logowania'), '/admin/site/login') ?></small> </p>

            <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems © 2017') ?></small> </p>
        </div>
    </div>
