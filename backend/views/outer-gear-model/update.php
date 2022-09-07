<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OuterGearModel */

$this->title =  Yii::t('app', 'Edytuj').': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('app', 'Magazyn zewnętrzny'), 'url' => ['/outer-warehouse/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] =  Yii::t('app', 'Edytuj');
?>
<div class="outer-gear-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
