<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OfferExtraCost */

$this->title = 'Update Offer Extra Cost: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Offer Extra Cost', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="offer-extra-cost-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
