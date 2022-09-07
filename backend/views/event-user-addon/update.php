<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventUserAddon */

$this->title = Yii::t('app', 'Edycja dodatku').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="event-user-addon-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
