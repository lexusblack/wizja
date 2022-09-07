<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TasksSchema */

$this->title = Yii::t('app','Edytuj') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schematy zadań'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasks-schema-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
