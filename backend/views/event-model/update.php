<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EventModel */

$this->title = Yii::t('app', 'Edytowanie');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Typy wydarzeń'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
