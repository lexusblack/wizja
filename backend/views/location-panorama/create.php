<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationPanorama */

$this->title = Yii::t('app', 'Dodaj panoramę');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Panorama'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-panorama-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
