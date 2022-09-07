<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Konto nieaktywne';
?>
<div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <img alt="image" class="img-responsive" src="/themes/e4e/img/newem.png">

            </div>
            <h3><?= Yii::t('app', 'Witaj w New Event Management') ?></h3>
            <p><?= Yii::t('app', 'Perfekcyjnym narzędziu do zarządzania twoją firmą eventową!') ?>
            </p>
            <h3 style="margin-top:30px;"><?=Yii::t('app', 'Instancja niekatywna')?></h3>
            <p><?= Yii::t('app', 'Państwa wersja demonstracyjna wygasła. W celu jej przywrócenia prosimy o kontakt z działem obsługi') ?></p>

            <p class="m-t"> <small><?= Html::a(Yii::t('app', 'support@newsystems.pl'), 'mailto:support@newsystems.pl') ?></small> </p>
            <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems © 2017') ?></small> </p>
        </div>
    </div>
