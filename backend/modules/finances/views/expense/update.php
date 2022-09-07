<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Expense */

\common\assets\CustomNumberFormatAsset::register($this);

$this->title = Yii::t('app', 'Edycja {modelClass}: ', [
    'modelClass' => 'Expense',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="expense-update">

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $item,
        'payment'=>$payment,
    ]) ?>

</div>
