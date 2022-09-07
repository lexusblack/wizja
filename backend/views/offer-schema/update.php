<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OfferSchema */

$this->title =  $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schematy oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja')." ".$this->title;
?>
<div class="offer-schema-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
