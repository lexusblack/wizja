<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OfferStatut */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Statusy ofert'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-statut-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
