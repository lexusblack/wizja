<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

\common\assets\CustomNumberFormatAsset::register($this);

$this->title = Yii::t('app', 'Dodaj: ').$model->getTypeLabel();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Faktury'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <?= $this->render('_form', [
        'model' => $model,
        'items' => $items,
        'payment'=>$payment,
    ]) ?>

</div>
