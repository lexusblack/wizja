<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\InvoiceAttachment */

$this->title = Yii::t('app', 'Aktualizuj załącznik przychodu').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załączniki przychodów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invoice-attachment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
