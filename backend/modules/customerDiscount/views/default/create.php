<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */

$this->title = Yii::t('app', 'Utwórz rabat dla klienta');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rabat klienta'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-discount-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'discounts' => $discounts,
        'customers' => $customers,
    ]) ?>

</div>
