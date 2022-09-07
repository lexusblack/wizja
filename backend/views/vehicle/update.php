<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Vehicle */

$this->title = Yii::t('app', 'Edycja pojazdu').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pojazdy'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="vehicle-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
