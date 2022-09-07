<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = Yii::t('app', 'Edycja modelu').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Modele'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}



?>
<div class="gear-update">

    <?= $this->render('_form', [
        'model' => $model,
        'rfids' => $rfids
    ]) ?>

</div>
