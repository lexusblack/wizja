<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ChatMessage */

$this->title = 'Create Chat Message';
$this->params['breadcrumbs'][] = ['label' => 'Chat Message', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
