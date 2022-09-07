<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Paymentmethod */

$this->title = Yii::t('app', 'Edycja') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Metody płatności'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="paymentmethod-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
