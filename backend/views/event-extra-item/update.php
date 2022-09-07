<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventExtraItem */

$this->title = Yii::t('app', 'Edytuj dodatkowy sprzęt: ').$model->name;
$this->params['breadcrumbs'][] = ['label' => $model->event->name, 'url' => ['event/view', 'id'=>$model->event_id, '#'=>'tab-gear']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-extra-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
