<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesForCustomer */

$this->title = Yii::t('app', 'Edytuj przychód od klienta').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przychód od klienta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="incomes-for-customer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
