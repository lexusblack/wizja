<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RolePriceGroup */

$this->title = 'Update Role Price Group: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Role Price Group', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-price-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
