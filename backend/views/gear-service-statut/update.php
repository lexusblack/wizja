<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearServiceStatut */

$this->title = 'Update Gear Service Statut: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Gear Service Statut', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gear-service-statut-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
