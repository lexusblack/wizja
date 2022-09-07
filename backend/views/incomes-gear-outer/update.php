<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IncomesGearOuter */

$this->title = Yii::t('app', 'Aktualizuj przychód').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Przychody sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="incomes-gear-outer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
