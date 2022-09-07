<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventAdditionalStatut */

$this->title = 'Update Event Additional Statut: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Event Additional Statut', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="event-additional-statut-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
