<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

\common\assets\CustomNumberFormatAsset::register($this);

$this->title = Yii::t('app', 'Edycja') . $model->fullnumber;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przychody'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullnumber, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="invoice-update">

    <?= $this->render('_form', [
        'model' => $model,
        'items'=>$items,
        'payment'=>$payment,
    ]) ?>

</div>
