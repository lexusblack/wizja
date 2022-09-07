<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearGroup */

$this->title = Yii::t('app', 'Edycja zestawu sprzętu').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zestaw sprzętu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="gear-group-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
