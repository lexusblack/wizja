<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CrossRental */

$this->title = Yii::t('app', 'Dodaj sprzęt do Cross Rental Network');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cross Rental'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cross-rental-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
