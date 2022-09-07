<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Service */

$this->title = 'Edytuj: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Usługi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
