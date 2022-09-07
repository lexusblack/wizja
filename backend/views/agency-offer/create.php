<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AgencyOffer */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Oferty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agency-offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
