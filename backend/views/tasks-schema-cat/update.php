<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchemaCat */

$this->title = 'Update Tasks Schema Cat: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tasks Schema Cat', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tasks-schema-cat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
