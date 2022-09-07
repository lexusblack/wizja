<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventMessage */

$this->title = Yii::t('app', 'Dodaj wiadomość do wydarzenia');
$this->params['breadcrumbs'][] = ['label' => 'Wiadomości wydarzenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-message-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
