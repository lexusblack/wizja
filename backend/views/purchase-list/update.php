<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseList */

$this->title = Yii::t('app', 'Edytuj listę zakupową')." ".$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Listy zakupowe'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
