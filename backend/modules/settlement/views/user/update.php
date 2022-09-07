<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */

$this->title = Yii::t('app','Aktualizuj rozliczenia użytkownika').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rozliczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="settlement-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
