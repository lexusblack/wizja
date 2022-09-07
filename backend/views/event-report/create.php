<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventReport */

$this->title = 'Create Event Report';
$this->params['breadcrumbs'][] = ['label' => 'Event Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-report-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
