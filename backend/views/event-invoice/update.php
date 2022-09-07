<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventInvoice */

$this->title = Yii::t('app', 'Edycja faktury').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenie').': '.$model->event->name, 'url' => ['/event/view', 'id'=>$model->event_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
