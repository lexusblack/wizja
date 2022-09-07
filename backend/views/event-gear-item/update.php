<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventGearItem */

$this->title = Yii::t('app', 'Edycja sorzętu wydarzenia').': ' . $model->event_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sprzęt wydarzenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->event_id, 'url' => ['view', 'event_id' => $model->event_id, 'gear_item_id' => $model->gear_item_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-gear-item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
