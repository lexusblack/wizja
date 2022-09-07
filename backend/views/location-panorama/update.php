<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LocationPanorama */

$this->title = Yii::t('app', 'Aktualizuj Panoramę').': ' . ' ' . $model->filename;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Panorama'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="location-panorama-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
