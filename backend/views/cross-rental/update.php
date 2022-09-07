<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CrossRental */

$this->title = 'Update Cross Rental: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cross Rental', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cross-rental-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
