<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\InvoiceSerie */

$this->title = Yii::t('app', 'Dodaj serię');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-serie-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
