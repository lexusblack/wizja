<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */

$this->title = 'Create Task Schema';
$this->params['breadcrumbs'][] = ['label' => 'Task Schema', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-schema-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
