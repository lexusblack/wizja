<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TasksSchemaCat */

$this->title = 'Create Tasks Schema Cat';
$this->params['breadcrumbs'][] = ['label' => 'Tasks Schema Cat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-schema-cat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
