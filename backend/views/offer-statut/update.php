<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OfferStatut */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Statusy ofert'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="offer-statut-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
