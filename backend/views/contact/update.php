<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Contact */

$this->title = Yii::t('app', 'Edycja kontaktu').': ' . $model->getDisplayLabel();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kontakty'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->last_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="contact-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
