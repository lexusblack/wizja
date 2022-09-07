<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Chat */

$this->title = Yii::t('app', 'Edytuj Chat').': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Edycja').' '. $model->name;
?>
<div class="chat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
