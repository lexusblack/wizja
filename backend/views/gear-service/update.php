<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearService */

$this->title = Yii::t('app', 'Edycja serwisu').': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Serwis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="gear-service-update">

    <?= $this->render('_form2', [
        'model' => $model,
    ]) ?>

</div>
