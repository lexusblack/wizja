<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Meeting */

$this->title = Yii::t('app', 'Edycja spotkania').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Spotkania'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="meeting-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
