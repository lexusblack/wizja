<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = Yii::t('app', 'Edycja {modelClass}: ', [
    'modelClass' => 'Message',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wiadomości'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'language' => $model->language]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja');
?>
<div class="message-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
