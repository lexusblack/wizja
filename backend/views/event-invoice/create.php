<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventInvoice */

$this->title = Yii::t('app', 'Dodaj fakturę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenie').': '.$model->event->name, 'url' => ['/event/view', 'id'=>$model->event_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-invoice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
