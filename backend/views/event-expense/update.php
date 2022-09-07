<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventExpense */

$this->title = Yii::t('app', 'Edycja kosztu').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenie').': '.$model->event->name, 'url' => ['/event/view', 'id'=>$model->event_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-expense-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
