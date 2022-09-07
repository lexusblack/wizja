<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VehicleAttachment */

$this->title = Yii::t('app', 'Edycja załącznika pojazdu').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załączniki pojazdów'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="vehicle-attachment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
