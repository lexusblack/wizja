<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IncomesForCustomer */

$this->title = 'Create Incomes For Customer';
$this->params['breadcrumbs'][] = ['label' => 'Incomes For Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incomes-for-customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
