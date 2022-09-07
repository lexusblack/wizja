<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EventUser */

$this->title = $model->event_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <img alt="image" class="img-responsive" src="/themes/e4e/img/newem.png">

            </div>
            <h3><?= Yii::t('app', 'Potwierdzenie udziału') ?></h3>
            <p><?=Yii::t('app', 'Udział w imprezie ').$model->event->name." ".Yii::t('app', 'został potwierdzony.')?></p>


            <p class="m-t"> <small><?= Html::a(Yii::t('app', 'Wróc na stronę logowania'), '/admin/site/login') ?></small> </p>

            <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems © 2017') ?></small> </p>
        </div>
    </div>