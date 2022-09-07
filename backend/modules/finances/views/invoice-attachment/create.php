<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\InvoiceAttachment */

$this->title = Yii::t('app', 'Stwórz załącznik przychodu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załącznik przychodu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-attachment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
