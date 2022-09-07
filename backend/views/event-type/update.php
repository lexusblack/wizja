<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventType */

$this->title = Yii::t('app', 'Edytuj');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rodzaje wydarzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edytuj');
?>
<div class="event-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
