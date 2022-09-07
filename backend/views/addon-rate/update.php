<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AddonRate */

$this->title = Yii::t('app', 'Edycja stawki').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dodatki'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="addon-rate-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
