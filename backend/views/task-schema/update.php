<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */

$this->title = 'Update Task Schema: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Task Schema', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="task-schema-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
