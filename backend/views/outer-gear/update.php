<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */

$this->title = Yii::t('app', 'Edycja').': ' . $model->outerGearModel->name." ".$model->company->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = ['label' => $model->outerGearModel->name." ".$model->company->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');

if (!$model->active) {
    echo Yii::t('app', "Model został usunięty");
    return;
}

?>
<div class="gear-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
