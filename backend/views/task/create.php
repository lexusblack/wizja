<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = Yii::t('app', 'Dodaj zadanie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zadania'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

    <?= $this->render('_form', [
        'model' => $model,
        'edit_all' => true,
        'ajax'=>false
    ]) ?>

</div>
