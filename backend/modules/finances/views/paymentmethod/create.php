<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Paymentmethod */

$this->title = Yii::t('app', 'Dodaj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Metody płatności'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paymentmethod-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
