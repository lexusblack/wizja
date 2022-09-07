<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventUserWorkingTime */

$this->title = Yii::t('app', 'Godziny pracy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenie'), 'url' => ['event/view', 'id'=>$model->event_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-user-working-time-create">

    <?= $this->render('_form', [
        'model' => $model,
        'ajax'=>$ajax
    ]) ?>

</div>
