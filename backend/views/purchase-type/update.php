<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseType */

$this->title = Yii::t('app', 'Edytuj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Typy zakupów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
