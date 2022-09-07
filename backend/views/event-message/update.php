<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventMessage */

$this->title = Yii::t('app', 'Edycja wiadomości wydarzenia').': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiadomości wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-message-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
