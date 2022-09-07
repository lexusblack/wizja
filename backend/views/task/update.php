<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $edit_all boolean */

$this->title = Yii::t('app', 'Edycja zadania').': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zadania'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="task-update">

    <?= $this->render('_form', [
        'model' => $model,
        'edit_all' => $edit_all
    ]) ?>

</div>
