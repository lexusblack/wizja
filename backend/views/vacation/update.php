<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Vacation */

$this->title = Yii::t('app', 'Edycja urlopu').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Urlopy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="vacation-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
