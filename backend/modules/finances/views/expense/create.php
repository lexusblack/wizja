<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Expense */

\common\assets\CustomNumberFormatAsset::register($this);

$this->title = Yii::t('app', 'Dodaj wydatek');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create">

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
        'rates' => $rates,
        'payment'=>$payment,
    ]) ?>

</div>
