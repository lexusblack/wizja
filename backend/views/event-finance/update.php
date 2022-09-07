<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventFinance */

$this->title = Yii::t('app', 'Aktualizuj wydarzenia Finance').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wydarzenie Finances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="event-finance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
