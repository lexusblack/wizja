<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseListItem */

$this->title = Yii::t('app', 'Dodaj pozycję')." ".$model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-list-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
