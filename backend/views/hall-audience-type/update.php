<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\HallAudienceType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rodzaj ustawienia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="hall-audience-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
