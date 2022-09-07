<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OfferExtraCost */

$this->title = 'Create Offer Extra Cost';
$this->params['breadcrumbs'][] = ['label' => 'Offer Extra Cost', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-extra-cost-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
