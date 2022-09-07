<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OfferDraft */

$this->title = Yii::t('app', 'Dodaj schemat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schematy ofert'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-draft-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
