<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearPurchase */

$this->title = 'Update Gear Purchase: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gear Purchase', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gear-purchase-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
