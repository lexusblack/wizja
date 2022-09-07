<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VatRate */

$this->title = Yii::t('app', 'Edycja') . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki VAT'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="vat-rate-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
