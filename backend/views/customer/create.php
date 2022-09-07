<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = Yii::t('app', 'Dodaj klienta');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Klienci'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
