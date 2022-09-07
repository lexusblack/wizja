<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventUser */

$this->title = Yii::t('app', 'Edycja użytkoników').': ' . $model->event_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Użytkownicy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->event_id, 'url' => ['view', 'event_id' => $model->event_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-user-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
