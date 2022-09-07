<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model \backend\models\SendOfferMail */

$this->title = Yii::t('app', 'Zgłoszenie przyjęte');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p><?=Yii::t('app', 'Twoje zgłoszenie zostało przyjęte. Nadaliśmy Twojej sprawie numer: ').'ERR_'.$model->id?></p>
    <p><?=Yii::t('app', 'Dziękujemy za pomoc w rozwijaniu New Event Management!')?></p>
    <p><?=Yii::t('app', 'Treść błędu:')?></p>
    <p><?=$model->text?></p>
</div>
