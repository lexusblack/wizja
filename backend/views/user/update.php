<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'Edycja użytkownika').': ' . '#' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
        'superusers'=>$superusers,
        'superusers_paid'=>$superusers_paid
    ]) ?>


</div>
