<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LocationPhoto */

$this->title = Yii::t('app', 'Dodaj zdjęcie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zdjęcie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-photo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
