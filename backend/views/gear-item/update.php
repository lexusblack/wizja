<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearItem */

$this->title = Yii::t('app', 'Edycja egzemplarza').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Egzemplarze'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="gear-item-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
