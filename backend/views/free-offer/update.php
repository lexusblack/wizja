<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FreeOffer */

$this->title = 'Update Free Offer: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Free Offer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="free-offer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
