<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearAttachment */

$this->title = Yii::t('app', 'Edycja Załącznika').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="gear-attachment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
