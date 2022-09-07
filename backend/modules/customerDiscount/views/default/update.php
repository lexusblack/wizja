<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */
if ($customer){
	$this->title = Yii::t('app', 'Aktualizuj rabat klienta').': ' . $customer->name;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rabaty klienta'), 'url' => ['/customer/view', 'id'=>$customer->id]];
}else{
	$this->title = Yii::t('app', 'Aktualizuj rabat klienta');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rabaty'), 'url' => ['/customer-discount/default/index']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-discount-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form3', [
        'model' => $model
    ]) ?>

</div>
