<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

$this->title = Yii::t('app', 'Edycja załącznika').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Załącznik lokacji'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="location-attachment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
