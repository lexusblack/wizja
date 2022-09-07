<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceCategory */

$this->title = 'Edytuj: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kategorie usług', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
