<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VatRate */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stawki VAT'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vat-rate-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
