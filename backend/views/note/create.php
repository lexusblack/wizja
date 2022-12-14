<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Note */

$this->title = 'Create Note';
$this->params['breadcrumbs'][] = ['label' => 'Note', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
