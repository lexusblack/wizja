<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventUserAllowance */

$this->title = Yii::t('app', 'Edycja diety').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Diety'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-user-allowance-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
