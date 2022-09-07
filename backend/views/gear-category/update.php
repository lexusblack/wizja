<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GearCategory */

$this->title = Yii::t('app', 'Edycja kategorii').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kategorie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="gear-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
