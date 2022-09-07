<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\InvoiceSerie */

$this->title = Yii::t('app', 'Edycja serii: ' . $model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="invoice-serie-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
