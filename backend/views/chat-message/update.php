<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ChatMessage */

$this->title = 'Update Chat Message: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Chat Message', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="chat-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
